<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeeExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\Role;
use App\Models\SystemBranch;
use App\Models\User;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function view;

class EmployeeController extends Controller
{
    use BaseControllerTrait;

    public function __construct(User $model)
    {
        $roles = Role::where('id', '!=', 1)->get();
        $branchs = SystemBranch::orderBy('id')->get();
        $this->initBaseModel($model);
        $this->isSingleImage = true;
        $this->isMultipleImages = false;
        $this->prefixView = 'employees';
        $this->shareBaseModel($model);
        View::share('roles', $roles);
        View::share('branchs', $branchs);

        View::share('title', 'staff');
        View::share('page', 'Staff');
    }

    public function index(Request $request)
    {
//        $items = $this->model->searchByQuery($request, ['is_admin' => 0]);
        $branchs = SystemBranch::all();
        $query = $this->model->where('is_admin', 0)->orderby('id', 'DESC');
        if(auth()->id() != 1){
            $branchs = SystemBranch::whereIn('id', json_decode(auth()->user()->branch_id))->get();
            $query = $this->model->where('is_admin', 0)->whereIn('branch_id', json_decode(auth()->user()->branch_id))->orderby('id', 'DESC');
        }

        $paginate = 50;

        if(isset($_GET['branch_id']) && !empty($_GET['branch_id'])){
            $query = $query->where('branch_id', 'LIKE', '%'.$_GET['branch_id'].'%');
        }

        if(isset($_GET['begin']) && !empty($_GET['begin'])){
            $query = $query->whereDate('created_at', '>=', $_GET['begin']);
        }

        if(isset($_GET['end']) && !empty($_GET['end'])){
            $query = $query->whereDate('created_at', '<=', $_GET['end']);
        }

        if(isset($_GET['key']) && !empty($_GET['key'])){
            $query = $query->where('id', 'LIKE', '%'.$_GET['key'].'%')->orWhere('name', 'LIKE', '%'.$_GET['key'].'%');
        }

        if(isset($_GET['show']) && !empty($_GET['show'])){
            $paginate = $request->show;
        }

        $items = $query->paginate($paginate)->appends(request()->query());

        return view('administrator.'.$this->prefixView.'.index', compact('items', 'branchs'));
    }

    public function get(Request $request, $id)
    {
        return $this->model->findById($id);
    }

    public function create()
    {
        $branchs = SystemBranch::all();
        if(auth()->id() != 1){
            $branchs = SystemBranch::whereIn('id', json_decode(auth()->user()->branch_id))->get();
        }

        return view('administrator.'.$this->prefixView.'.add')->with(compact('branchs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:users,code',
            'name' => 'required',
            'password' => 'required|min:6',
            'phone' => 'required|unique:users,phone',
            'start' => 'required',
            'birthday' => 'required',
            'address' => 'required',
            'branch' => 'required',
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
        $branchs = SystemBranch::all();
        if(auth()->id() != 1){
            $branchs = SystemBranch::whereIn('id', json_decode(auth()->user()->branch_id))->get();
        }
        $item = $this->model->findById($id);
        $rolesOfUser = $item->roles;
        return view('administrator.'.$this->prefixView.'.edit', compact('item','rolesOfUser', 'id', 'branchs'));
    }

    public function update(Request $request)
    {
        $oldId = User::find($request->id);

        if($oldId->id != $request->id_new){
            $checkID = User::find($request->id_new);
            if(isset($checkID) && !empty($checkID)){
                return response()->json([
                    'status' => false,
                    'message' => 'ID đã được sử dụng vui lòng sử dụng ID khác',
                ]);
            }
        }

        $item = $this->model->updateByQuery($request, $request->id);
        \session()->put('message', 'Update successful');
        return response()->json([
            'status' => true,
            'url' => route('administrator.'.$this->prefixView.'.detail', ['id' => $item->id]),
        ]);
    }

    public function delete($id)
    {
//        return $this->deleteModelTrait($id, $this->model);
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
        return $this->model->deleteManyByIds($request, $this->forceDelete);
    }

    public function export(Request $request)
    {
        return Excel::download(new UsersExport(), $this->prefixView . '.xlsx');
    }

    public function import(Request $request){
        Excel::import(new UserImport(), $request->file);

        return redirect()->route('administrator.'.$this->prefixView.'.index')->with('message', 'Staff Imported Successfully');
    }

    public function detail($id){
        $title = "Detail staff";
        $message = '';
        if(Session::get('message')){
            $message = Session::get('message');
        }
        $item = $this->model->find($id);
        $arr_branch = json_decode($item->branch_id);

        return view('administrator.'.$this->prefixView.'.detail' , compact('id', 'title', 'message', 'item', 'arr_branch'));
    }

    public function search(Request $request){

        $items = User::where('is_admin', '=', 0)->where('is_admin', 0)->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->where('is_admin', 0)->latest()->limit(10)->get();

        if(auth()->id() != 1){
            $items = User::where('is_admin', '=', 0)->where('is_admin', 0)->whereIn('branch_id', json_decode(auth()->user()->branch_id))->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->where('is_admin', 0)->whereIn('branch_id', json_decode(auth()->user()->branch_id))->latest()->limit(10)->get();
        }

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }
}
