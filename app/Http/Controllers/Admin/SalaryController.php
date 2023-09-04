<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Salary;
use App\Http\Controllers\Controller;
use App\Models\SystemBranch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\BaseControllerTrait;
use App\Exports\ModelExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function redirect;
use function view;

class SalaryController extends Controller
{
    use BaseControllerTrait;

    public function __construct(Salary $model)
    {
        $this->initBaseModel($model);
        $this->shareBaseModel($model);

        View::share('title', 'employee salary');
        View::share('page', 'Employee Salary');
    }

    public function index(Request $request)
    {
//        $items = $this->model->searchByQuery($request);
        $name_br = $color_br = $total_order = [];

        $paginate = 50;

        $branches = SystemBranch::all();

        $query = User::with('order')->orderBy('name', 'ASC');

        if(auth()->id() != 1){
            $branches = SystemBranch::whereIn('id', json_decode(Auth::user()->branch_id))->get();
            $query = User::where('is_admin', 0)->whereIn('branch_id', json_decode(Auth::user()->branch_id))->latest();
        }

        if(count($branches) == 0){
            return redirect()->back()->with('message', 'There are no manager of branch');
        }else{
            foreach($branches as $key => $branch){
                $name_br[$key] = $branch->name;
                $color_br[$key] = $branch->color;

                $total_order[$key] = Order::with('user')->whereHas('user', function ($query) use ($branch){
                        $query->where('branch_id', $branch->id);

                    })->sum('service_charge');

                if(isset($_GET['begin']) && !empty($_GET['begin']) && isset($_GET['end']) && !empty($_GET['end'])){
                    $total_order[$key] = Order::with('user')->whereDate('created_at', '>=', $_GET['begin'])
                        ->whereDate('created_at', '<=', $_GET['end'])->whereHas('user', function ($query) use ($branch){
                        $query->where('branch_id', $branch->id);

                    })->sum('service_charge');
                }

            }


            if(isset($_GET['begin']) && !empty($_GET['begin'])){
                $begin = $_GET['begin'];
                $query =  $query->whereHas('order', function ($query) use ($begin){
                   $query->whereDate('created_at', '>=', $begin);
                });
            }

            if(isset($_GET['end']) && !empty($_GET['end'])){
                $end = $_GET['end'];
                $query =  $query->whereHas('order', function ($query) use ($end){
                    $query->whereDate('created_at', '<=', $end);
                });
            }

            if(isset($_GET['key']) && !empty($_GET['key'])){
                $query = $query->where('id', 'LIKE', '%'.$_GET['key'].'%')->orWhere('name', 'LIKE', '%'.$_GET['key'].'%');
            }

            if(isset($_GET['show']) && !empty($_GET['show'])){
                $paginate = $request->show;
            }

            $items = $query->paginate($paginate)->appends(request()->query());

            if(count($branches) < 2){
                $branches = SystemBranch::whereIn('name', $name_br)->first()->id;
                return redirect()->route('administrator.salaries.branch', ['id' => $branches]);
            }

            return view('administrator.' . $this->prefixView . '.index', compact('items', 'name_br', 'color_br', 'branches', 'total_order'));
        }
    }

