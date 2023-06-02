<?php

namespace App\Http\Controllers\Api;

use App\Models\IPpool;
use App\Models\Package;
use App\Models\PppUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Models\Mikrotik;
use App\Http\Controllers\Controller;
use App\Models\InterfaceAddress;
use App\Models\InterfaceEthernet;
use App\Models\Intrface;
use App\Models\IpArp;
use App\Models\Queue;
use App\Models\QueueType;
use Illuminate\Support\Facades\Validator;
use App\Services\ConnectionService;
use App\Services\User\UserConnectionService;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnSelf;

class MikrotikController extends Controller
{
    private $user_connection_service;
    private $interfaceController;
    public $query_data = '';
    public $user;

    public function __construct(
        InterfaceController $interfaceController,
        UserConnectionService $user_connection_service,
    ) {
        $this->interfaceController = $interfaceController;
        $this->user_connection_service = $user_connection_service;

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
            if ($this->user->hasPermissionTo('Miktrotik', 'api')) {

                $item = $request->item ?? 10;
                $mikrotik = Mikrotik::latest()
                    ->paginate($item);
                return success_message("All Mikrotik", $mikrotik);
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allmikrotik(Request $request)
    {
        try {
            $mikrotik = Mikrotik::get();
            return success_message("All Mikrotik", $mikrotik);
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
    // setMikrotikConnection
    public function setMikrotikConnection(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id'         => 'required',
            'mikrotik'        => 'required',
            'connection_type' => 'required',
        ]);

        info($request->mikrotik);
        if ($validator->fails()) {
            return error_message('Validation Error', $validator->errors()->all(), 422);
        } else {

            $user = [
                'id' => "$request->user_id"
            ];

            // return $request->connection_type;
            try {
                DB::beginTransaction();
                $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
                if (empty($mikrotik)) return error_message('Mikrotik not found');
                $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                if ($request->connection_type == 'si_m_b') {
                    $validator = Validator::make($request->all(), [
                        'target_address' => $request->connection_type === 'si_m_b' ? 'required' : 'nullable',
                        'destination' => $request->connection_type === 'si_m_b' ? 'required' : 'nullable',
                        'mac_address' => $request->connection_type === 'si_m_b' ? 'required' : 'nullable',
                    ]);
                    if ($validator->fails())  return error_message('Validation Error', $validator->errors()->all(), 422);
                    $request['address'] = $request->target_address;
                    $request['interface'] = $request->destination;
                    $corn_query = $connection->ipArpAdd($request);
                    if (gettype($corn_query) == 'string') return error_message($corn_query);
                    $this->interfaceController->createdata($corn_query, $mikrotik->id);
                } elseif ($request->connection_type == 'si_private_queue' || $request->connection_type == 'si_public_queue') {
                    $validator = Validator::make($request->all(), [
                        'target_address' => $request->connection_type === 'si_m_b' ? 'required' : 'nullable',
                    ]);
                    if ($validator->fails())  return error_message('Validation Error', $validator->errors()->all(), 422);
                    $query_data = $connection->addQueueTargetAddress($request);
                }
                $query_data = '';
                try {
                    $query_data = $connection->addUserToMikrotik($request);
                    if (gettype($query_data) == 'string') return error_message($query_data);
                    $this->query_data = $query_data[0]['expire_date'];
                    $this->user_connection_service->create($request, $user, $this->query_data);
                    DB::commit();
                    return success_message("User Create Successfully with Mikrotik");
                } catch (Exception $exception) {
                    DB::rollBack();
                    info($exception->getMessage());
                    return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
                }
                $this->user_connection_service->create($request, $user, $this->query_data);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'User Updated successfully.'
                ]);
            } catch (Exception $exception) {
                DB::rollBack();
                info(json_encode($exception->getMessage()));
                return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
            }
        }
    }

