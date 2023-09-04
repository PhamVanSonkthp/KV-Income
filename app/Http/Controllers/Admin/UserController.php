<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModelExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SystemBranch;
use App\Models\User;
use App\Models\UserType;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function view;

class UserController extends Controller
{
    use BaseControllerTrait;

    public function __construct(User $model, Role $role)
    {
        $this->initBaseModel($model);
        $this->isSingleImage = true;
        $this->isMultipleImages = false;
        $this->shareBaseModel($model);
        $this->role = $role;
        $userTypes = UserType::all();
        View::share('userTypes', $userTypes);

        View::share('title', 'admin user');
        View::share('page', 'Admin User');
    }

    public function index(Request $request)
    {
        $branchs = SystemBranch::orderBy('id')->get();
        $roles = $this->role->where('id', '!=', 1)->get();

//        $items = $this->model->searchByQuery($request, ['is_admin' => 1]);
        $paginate = 50;

        $query = $this->model->where('is_admin', 1)->latest();


        if(isset($_GET['branch_id']) && !empty($_GET['branch_id'])){
            $query = $query->where('branch_id', 'LIKE', '%'.$_GET['branch_id'].'%');
        }

        if(isset($_GET['role_id']) && !empty($_GET['role_id'])){
            $query = $query->where('role_id', 'LIKE', '%'.$_GET['role_id'].'%');
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

        return view('administrator.' . $this->prefixView . '.index', compact('items', 'branchs', 'roles'));
    }

    public function get(Request $request, $id)
    {
        return $this->model->findById($id);
    }

    public function create()
    {
        $branchs = SystemBranch::orderBy('id')->get();
        $roles = $this->role->where('id', '!=', 1)->get();
        return view('administrator.' . $this->prefixView . '.add', compact('roles', 'branchs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'name' => 'required',
           'password' => 'required|min:6',
           'branch' => 'required',
           'admin_group' => 'required',
        ],[
            'name.required' => 'Name is not empty',
            'password.required' => 'Password is not empty',
            'password.min' => 'Password is must be at least 6 characters',
            'branch.required' => 'Please choose at least 1 branch',
            'admin_group.required' => 'Please choose at least 1 admin group',
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
        $page = $title = 'Update admin user';
        $branchs = SystemBranch::orderBy('id')->get();
        $roles = $this->role->where('id', '!=', 1)->get();
        $item = $this->model->findById($id);
        $arr_branch = json_decode($item->branch_id);
        $arr_role = json_decode($item->role_id);
        return view('administrator.' . $this->prefixView . '.edit', compact('item', 'id', 'branchs', 'roles', 'page', 'title', 'arr_role', 'arr_branch'));
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
//        return $this->model->deleteByQuery($request, $id, $this->forceDelete);
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
        return Excel::download(new ModelExport($this->model, $request), $this->prefixView . '.xlsx');
    }

    public function generate(){
        $pass = substr(md5(microtime()), rand(0,26), 6);
        return response()->json([
            'password' => $pass,
        ]);
    }

    public function detail($id){
        $title = "Detail admin user";
        $item = $this->model->find($id);
        $branchs = SystemBranch::orderBy('id')->get();
        $arr_branch = json_decode($item->branch_id);
        $roles = $this->role->where('id', '!=', 1)->get();
        $arr_role = json_decode($item->role_id);

        return view('administrator.'.$this->prefixView.'.detail' , compact('id', 'title', 'item', 'branchs', 'arr_branch', 'branchs', 'roles', 'arr_role'));
    }

    public function search(Request $request){
        $items = User::where('is_admin', '=', 1)->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->where('is_admin', 1)->latest()->limit(10)->get();

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }
}
