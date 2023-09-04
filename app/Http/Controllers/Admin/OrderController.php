<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModelExport;
use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Imports\OrderImport;
use App\Models\Order;
use App\Models\PaymentType;
use App\Models\SystemBranch;
use App\Models\User;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function redirect;
use function view;

class OrderController extends Controller
{
    use BaseControllerTrait;

    public function __construct(Order $model)
    {
        $branchs = SystemBranch::orderBy('id')->get();
        $staffs = User::where('is_admin', 0)->orderby('name', 'ASC')->get();
        $payment = PaymentType::orderby('id')->get();

        $this->initBaseModel($model);
        $this->isSingleImage = false;
        $this->isMultipleImages = false;
        $this->shareBaseModel($model);

        View::share('staffs', $staffs);
        View::share('branchs', $branchs);
        View::share('payment', $payment);

        View::share('title', 'order');
        View::share('page', 'Order');
    }

    public function index(Request $request)
    {
//        $items = $this->model-
        $branchs = SystemBranch::all();
        $query = $this->model->with('user')->orderby('id', 'DESC');
        $staffs = User::where('is_admin', 0)->orderby('name', 'ASC')->get();
        $paginate = 50;

        if(auth()->id() != 1){
            $branchs = SystemBranch::whereIn('id', json_decode(auth()->user()->branch_id))->get();
            $query = $this->model->whereHas('user', function ($query){
                $query->whereIn('branch_id', json_decode(auth()->user()->branch_id));
            })->orderby('id', 'DESC');
            $staffs = User::whereIn('branch_id', json_decode(auth()->user()->branch_id))->get();
        }


        if(isset($_GET['branch_id']) && !empty($_GET['branch_id'])){
            $query = $query->whereHas('user', function ($query){
                $query->where('branch_id', $_GET['branch_id']);
            });
        }

        if(isset($_GET['staff_id']) && !empty($_GET['staff_id'])){
            $query = $query->where('user_id', $_GET['staff_id']);
        }

        if(isset($_GET['payment_id']) && !empty($_GET['payment_id'])){
            $query = $query->where('payment_type_id', $_GET['payment_id']);
        }


        if(isset($_GET['begin']) && !empty($_GET['begin'])){
            $query = $query->whereDate('created_at', '>=', $_GET['begin']);
        }

        if(isset($_GET['end']) && !empty($_GET['end'])){
            $query = $query->whereDate('created_at', '<=', $_GET['end']);
        }

        if(isset($_GET['key']) && !empty($_GET['key'])){
            $query = $query->where('id', 'LIKE', '%'.$_GET['key'].'%')->orWhere('code', 'LIKE', '%'.$_GET['key'].'%');
        }

        if(isset($_GET['show']) && !empty($_GET['show'])){
            $paginate = $request->show;
        }

        $items = $query->paginate($paginate)->appends(request()->query());

        return view('administrator.' . $this->prefixView . '.index', compact('items', 'branchs', 'staffs'));
    }

    public function get(Request $request, $id)
    {
        return $this->model->findById($id);
    }

    public function create()
    {
        $staffs = User::where('is_admin', 0)->where('branch_id', '!=', null)->orderby('name', 'ASC')->get();
        if(auth()->id() != 1){
            $staffs = User::whereIn('branch_id', json_decode(auth()->user()->branch_id))->get();
        }


        return view('administrator.' . $this->prefixView . '.add')->with(compact('staffs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:orders,code',
            'charge' => 'required|numeric',
            'staff' => 'required',
            'tips' => 'required|numeric',
            'payment' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }else{
            $item = $this->model->storeByQuery($request);
            \session()->put('message', 'Create successful');
            return response()->json([
                'status' => true,
                'url' => route('administrator.'.$this->prefixView.'.detail', ['id' => $item->id]),
            ]);
        }
    }

    public function edit($id)
    {
        $staffs = User::whereIn('branch_id', json_decode(auth()->user()->branch_id))->get();
        $item = $this->model->find($id);
        return view('administrator.' . $this->prefixView . '.edit', compact('item', 'id', 'staffs'));
    }

    public function update(Request $request)
    {
        $item = $this->model->updateByQuery($request, $request->id);
        \session()->put('message', 'Update successful');
        return response()->json([
            'status' => true,
            'url' => route('administrator.'.$this->prefixView.'.detail', ['id' => $item->id]),
        ]);
    }

    public function delete(Request $request, $id)
    {
        $item = $this->model->find($id);
        $item->forceDelete();
        \session()->put('message', 'Delete successful');
        return response()->json([
            'status' => true,
            'url' => route('administrator.'.$this->prefixView.'.index')
        ]);
    }

    public function deleteManyByIds(Request $request)
    {
        \session()->put('message', 'Delete successful');
        return $this->model->deleteManyByIds($request, $this->forceDelete);
    }

    public function export(Request $request)
    {
        return Excel::download(new OrderExport($this->model, $request), $this->prefixView . '.xlsx');
    }

    public function import(Request $request){
        Excel::import(new OrderImport(), $request->file);

        return redirect()->route('administrator.'.$this->prefixView.'.index')->with('message', 'Order Imported Successfully');
    }

    public function updateToShipping(Request $request, $id)
    {
        $item = $this->model->find($id);
        $item->updateToShipping();
        $item->refresh();
        return response()->json($item);
    }

    public function detail($id){
        $title = "Detail order";

        $item = $this->model->find($id);

        return view('administrator.'.$this->prefixView.'.detail' , compact('id', 'title', 'item'));
    }

    public function choose(Request $request){
        $branchs = SystemBranch::all();
        $value = '';
        $item = User::find($request->staff_id);
        foreach($branchs as $branch){
            if($item->branch_id == $branch->id){
                $value = $branch->name;
            }
        }
        return response()->json([
            'value' => $value,
        ]);
    }

    public function search(Request $request){
        $items = $this->model->with('user')->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('code', 'LIKE', '%'.$request->key.'%')->latest()->limit(10)->get();

        if(auth()->id() != 1){
            $items = $this->model->with('user')->where('id', 'LIKE', '%'.$request->key.'%')->whereHas('user', function ($query){
                $query->whereIn('branch_id', json_decode(auth()->user()->branch_id));
            })->orWhere('code', 'LIKE', '%'.$request->key.'%')->whereHas('user', function ($query){
                $query->whereIn('branch_id', json_decode(auth()->user()->branch_id));
            })->latest()->limit(10)->get();
        }

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }
}
