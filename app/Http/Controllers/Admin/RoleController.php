<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleAddRequest;
use App\Http\Requests\RoleEditRequest;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function redirect;
use function view;

class RoleController extends Controller
{
    use BaseControllerTrait;

    private $model;
    private $premission;
    private $roles;

    public function __construct(Role $model, Permission $premission)
    {
        $this->model = $model;
        $this->models = Role::all();
        $this->premission = $premission;

        $this->initBaseModel($model);
        $this->isSingleImage = false;
        $this->isMultipleImages = false;
        $this->shareBaseModel($model);

        View::share('title', 'admin group');
        View::share('page', 'Admin Group');
        View::share('model', 'Role');
    }

    public function index(Request $request){
//        $items = $this->model->searchByQuery($request);
        $paginate = 50;

        $query = Role::where('id', '!=', 1)->latest();

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

        $premissionsParent = $this->premission->where('parent_id' , 0)->orderBy('id')->get();

        return view('administrator.'.$this->prefixView.'.index', compact('items','premissionsParent'));
    }


    public function get(Request $request, $id)
    {
        return $this->model->findById($id);
    }

    public function detail($id){
        $title = "Detail Admin Group";
        $premissionsParent = $this->premission->where('parent_id' , 0)->orderBy('id')->get();
        $role = $this->model->find($id);
        $permissionsChecked = $role->permissions;
        foreach($permissionsChecked as $key => $value){
            $permissionName[$key] = $value->parent_id;
        }
        return view('administrator.'.$this->prefixView.'.detail' , compact('premissionsParent'  , 'role' , 'permissionsChecked', 'permissionName', 'title', 'id'));
    }

    public function create(){
        $title = 'create admin group';
        $page = 'Create Admin Group';
        $premissionsParent = $this->premission->where('parent_id' , 0)->orderBy('id')->get();
        return view('administrator.'.$this->prefixView.'.add' , compact('premissionsParent', 'title', 'page'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'permission_id' => 'required',
        ],[
            'name.required' => 'Name is not empty',
            'permission_id.required' => 'You do not choose a role',
        ]);
        if($validator->fails()){
           return response()->json([
               'status' => false,
               'message' => $validator->errors()->first(),
           ]);
        }else{
            $checkName = Role::where('name', $request->name)->first();
            if(empty($checkName)){
                $role = $this->model->create([
                    'name' => $request->name,
                    'display_name' => $request->name,
                    'user_id' => Auth::id(),
                ]);
                $role->permissions()->attach($request->permission_id);
                \session()->put('message', 'Create successful');
                return response()->json([
                    'status' => true,
                    'url' => route('administrator.'.$this->prefixView.'.detail', ['id' => $role->id]),
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Name is had already',
                ]);
            }

        }
    }

    public function edit($id){
        $title = 'Update Admin Group';
        $premissionsParent = $this->premission->where('parent_id' , 0)->orderBy('id')->get();
        $role = $this->model->find($id);
        $permissionsChecked = $role->permissions;
        foreach($permissionsChecked as $key => $value){
            $permissionName[$key] = $value->parent_id;
        }
        return view('administrator.'.$this->prefixView.'.edit' , compact('premissionsParent'  , 'role' , 'permissionsChecked', 'permissionName', 'id', 'title'));
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'permission_id' => 'required',
        ],[
            'permission_id.required' => 'You do not choose a role',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }else{
            $item = $this->model->find($request->id);
            if($item->name != $request->name){
                $checkName = Role::where('name', $request->name)->first();

                if(isset($checkName) && !empty($checkName)){
                    return response()->json([
                        'status' => false,
                        'message' => 'Name is had already',
                    ]);
                }
            }


            $item->update([
                'name' => $request->name,
                'display_name' => $request->name,
                'user_id' => Auth::id(),
            ]);


            $item->permissions()->sync($request->permission_id);
            \session()->put('message', 'Update successful');
            return response()->json([
                'status' => true,
                'url' => route('administrator.'.$this->prefixView.'.detail', ['id' => $item->id])
            ]);
        }
    }

    public function delete($id)
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
        return Excel::download(new ModelExport($this->model, $request), $this->prefixView . '.xlsx');
    }

    public function search(Request $request){
        $items = Role::where('id', '!=', 1)->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->where('id', '!=', 1)->latest()->limit(10)->get();

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }

}