    public function get(Request $request, $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create()
    {
        return view('administrator.' . $this->prefixView . '.add');
    }

    public function store(Request $request)
    {
        $item = $this->model->storeByQuery($request);
        return redirect()->route('administrator.' . $this->prefixView . '.edit', ["id" => $item->id]);
    }

    public function edit($id)
    {
        $item = $this->model->find($id);
        return view('administrator.' . $this->prefixView . '.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->model->updateByQuery($request, $id);
        return redirect()->route('administrator.' . $this->prefixView . '.edit', ['id' => $id]);
    }

    public function delete(Request $request, $id)
    {
        return $this->model->deleteByQuery($request, $id, $this->forceDelete);
    }

    public function deleteManyByIds(Request $request)
    {
        return $this->model->deleteManyByIds($request, $this->forceDelete);
    }

    public function export(Request $request)
    {
        return Excel::download(new ModelExport($this->model, $request), $this->prefixView . '.xlsx');
    }

    public function search(Request $request){
        $items = User::where('is_admin', '=', 0)->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->where('is_admin', 0)->latest()->limit(10)->get();

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }

    public function branch($id, Request $request){
        $name_br = SystemBranch::find($id)->name;

        $name_user = $total_order = $cash_return = [];
        $paginate = 50;

        $query = User::with('order')->where('is_admin', 0)->where('branch_id', $id)->orderBy('name', 'ASC');

        if(isset($_GET['begin']) && !empty($_GET['begin'])){
            $begin = $_GET['begin'];
            $query =  $query->whereHas('order', function ($query) use ($begin){
                $query->whereDate('created_at', '>=', $begin);
            });
        }

        if(isset($_GET['end']) && !empty($_GET['end'])){
            $end = $_GET['end'];
            $query =  $query->whereHas('order', function ($query) use ($end){
                $query->whereDate('created_at', '<=', $end);
            });
        }

        if(isset($_GET['key']) && !empty($_GET['key'])){
            $query = $query->where('id', 'LIKE', '%'.$_GET['key'].'%')->orWhere('name', 'LIKE', '%'.$_GET['key'].'%');
        }

        if(isset($_GET['show']) && !empty($_GET['show'])){
            $paginate = $request->show;
        }

        $items = $query->paginate($paginate)->appends(request()->query());
        foreach($items as $key => $item){
            $name_user[$key] = $item->name;

            $total_order[$key] = Order::where('user_id', $item->id)->sum('service_charge');
            $total_cash[$key] = Order::where('user_id', $item->id)->where('payment_type_id', 4)->sum(\Illuminate\Support\Facades\DB::raw('service_charge - deposit'));
            $total_tips[$key] = Order::where('user_id', $item->id)->sum('tips');
            $cash_return[$key] = $total_cash[$key] - $total_tips[$key];

            if(isset($_GET['begin']) && !empty($_GET['begin']) && isset($_GET['end']) && !empty($_GET['end'])){
                $total_order[$key] = Order::where('user_id', $item->id)->whereDate('created_at', '>=', $_GET['begin'])
                    ->whereDate('created_at', '<=', $_GET['end'])->sum('service_charge');
                $total_cash[$key] = Order::where('user_id', $item->id)->whereDate('created_at', '>=', $_GET['begin'])
                    ->whereDate('created_at', '<=', $_GET['end'])->where('payment_type_id', 4)->sum(\Illuminate\Support\Facades\DB::raw('service_charge - deposit'));
                $total_tips[$key] = Order::where('user_id', $item->id)->whereDate('created_at', '>=', $_GET['begin'])
                    ->whereDate('created_at', '<=', $_GET['end'])->sum('tips');
                $cash_return[$key] = $total_cash[$key] - $total_tips[$key];
            }
        }

        return view('administrator.' . $this->prefixView . '.branch', compact('items', 'id', 'name_br', 'name_user', 'total_order', 'cash_return'));
    }

    public function detail(Request $request, $id){
        $paginate = 50;

        $name = User::find($id)->name;
        $query = Order::where('user_id', $id)->latest();

        if(isset($_GET['begin']) && !empty($_GET['begin'])){
            $query = $query->whereDate('created_at', '>=', $_GET['begin']);
        }

        if(isset($_GET['end']) && !empty($_GET['end'])){
            $query = $query->whereDate('created_at', '<=',  $_GET['end']);
        }

        if(isset($_GET['show']) && !empty($_GET['show'])){
            $paginate = $request->show;
        }

        $items = $query->paginate($paginate)->appends(request()->query());

        return view('administrator.' . $this->prefixView . '.detail', ['id' => $id], compact('items', 'id', 'name'));
    }
}
