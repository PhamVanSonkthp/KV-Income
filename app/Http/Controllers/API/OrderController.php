<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryNew;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\Formatter;
use App\Models\Helper;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ParticipantChat;
use App\Models\Product;
use App\Models\RestfulAPI;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserProductRecent;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Models\VoucherUsed;
use App\Traits\StorageImageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    private $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function list(Request $request)
    {
        $queries = ['user_id' => auth()->id()];
        $results = RestfulAPI::response($this->model, $request, $queries, null,null,true);

        if (!empty($request->search_query)){
            $results = $results->orWhere("code", 'LIKE', "%{$request->search_query}%");
        }

        $results = $results->latest()->paginate(Formatter::getLimitRequest($request->limit))->appends(request()->query());

        return response()->json($results);
    }

    public function store(Request $request)
    {

        $request->validate([
            'service_charge' => 'numeric|nullable',
            'tips' => 'numeric|nullable',
            'payment_type_id' => 'required|numeric',
        ]);

        $tips = 0;

        if (!empty($request->tips)){

            $setting = Setting::first();
            $tips = $request->tips * optional($setting)->percent_tips;
        }

        $deposit = $request->deposit ?? 0;

        $item = Order::create([
            'service_charge' => ($request->service_charge ?? 0) + $deposit,
            'tips' => $tips ?? 0,
            'payment_type_id' => $request->payment_type_id,
            'deposit' => $deposit,
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

        $item->update([
            'code' => $item->id
        ]);

        $files = $request->file('images');

        if (!empty($files) && is_array($files)){

            foreach ($files as $file){

                $itemImage = Image::create([
                    'uuid' => Helper::randomString(),
                    'table' => $this->model->getTableName(),
                    'image_path' => "waiting",
                    'image_name' => "waiting",
                    'relate_id' => $item->id,
                ]);

                $dataUploadFeatureImage = StorageImageTrait::storageTraitUpload($request, $file,'multiple', $itemImage->id);

                if (empty($dataUploadFeatureImage) || empty($dataUploadFeatureImage['file_path'])){
                    $itemImage->delete();
                }else{
                    $dataUpdate = [
                        'image_path' => $dataUploadFeatureImage['file_path'],
                        'image_name' => $dataUploadFeatureImage['file_name'],
                    ];

                    $itemImage->update($dataUpdate);
                }

            }
        }

        $item->refresh();

        return response()->json($item);
    }

    public function storeNotAuth(Request $request)
    {
        $request->validate([
            'quantities' => 'required|array|min:1',
            "quantities.*" => "required|numeric|min:1",
            'product_ids' => 'required|array|min:1',
            "product_ids.*" => "required|numeric|min:1",
            "name" => "required",
            "phone" => "required",
            "address" => "required",
        ]);

        if (count($request->quantities) != count($request->product_ids)) {
            return Helper::errorAPI(99, [], "2 mảng phải bằng nhau");
        }

        DB::beginTransaction();

        $item = $this->model->create([
            'user_id' => 0,
        ]);

        foreach ($request->product_ids as $index => $product_id) {

            $product = Product::find($product_id);

            if (empty($product)) continue;

            $orderProduct = OrderProduct::create([
                'order_id' => $item->id,
                'product_id' => $product->id,
                'quantity' => $request->quantities[$index],
                'price' => $product->priceByUser(),
                'name' => $product->name,
                'product_image' => $product->avatar(),
            ]);

            $orderProduct->fill(['order_size' => $product->size, 'order_color' => $product->color])->save();
        }

        DB::commit();

        $html = "<p>Thông tin khách hàng</p>";
        $html .= "<div>Họ và tên: " . $request->name . "</div>";
        $html .= "<div>Số điện thoại: " . $request->phone . "</div>";
        $html .= "<div>Địa chỉ: " . $request->address . "</div>";

        $html .= "<p>Danh sách đơn hàng</p>";

        $table = "<table style='width: 100%;border: solid;'>";
        $table .= "<thead><tr><th style='border: 1px solid;'>Sản phẩm</th><th style='border: 1px solid;'>Số lượng</th></tr></thead>";
        $table .= "<tbody>";
        foreach ($item->products as $productItem) {

            $productAttributeHtml = "";

            if (!empty($productItem->order_size) || !empty($productItem->order_color)) {
                $productAttributeHtml = '<div>Phân loại:<strong>' . Formatter::getShortDescriptionAttribute($productItem->order_size) . '</strong>,<strong>' . Formatter::getShortDescriptionAttribute($productItem->order_color) . '</strong></div>';
            }

            if (!(strpos($productItem->product_image, 'http') !== false)) {
                $productItem->product_image = env('APP_URL') . $productItem->product_image;
            }

            $productsHtml = '<div style="margin-top: 5px;display: flex;gap: 10px;"><div style="flex: 1;"><img style="height: 40px;" src="' . $productItem->product_image . '"></div><div style="flex: 5;"><div>' . $productItem->name . '</div>' . $productAttributeHtml . '</div></div>';

            $table .= "<tr><td>" . $productsHtml . "</td><td style='text-align: center;'>{$productItem->quantity}</td></tr>";
        }

        $table .= "</tbody>";
        $table .= "</table>";

        $html .= $table;
        $html .= "<div style='margin-top: 10px;'>Hãy truy cập <a href='" . route('administrator.orders.index') . "'>" . route('administrator.orders.index') . "</a> để kiểm tra đơn hàng!</div>";

        Helper::sendEmailToShop('Đơn hàng mới!', $html);

        $item->refresh();

        return response()->json($item);
    }

}