    /**
     * store the specified resource from storage.
     *
     * @param int $request
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'username' => 'required|max:255',
            'password' => 'required',
            'host'     => 'required|ipv4',
            'status'   => '',
            'address'  => 'required',
            'port'     => 'required',
            'sitename' => 'required',
        ]);

        if ($validator->fails()) {
            return error_message('Validation Error', $validator->errors()->all(), 422);
        } else {
            if ($this->user->hasPermissionTo('Add Mikrotik', 'api')) {
                //create client
                new ConnectionService($request->get('host'), $request->get('username'), $request->get('password'), $request->get('port'));
                try {
                    $mikrotik = new Mikrotik();
                    $mikrotik->identity = $request->identity;
                    $mikrotik->host = $request->host;
                    $mikrotik->username = $request->username;
                    $mikrotik->password = $request->password;
                    $mikrotik->port = $request->port;
                    $mikrotik->status = TRUE;
                    $mikrotik->address = $request->address;
                    $mikrotik->sitename = $request->sitename;
                    $mikrotik->user_id = 2;
                    $mikrotik->save();
                    return success_message("Data Created Successfully", $mikrotik);
                } catch (Exception $e) {
                    return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
                }
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mikrotik = Mikrotik::find($id);
        return response()->json([
            'mikrotik' => $mikrotik
        ]);
    }
    /**
     * Update the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'username' => 'required|max:255',
            'password' => 'required',
            'status'   => '',
            'address'  => 'required',
            'port'     => '',
            'sitename' => 'required',
            'host'     => 'required|ipv4|unique:mikrotiks,host,' . $id,
        ]);

        if ($validator->fails()) {
            return error_message('Validation Error', $validator->errors()->all(), 422);
        } else {
            //create client
            new ConnectionService($request->get('host'), $request->get('username'), $request->get('password'), $request->get('port'));

            try {
                if ($this->user->hasPermissionTo('Miktrotik Edit', 'api')) {
                    $mikrotik = Mikrotik::find($id);
                    $mikrotik->identity = $request->identity;
                    $mikrotik->host = $request->host;
                    $mikrotik->username = $request->username;
                    $mikrotik->password = $request->password;
                    $mikrotik->port = $request->port;
                    $mikrotik->address = $request->address;
                    $mikrotik->sitename = $request->sitename;
                    $mikrotik->save();
                    return success_message("Data Update Successfully", $mikrotik);
                } else {
                    return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
                }
            } catch (Exception $e) {
                return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if ($this->user->hasPermissionTo('Miktrotik Delete', 'api')) {
                $mikrotik = Mikrotik::find($id);
                $mikrotik->delete();
                return success_message("Data Delete Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * changeStatus specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        try {
            Mikrotik::where('id', $id)->update(['status' => DB::raw("IF(status = 1, 0 ,1)")]);
            return success_message("Status change Successfully");
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    // mikrotikData
    public function mikrotikData()
    {
        if ($this->user->hasPermissionTo('PPPoE Active Users', 'api')) {
            $allOnlineData = array();
            $allmkt = Mikrotik::all();
            foreach ($allmkt as $mkitem) {
                try {
                    $connection = new ConnectionService($mkitem->host, $mkitem->username, $mkitem->password, $mkitem->port);
                    $query_all_active_online_user = $connection->allUserByMiktoTik();
                    foreach ($query_all_active_online_user as $sItem) {
                        $allOnlineData[] = $sItem;
                    }
                } catch (Exception $e) {
                    continue;
                }
            };
            $data  = [
                'alluser' => $allOnlineData,
                'allmikrotik' =>  $allmkt,
            ];
            return success_message("Get all Online User and mikrotik", $data);
        } else {
            return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
        }
    }
    /* 
    * getallmikrotikusers
    * Request
    */
    public function mikrotik_online_disconnect(Request $request, $id)
    {
        $mkitem = Mikrotik::find($id);
        try {
            $connection = new ConnectionService($mkitem->host, $mkitem->username, $mkitem->password, $mkitem->port);
            $connection->disconnectConnectedUser($request->name);
            return success_message("User Disconntct Successfully");
        } catch (Exception $exception) {
            return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
        }
        return success_message("User Disconnect Successfully");
    }
    /* 
    * getallmikrotikusers
    * Request
    */
    public function getallmikrotikOnlineusers(Request $request, $id)
    {
        $allOnlineData = array();
        $mkitem = Mikrotik::find($id);
        try {
            $connection = new ConnectionService($mkitem->host, $mkitem->username, $mkitem->password, $mkitem->port);
            $query_all_active_online_user = $connection->getallmikrotikOnlineusers();
            foreach ($query_all_active_online_user as $sItem) {
                $allOnlineData[] = $sItem;
                continue;
            }
        } catch (Exception $exception) {
            return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
        }

        return success_message("Get all mkt User", $allOnlineData);
    }
    /* 
    * getallmikrotikusers
    * Request
    */
    public function getallmikrotikusers(Request $request)
    {
        if ($request->type == 1) {
            $allOnlineData = array();
            $allmkt = Mikrotik::all();
            foreach ($allmkt as $mkitem) {
                try {
                    $connection = new ConnectionService($mkitem->host, $mkitem->username, $mkitem->password, $mkitem->port);
                    $query_all_active_online_user = $connection->getTotalOfflineMikroTikUsers();
                    foreach ($query_all_active_online_user as $sItem) {
                        $allOnlineData[] = $sItem;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
            return success_message("Get Online User", $allOnlineData);
        } else {
            $allOnlineData = array();
            $allmkt = Mikrotik::all();
            foreach ($allmkt as $mkitem) {
                try {
                    $connection = new ConnectionService($mkitem->host, $mkitem->username, $mkitem->password, $mkitem->port);
                    $query_all_active_online_user = $connection->getallmikrotikusers();
                    foreach ($query_all_active_online_user as $sItem) {
                        $allOnlineData[] = $sItem;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
            return success_message("Get Online User", $allOnlineData);
        };
    }

    // add to radious 
    public function addToRadius(Request $request, $id)
    {
        try {
            if ($this->user->hasPermissionTo('Add Radious', 'api')) {
                $mikrotik = Mikrotik::where('id', $id)->first();
                if (empty($mikrotik)) return error_message('Mikrotik not found');
                //create client
                $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                //pool*
                if (isset($request->type) && $request->type == 1 || $request->type == 'sync_all') {
                    $query_ppp_pool = $connection->poolPrint();
                    $data =  $this->ipPoolPrint($query_ppp_pool, $id);
                    return success_message("MikrTik IP Pool Saved Successfully.", $data);
                }
                //profile*
                if (isset($request->type) && $request->type == 2 || $request->type == 'sync_all') {
                    $query_ppp_profile = $connection->profilePrint();
                    $this->profilePrint($query_ppp_profile, $id);
                    return success_message("MikroTik Profile (Packages) Saved Successfully.");
                }
                //secret*
                if (isset($request->type) && $request->type == 3 || $request->type == 'sync_all') {
                    $query_ppp_secret = $connection->secretprint();
                    $this->secretPrint($query_ppp_secret, $id);
                    return success_message("MikroTik User (Secret) Saved Successfully.");
                }
                //activeConnectionUser
                if (isset($request->type) && $request->type == 4 || $request->type == 'sync_all') {
                    $query_ppp_active_online_user = $connection->activeConnectionUser();
                    return success_message("Get Online User", $query_ppp_active_online_user);
                }
                // disconnectConnectedUser
                if (isset($request->type) && $request->type == 5 || $request->type == 'sync_all') {
                    $query_ppp_dactive_online_user = $connection->disconnectConnectedUser();
                    return $query_ppp_dactive_online_user;
                }

                //sync queueSimplePrint form the mikrotik 
                if (isset($request->type) && $request->type == 6 || $request->type == 'sync_all') {
                    $query_queue_simple = $connection->queueSimplePrint();
                    $this->queueSimplePrintCreate($query_queue_simple, $id);
                    return success_message("Simple Queue Saved Successfully.");
                }
                //sync queue type form the mikrotik 
                if (isset($request->type) && $request->type == 7 || $request->type == 'sync_all') {
                    $query_queue_type = $connection->queueTypePrint();
                    $this->queueTypePrintCreate($query_queue_type, $id);
                    return success_message("Queue Type Saved Successfully.");
                }
                //sync ethernet form the mikrotik 
                if (isset($request->type) && $request->type == 8 || $request->type == 'sync_all') {
                    $query_queue_type = $connection->printEthernet();
                    $this->interfaceEthernetCreate($query_queue_type, $id);
                    return success_message("Ethernet Saved Successfully.");
                }
                //sync ip Address form the mikrotik 
                if (isset($request->type) && $request->type == 9 || $request->type == 'sync_all') {
                    $query_queue_type = $connection->ipAddressPrint();
                    $this->ipAddressCreate($query_queue_type, $id);
                    return success_message("IP Address Saved Successfully.");
                }
                //sync ip arp form the mikrotik 
                if (isset($request->type) && $request->type == 10 || $request->type == 'sync_all') {
                    $query_queue_type = $connection->printIpArp();
                    $this->ipArpCreate($query_queue_type, $id);
                    return success_message("IP ARP Saved Successfully.");
                }
                //sync interface form the mikrotik 
                if (isset($request->type) && $request->type == 11 || $request->type == 'sync_all') {
                    $query_data = $connection->printInterface();
                    $this->interfaceCreate($query_data, $id);
                    return success_message("Interface Saved Successfully.");
                }
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
            return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
        }
    }

    // deleteIpPool
    public function deleteIpPool(Request $request, $id)
    {

        try {
            // $mikrotik = Mikrotik::where('id', $id)->first();
            // if (empty($mikrotik)) return error_message('Mikrotik not found');
            // //create client
            // $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
            //pool
            $pool = IPpool::find($id);
            if (empty($pool)) return error_message('Ippool not found');
            if (isset($request->type) && $request->type == 1) {
                $pool->delete();
            }
            return success_message("Data Delete Successfully.");
        } catch (Exception $exception) {
            info($exception->getMessage());
            return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
        }
    }

    //create ip pool
    public function ipPoolPrint($responses, $mikrotik_id)
    {
        return $responses;
        foreach ($responses as $key => $ip_pool) {
            $range = explode('-', $ip_pool['ranges']);
            $check_ip_pool =  IPpool::where('name', $ip_pool['name'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_ip_pool) {

                if (count($range) == 2) {
                    $firstd =  explode('.', current($range));
                    $secondD =  explode('.', end($range));
                    // return [$range, $total_number_of_ip =  end($secondD) - end($firstd) + 1];
                    $total_number_of_ip =  end($secondD) - end($firstd);
                } else {
                    $range = array_filter(explode('/', $ip_pool['ranges']));
                    return $total_number_of_ip =  $this->calculateSubnet(end($range));
                }
                $data = [
                    'name'        => $ip_pool['name'] ?? '',
                    'nas_type'    => 'Mikrotik',
                    'type'        => 'PPPOE',
                    'start_ip'    => $range[0] ?? '',
                    'end_ip'      => $range[1] ?? '',
                    'mikrotik_id' => $mikrotik_id,
                    'subnet'      => $total_number_of_ip[1] ?? '',
                    'total_number_of_ip' => $total_number_of_ip[0] ?? $total_number_of_ip,
                ];
                IPpool::create($data);
            }
        }
    }


    public function calculateSubnet($data)
    {
        if ($data == 8) {
            $data =  [16777214, "255.0.0.0"]; // return first toatal number of ip and 2nd subnetmask
        } elseif ($data == 9) {
            $data =  [8388606, "255.128.0.0"];
        } elseif ($data == 10) {
            $data =  [4194302, '255.192.0.0'];
        } elseif ($data == 11) {
            $data =  [2097150, '255.224.0.0'];
        } elseif ($data == 12) {
            $data =  [1048574, '255.240.0.0'];
        } elseif ($data == 13) {
            $data =  [524286, '255.248.0.0'];
        } elseif ($data == 14) {
            $data =  [262142, '255.252.0.0'];
        } elseif ($data == 15) {
            $data =  [131070, '255.254.0.0'];
        } elseif ($data == 16) {
            $data =  [65534, '255.255.0.0'];
        } elseif ($data == 17) {
            $data = [32766, '255.255.128.0'];
        } elseif ($data == 18) {
            $data = [16382, '255.255.192.0'];
        } elseif ($data == 19) {
            $data = [8190, '255.255.224.0'];
        } elseif ($data == 20) {
            $data = [4094, '255.255.240.0'];
        } elseif ($data == 21) {
            $data = [2046, '255.255.248.0'];
        } elseif ($data == 22) {
            $data = [1022, '255.255.252.0'];
        } elseif ($data == 23) {
            $data = [210, '255.255.254.0'];
        } elseif ($data == 24) {
            $data = [254, '255.255.255.0'];
        } elseif ($data == 25) {
            $data = [126, '255.255.255.128'];
        } elseif ($data == 26) {
            $data = [62, '255.255.255.192'];
        } elseif ($data == 27) {
            $data = [30, '255.255.255.224'];
        } elseif ($data == 28) {
            $data = [14, '255.255.255.240'];
        } elseif ($data == 29) {
            $data = [6, '255.255.255.248'];
        } elseif ($data == 30) {
            $data = [2, '255.255.255.252'];
        } elseif ($data == 31) {
            $data = [0, '255.255.255.254'];
        } elseif ($data == 32) {
            $data = [0, '255.255.255.255'];
        } else {
            $data = [0, '000:000:000:000'];
        }
        return $data;
    }

    //create package
    public function profilePrint($responses, $mikrotik_id)
    {
        foreach ($responses as $profile) {
            $check_is_data_exists =  Package::where('name', $profile['name'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                if (isset($profile['remote-address'])) {
                    $check_is_pool_exists =  IPpool::select('name', 'id', 'mikrotik_id')
                        ->when($profile['remote-address'], function ($q) use ($profile) {
                            return $q->where('name', $profile['remote-address']);
                        })->first();
                    if ($check_is_pool_exists) {
                        $pool_id = $check_is_pool_exists->id;
                    } else {
                        $pool_id = 0;
                    }
                } else {
                    $pool_id = 0;
                };
                $data = [
                    'name'            => $profile['name'],
                    'type'            => 'PPPOE',
                    'nas_id'          => 0,
                    'mikrotik_id'     => $mikrotik_id,
                    'pool_id'         => $pool_id,
                    'price'           => 0,
                    'pop_price'       => 0,
                    'franchise_price' => 0,
                    'local_address'   => isset($profile['local-address']) ? $profile['local-address'] : '',
                    'bandwidth'       => $profile['rate-limit'] ?? '',
                    'status'          => isset($profile['default']) ? $profile['default'] : '',
                ];
                Package::create($data);
            }
        }
    }

    //ppp users
    public function secretPrint($responses, $mikrotik_id)
    {
        foreach ($responses as $pppuser) {
            $check_pppuser =  PppUser::where('name', $pppuser['name'])->where('id_in_mkt', $pppuser['.id'])->first();
            if (!$check_pppuser) {
                $ppp = new PppUser();
                $ppp->id_in_mkt = $pppuser['.id'];
                $ppp->mikrotik_id = $mikrotik_id;
                $ppp->name = $pppuser['name'];
                $ppp->service = isset($pppuser['service']) ? $pppuser['service'] : '';
                $ppp->password = isset($pppuser['password']) ? $pppuser['password'] : '';
                $ppp->profile = isset($pppuser['profile']) ? $pppuser['profile'] : '';
                if (!empty($pppuser['local-address'])) {
                    $ppp->localAddress = $pppuser['local-address'];
                }
                if (!empty($pppuser['remote-address'])) {
                    $ppp->remoteAddress = $pppuser['remote-address'];
                }
                if (!empty($pppuser['only-one'])) {
                    $ppp->onlyOne = $pppuser['only-one'];
                }
                if (!empty($pppuser['rate-limit'])) {
                    $ppp->rateLimit = $pppuser['rate-limit'];
                }
                if (!empty($pppuser['dns-server'])) {
                    $ppp->dns = $pppuser['dns-server'];
                }
                $ppp->status  = isset($pppuser['disabled']) ? $pppuser['disabled'] : 'true';
                $ppp->save();
            }
        }
    }
    //store data into database queues Table
    public function queueSimplePrintCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  Queue::where('name', $dataItem['name'])->first();
            if (!$check_is_data_exists) {
                $data = [
                    'name'            => $dataItem['name'],
                    'id_in_mkt'       => $dataItem['.id'],
                    'mikrotik_id'     => $mikrotik_id,
                    'queue_type'      => $dataItem['queue'],
                    'target'          => isset($dataItem['target']) ? $dataItem['target'] : "",
                    'dst'             => isset($dataItem['dst']) ? $dataItem['dst'] : "",
                    'parent'          => $dataItem['parent'],
                    'packet-marks'    => $dataItem['packet-marks'],
                    'priority'        => $dataItem['priority'],
                    'max-limit'       => $dataItem['max-limit'],
                    'burst-limit'     => $dataItem['burst-limit'],
                    'burst-threshold' => $dataItem['burst-threshold'],
                    'burst-time'      => $dataItem['burst-time'],
                    'comment'         => isset($dataItem['comment']) ? $dataItem['comment'] : "",
                    'status'          => isset($dataItem['disabled']) ? $dataItem['disabled'] : '',
                ];
                Queue::create($data);
            }
        }
    }
    //store data into database queue_type Table
    public function queueTypePrintCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  QueueType::where('name', $dataItem['name'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                $data = [
                    'name'            => $dataItem['name'],
                    'id_in_mkt'       => $dataItem['.id'],
                    'mikrotik_id'     => $mikrotik_id,
                    'kind'            => $dataItem['kind'],
                    'pcq_rate'        => isset($dataItem['pcq-rate']) ? $dataItem['pcq-rate'] : '',
                    'burst_rate'      => isset($dataItem['pcq-burst-rate']) ? $dataItem['pcq-burst-rate'] : '',
                    'burst_threshold' => isset($dataItem['pcq-burst-threshold']) ? $dataItem['pcq-burst-threshold'] : '',
                    'burst_time'      => isset($dataItem['pcq-burst-time']) ? $dataItem['pcq-burst-time'] : '',
                    'pcq_classifier'  => isset($dataItem['pcq-classifier']) ? $dataItem['pcq-classifier'] : '',
                    'status'          => isset($dataItem['default']) ? $dataItem['default'] : '',
                ];
                QueueType::create($data);
            }
        }
    }
    //store data into database Ethernet Table
    public function interfaceEthernetCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  InterfaceEthernet::where('id_in_mkt', $dataItem['.id'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                $data = [
                    'name'        => isset($dataItem['name']) ? $dataItem['name'] : '',
                    'id_in_mkt'   => $dataItem['.id'],
                    'mikrotik_id' => $mikrotik_id,
                    'arp'         => $dataItem['arp'],
                    'mac_address' => isset($dataItem['mac-address']) ? $dataItem['mac-address'] : '',
                    'mtu'         => isset($dataItem['mtu']) ? $dataItem['mtu'] : '',
                    'comment'     => isset($dataItem['comment']) ? $dataItem['comment'] : '',
                    'status'      => isset($dataItem['disabled']) ? $dataItem['disabled'] : '',
                ];
                InterfaceEthernet::create($data);
            }
        }
    }
    //store data into database ip address Table
    public function ipAddressCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  InterfaceAddress::where('id_in_mkt', $dataItem['.id'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                $data = [
                    'id_in_mkt'        => $dataItem['.id'],
                    'mikrotik_id'      => $mikrotik_id,
                    'address'          => $dataItem['address'],
                    'network'          => isset($dataItem['network']) ? $dataItem['network'] : '',
                    'actual-interface' => isset($dataItem['actual-interface']) ? $dataItem['actual-interface'] : '',
                    'interface'        => isset($dataItem['interface']) ? $dataItem['interface'] : '',
                    'comment'          => isset($dataItem['comment']) ? $dataItem['comment'] : '',
                    'status'           => isset($dataItem['disabled']) ? $dataItem['disabled'] : '',
                ];
                InterfaceAddress::create($data);
            }
        }
    }
    //store data into database ip address Table
    public function ipArpCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  IpArp::where('id_in_mkt', $dataItem['.id'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                $data = [
                    'mikrotik_id'  => $mikrotik_id,
                    'id_in_mkt'    => $dataItem['.id'],
                    'DHCP'         => $dataItem['DHCP'],
                    'address'      => $dataItem['address'],
                    'complete'     => isset($dataItem['complete']) ? $dataItem['complete'] : '',
                    'interface'    => isset($dataItem['interface']) ? $dataItem['interface'] : '',
                    'dynamic'      => isset($dataItem['dynamic']) ? $dataItem['dynamic'] : '',
                    'mac_address'  => isset($dataItem['mac-address']) ? $dataItem['mac-address'] : '',
                    'published'    => isset($dataItem['published']) ? $dataItem['published'] : '',
                    'comment'      => isset($dataItem['comment']) ? $dataItem['comment'] : '',
                    'status'       => isset($dataItem['disabled']) ? $dataItem['disabled'] : '',
                ];
                IpArp::create($data);
            }
        }
    }
    //store data into database Intraface Model
    public function interfaceCreate($res, $mikrotik_id)
    {
        foreach ($res as $dataItem) {
            $check_is_data_exists =  Intrface::where('id_in_mkt', $dataItem['.id'])->where('mikrotik_id', $mikrotik_id)->first();
            if (!$check_is_data_exists) {
                if (isset($dataItem['name']) && strpos($dataItem['name'], 'pppoe') !== false || strpos($dataItem['name'], 'pptp') !== false) {
                } else {
                    $data = [
                        'mikrotik_id'          => $mikrotik_id,
                        'id_in_mkt'            => $dataItem['.id'],
                        'name'                 => $dataItem['name'],
                        'mtu'                  => isset($dataItem['mtu']) ? $dataItem['mtu'] : '',
                        'type'                 => isset($dataItem['type']) ? $dataItem['type'] : '',
                        'actual_mtu'           => isset($dataItem['actual-mtu']) ? $dataItem['actual-mtu'] : '',
                        'arp'                  => isset($dataItem['arp']) ? $dataItem['arp'] : '',
                        'mac_address'          => isset($dataItem['mac-address']) ? $dataItem['mac-address'] : '',
                        'last_link_up_time'    => isset($dataItem['last-link-up-time']) ? $dataItem['last-link-up-time'] : '',
                        'last_link_down_time'  => isset($dataItem['last-link-down-time']) ? $dataItem['last-link-down-time'] : '',
                        'link_downs'           => isset($dataItem['link-downs']) ? $dataItem['link-downs'] : '',
                        'use_service_tag'      => isset($dataItem['use-service-tag']) ? $dataItem['use-service-tag'] : '',
                        'comment'              => isset($dataItem['comment']) ? $dataItem['comment'] : '',
                        'status'               => isset($dataItem['disabled']) ? $dataItem['disabled'] : '',
                    ];
                    Intrface::create($data);
                }
            }
        }
    }
}
