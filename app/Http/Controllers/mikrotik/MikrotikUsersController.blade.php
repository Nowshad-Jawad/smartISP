<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\Mikrotik;
use App\Models\PppUser;
use App\Observers\ActionObserver;
use App\Services\ConnectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MikroTikUsersController extends Controller
{
    public $user;
    public $actionObserver;
    public function __construct()
    {
        $this->actionObserver = new ActionObserver;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('api')->user();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($this->user->hasPermissionTo('mikrotik_users', 'api')) {
                $users = PppUser::select(['id', 'name', 'service', 'password', 'profile', 'remoteAddress', 'mikrotik_id', 'status'])->latest()->with('mikrotik')->where('user_status', 1)->paginate($request->item ?? 10);
                $total_active = PppUser::where('status', 1)->where('user_status', 1)->count();
                $total_inactive = PppUser::where('status', 0)->where('user_status', 1)->count();
                $data = [
                    'data' => $users,
                    'active' => $total_active,
                    'inactive' => $total_inactive,
                ];
                return success_message("all mikrotik users", $data);
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            if ($this->user->hasPermissionTo('Change_mikrotik_users_status', 'api')) {

                PppUser::where('id', $id)->update(['status' => DB::raw("IF(status = 1, 0 ,1)")]);
                return success_message("Status change Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data = PppUser::where('id', $id)->first();
            return success_message("mikrotik users", $data);
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mikrotikusers()
    {
        try {
            $mikrotik = Mikrotik::select([
                'id',
                'identity',
                "host",
                'username',
                'password',
                'port',
            ])->get();
            $data = [];
            foreach ($mikrotik as $mikrotik) {
                $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                $query_data = $connection->addUserToMikrotik($request);
            }

            //   ConnectionService::
            return success_message("mikrotik users", $data);
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if ($this->user->hasPermissionTo('Delete_mikrotik_users', 'api')) {

                $data = PppUser::find($id);
                $data->delete();
                $this->actionObserver->createAction(Auth::user()->name . " Delete a MikroTik User id is " . $data->id, Auth::user()->name, 'success');
                return success_message("Delete Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
}
