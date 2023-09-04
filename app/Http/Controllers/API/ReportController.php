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
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\RestfulAPI;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserProductRecent;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Models\VoucherUsed;
use App\Traits\StorageImageTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    private $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function get(Request $request)
    {
        $request->validate([
//            'start' => 'required|date_format:Y-m-d H:i:s',
//            'end' => 'required|date_format:Y-m-d H:i:s',
            'type' => 'required|numeric|min:0|max:3',
        ]);

        $queries = ['user_id' => auth()->id()];
        $results = RestfulAPI::response($this->model, $request, $queries, null, null, true);
        $totalServiceChargeNow = $results->sum('service_charge');

        $queries = ['user_id' => auth()->id()];
        $results = RestfulAPI::response($this->model, $request, $queries, null, null, true);
        $totalTipsNow = $results->sum('tips');

        $queries = ['user_id' => auth()->id()];
        $results = RestfulAPI::response($this->model, $request, $queries, null, null, true);
        $totalBillNow = $results->count();

        $queries = ['user_id' => auth()->id(), 'payment_type_id' => 4];
        $results = RestfulAPI::response($this->model, $request, $queries, null, null, true);
        $totalCashNow = $results->sum(DB::raw('service_charge - deposit'));

        $cashReturnNow = $totalCashNow - $totalTipsNow;

        $dateBefore = Carbon::parse($request->start);
        $dateAfter = Carbon::parse($request->end);

        switch ($request->type) {
            case 1:
                $dateBefore = $dateBefore->subDay();
                $dateAfter = $dateAfter->subDay();
                break;
            case 2:
                $dateBefore = $dateBefore->addWeek();
                $dateAfter = $dateAfter->addWeek();
                break;
            case 3:
                $dateBefore = $dateBefore->addMonth();
                $dateAfter = $dateAfter->addMonth();
                break;
        }

        $queries = ['user_id' => auth()->id(), 'start' => $dateBefore, 'end' => $dateAfter];
        $results = RestfulAPI::response($this->model, null, $queries, null, null, true);
        $totalServiceChargeBefore = $results->sum('service_charge');

        $queries = ['user_id' => auth()->id(), 'start' => $dateBefore, 'end' => $dateAfter];
        $results = RestfulAPI::response($this->model, null, $queries, null, null, true);
        $totalTipsBefore = $results->sum('tips');

        $queries = ['user_id' => auth()->id(), 'start' => $dateBefore, 'end' => $dateAfter];
        $results = RestfulAPI::response($this->model, null, $queries, null, null, true);
        $totalBillBefore = $results->count();

        $queries = ['user_id' => auth()->id(), 'payment_type_id' => 4, 'start' => $dateBefore, 'end' => $dateAfter];
        $results = RestfulAPI::response($this->model, null, $queries, null, null, true);
        $totalCashBefore = $results->sum(DB::raw('service_charge - deposit'));

        $cashReturnBefore = $totalCashBefore - $totalTipsBefore;

        return response()->json([
            'before' => [
                'total_service_charge_now' => (float)number_format((float)$totalServiceChargeBefore, 2, '.', ''),
                'total_tips_now' => (float)number_format((float)$totalTipsBefore, 2, '.', ''),
                'total_bill_now' => (int) $totalBillBefore,
                'total_cash_now' => (float)number_format((float)$totalCashBefore, 2, '.', ''),
                'cash_return_now' => $cashReturnBefore,
            ],
            'now' => [
                'total_service_charge_now' => (float)number_format((float)$totalServiceChargeNow, 2, '.', ''),
                'total_tips_now' => (float)number_format((float)$totalTipsNow, 2, '.', ''),
                'total_bill_now' => (int) $totalBillNow,
                'total_cash_now' => (float)number_format((float)$totalCashNow, 2, '.', ''),
                'cash_return_now' => (float)number_format((float)$cashReturnNow, 2, '.', ''),
            ]
        ]);
    }

}
