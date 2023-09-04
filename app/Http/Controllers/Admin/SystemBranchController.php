<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModelExport;
use App\Http\Controllers\Controller;
use App\Models\SystemBranch;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use function redirect;
use function view;

class SystemBranchController extends Controller
{
    use BaseControllerTrait;

    public function __construct(SystemBranch $model)
    {
        $this->initBaseModel($model);
        $this->isSingleImage = true;
        $this->isMultipleImages = false;
        $this->shareBaseModel($model);

        View::share('title', 'branches');
        View::share('page', 'Branches');
    }

    public function index(Request $request)
    {
        $paginate = 50;

        $query = $this->model->orderby('id', 'DESC');

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

        return view('administrator.' . $this->prefixView . '.index', compact('items'));
    }

    public function get(Request $request, $id)
    {
        return $this->model->findById($id);
    }

    public function create()
    {
        return view('administrator.' . $this->prefixView . '.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:system_branches,name',
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
        $item = $this->model->find($id);
        return view('administrator.' . $this->prefixView . '.edit', compact('item', 'id'));
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

    public function detail($id){
        $title = "Detail branch";
        $item = $this->model->find($id);

        return view('administrator.'.$this->prefixView.'.detail' , compact('id', 'title', 'item'));
    }

    public function search(Request $request){
        $items = $this->model->where('id', 'LIKE', '%'.$request->key.'%')->orWhere('name', 'LIKE', '%'.$request->key.'%')->orderby('id')->limit(10)->get();

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }
}
