<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ModelExport;
use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Role;
use App\Models\User;
use App\Traits\BaseControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use function view;

class HistoryDataController extends Controller
{
    use BaseControllerTrait;

    public function __construct(Audit $model)
    {
        $users = User::all();
        $this->initBaseModel($model);
        $this->shareBaseModel($model);

        View::share('users', $users);
        View::share('title', 'admin activity logs');
        View::share('page', 'Admin Activity Logs');
    }

    public function index(Request $request){
        $paginate = 50;

        $query = $this->model->where('user_id', '!=', 1)->latest();

        if(isset($_GET['begin']) && !empty($_GET['begin'])){
            $query = $query->whereDate('created_at', '>=', $_GET['begin']);
        }

        if(isset($_GET['end']) && !empty($_GET['end'])){
            $query = $query->whereDate('created_at', '<=', $_GET['end']);
        }

        if(isset($_GET['admin_user']) && !empty($_GET['admin_user'])){
            $query = $query->where('user_id', $_GET['admin_user']);
        }

        if(isset($_GET['show']) && !empty($_GET['show'])){
            $paginate = $request->show;
        }


        $items = $query->paginate($paginate)->appends(request()->query());
//        $items = $this->model->searchByQuery($request);
        return view('administrator.'.$this->prefixView.'.index', compact('items'));
    }

    public function export(Request $request)
    {
        return Excel::download(new ModelExport($this->model, $request), $this->prefixView . '.xlsx');
    }

    public function detail($id){
        $title = $page = 'Detail admin activity logs';

        $item = Audit::find($id);

        return view('administrator.'.$this->prefixView.'.detail', compact('item', 'title', 'page', 'id'));
    }

    public function search(Request $request){
        $items = User::where('is_admin', 1)->where('name', 'LIKE', '%'.$request->key.'%')->latest()->limit(10)->get();

        return response()->json([
            'html' => view('administrator.components.search_advance')->with(compact('items'))->render()
        ]);
    }
}
