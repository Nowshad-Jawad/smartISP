<?php

namespace App\Services;

use App\Models\IPpool;
use App\Models\Package;
use App\Models\Softwaresystem;
use App\Models\User\UserBillingInfo;
use App\Models\UserConnectionInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

use function PHPUnit\Framework\returnSelf;

class ConnectionService
{
    private $client;
    public $expire_date = '';
    public function __construct($host, $user, $pass, $port)
    {
        $config =  (new Config())
            ->set('timeout', 1)
            ->set('host', $host)
            ->set('user', $user)
            ->set('pass', $pass)
            ->set('port', (int) $port);
        $this->client = new Client($config);
    }

    /**
     * add a user to mikrotik
     *
     * @param [type] $request
     * @return void
     */
    public function addUserToMikrotik($request)
    {
        $pack = Package::find($request['package_id']);
        // dd(date('d/m/Y H:i a',strtotime($request->connection_date) + $pack->validdays));
        $expire_date = date('d/m/Y H:i a',strtotime($request->connection_date) + $pack->validdays);
        // // return $request->expire_date !== '' ? $request->expire_date : $pack['fixed_expire_time'];
        // if ($request->expire_date) {;
        //     $expire_date =  date('d-m-Y H:i a', strtotime($request->expire_date));
        //     $this->expire_date = $request->expire_date;
        // } else {
        //     $expire_date = "(Package Date) " . date('d-m-Y H:i a', strtotime($pack['fixed_expire_time']));
        //     $this->expire_date = $pack['fixed_expire_time'];
        // }
        // if ($this->expire_date == '') return 'Package Expire date and User Custom expire date Both null';
        // if (!$pack) return false;
        $comment = "Phone:" . ($request->phone !== '' ? $request->phone : 'N/A') . "| Zone:" . ($request->zone_id !== '' ? $request->zone->name : 'N/A') . " | Package: $pack->name.  | Connection Date: " . ($request->connection_date !== '' ? $request->connection_date : 'N/A') . " | Exprire Date: $expire_date";
        // if ($request['remote_address']) {
        //     $query = (new Query('/ppp/secret/add'))
        //         ->equal('name', $request['username'])
        //         ->equal('password', $request['userpassword'])
        //         ->equal('service', 'pppoe')
        //         ->equal('remote-address', $request['remote_address'])
        //         ->equal('profile', $pack['name'])
        //         ->equal('comment', "$comment")
        //         // ->equal('disabled', 'yes');
        //     $this->client->query($query)->read();
        //     return  array([
        //         'expire_date' => $this->expire_date,
        //     ]);
        // } else {
            $query = (new Query('/ppp/secret/add'))
                ->equal('name', $request['username'])
                ->equal('password', $request['password'])
                ->equal('service', 'pppoe')
                ->equal('profile', $pack['name'])
                ->equal('comment', $comment)
                ->equal('disabled', 'no');
            $this->client->query($query)->read();
            return  array([
                'expire_date' => $expire_date,
            ]);
        // }
    }
    /**
     * check_milrotiok_user_status
     *
     * @param [type] $request
     * @return void
     */
    public function check_milrotiok_user_status($username)
    {
        $query = (new Query("/ppp/active/print"))->where('name', $username);
        $query_data = $this->client->query($query)->read();
        if ($query_data) {
            return $query_data;
        } else {
            $query = (new Query("/ppp/secret/print"))->where('name', $username);
            return $this->client->query($query)->read();
        }
    }

    /**
     * add a user to mikrotik
     *
     * @param [type] $request
     * @return void
     */
    public function updateUserToMikrotik($request, $oldData, $expire_date)
    {
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', $oldData->username);
        $secrets  =  $this->client->query($query_ppp_pool)->read();
        $profile = Package::where('id', $request->package)->first();
        if (!$profile) return false;
        $exprireDate = $expire_date !== null ? $expire_date : $profile->fixed_expire_time;
        $comment = "Phone: $request->phone  | Zone:" . ($request->zone !== '' ? $request->zone : 'N/A') . " | Package: $profile->name  | Connection Date: " . ($request->connection_date !== '' ? $request->connection_date : 'N/A') . " | Exprire Date: $exprireDate";
        foreach ($secrets as $secret) {
            $query = (new Query('/ppp/secret/set'))
                ->equal('.id', $secret['.id'])
                ->equal('profile', $profile->name)
                ->equal('password', $request->userpassword)
                ->equal('service', $request->service ? $request->service : 'pppoe')
                ->equal('comment', $request->router_component !== null ? $request->router_component : $comment);
            $this->client->query($query)->read();
        }
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', $oldData->username);
        return $this->client->query($query_ppp_pool)->read();
    }
    /**
     * Change a user to Profile
     *
     * @param [type] $request
     * @return void
     */
    public function addQueueTargetAddress($req)
    {
        $query = (new Query('/queue/simple/print'))->where('name', $req->queue_name);
        $query_profile =  $this->client->query($query)->read();
        foreach ($query_profile as $p) {
            $trdata =  str_replace(array("$req->target_address/32"), "", $p['target']);
            // return   $trdata;
            $query = (new Query('/queue/simple/set'))
                ->equal('.id', $p['.id'])
                ->equal('target', $trdata . ',' . $req->target_address);
            $this->client->query($query)->read();
        }
        $query = (new Query('/queue/simple/print'))->where('name', $req->queue_name);
        return  $this->client->query($query)->read();
    }
    /**
     * disconnect Queue change target address a user to Profile
     *
     * @param [type] $request
     * @return void
     */
    public function removeQueueTargetAddress()
    {
        $query = (new Query('/queue/simple/print'))->where('name', 'test');
        $query_profile =  $this->client->query($query)->read();
        foreach ($query_profile as $p) {
            // $trdata =  str_replace(array('192.168.168.168/32'), "", $p['target']);
            $query = (new Query('/queue/simple/set'))
                ->equal('.id', $p['.id'])
                ->equal('target', $p['target'] . ',' . '10.10.99.99');
            $this->client->query($query)->read();
        }
        $query = (new Query('/queue/simple/print'))->where('name', 'Disconnect');
        return  $this->client->query($query)->read();
    }
    /**
     * Change a user to Profile
     *
     * itemid: "51"
     * mikrotik: "2"
     * user_name: "testuser3"
     * @param [type] $request
     * @return void
     */
    public function chnageUserProfile($itemid, $user_name, $disconnected_package)
    {
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', $user_name);
        $secrets  =  $this->client->query($query_ppp_pool)->read();
        if ($secrets) {
            $query = (new Query('/ppp/secret/set'))
                ->equal('.id', $secrets[0]['.id'])
                ->equal('profile', $disconnected_package);
            $this->client->query($query)->read();
            $user_info =  UserConnectionInfo::where('user_id', $itemid)->first();
            if (isset($secrets[0]['remote-address'])) {
                if ($user_info) {
                    $query_data = $this->mikrotik_user_change_status($user_info->username, $user_info->status);
                    if ($query_data) {
                        // return $user_info;
                        $user_info->status = $query_data[0]['disabled'] == 'true' ? 0 : 1;
                        $user_info->save();
                    }
                } else {
                    $user_info->status =  $user_info['status'] == 0 ? 1 : 0;
                    $user_info->save();
                }
                $this->disconnectConnectedUser($user_name);
            }
        }
    }
    /**
     * Change a user to Profile
     *
     * itemid: "51"
     * mikrotik: "2"
     * user_name: "testuser3"
     * @param [type] $request
     * @return void
     */
    public function disconnectUserProfile($itemid, $user_name, $disconnected_package, $d_user_c = true)
    {
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', $user_name);
        $secrets  =  $this->client->query($query_ppp_pool)->read();
        if ($secrets) {
            $query = (new Query('/ppp/secret/set'))
                ->equal('.id', $secrets[0]['.id'])
                ->equal('profile', $disconnected_package)
                ->equal('disabled', $d_user_c == true ? 'true' : 'false');

            $this->client->query($query)->read();
            $secrets  =  $this->client->query($query_ppp_pool)->read();
            if (isset($secrets[0]['remote-address'])) {

                if ($d_user_c == true) {
                    UserConnectionInfo::where('user_id', $itemid)->update([
                        'status' => 0,
                    ]);
                    //don't disconnect user when get data is false
                    // related to grace user disconnect 
                    $this->disconnectConnectedUser($user_name);
                }
            } else {
                if ($d_user_c == true) {
                    UserConnectionInfo::where('user_id', $itemid)->update([
                        'status' => 0,
                    ]);
                }
            }
        }
    }

    // activeDisconnectedUser
    public function activeDisconnectedUser($itemid, $user_name)
    {
        $userBillingInfo = UserBillingInfo::select('purches_package_id', 'package_id', 'user_id')->with('package')->where('user_id', $itemid)->first();
        $oldpackage = Package::where('id', $userBillingInfo->purches_package_id)->first();
        $query_data = (new Query("/ppp/secret/print"))->where('name', $user_name);
        $secrets  =  $this->client->query($query_data)->read();
        if ($secrets) {
            $query = (new Query('/ppp/secret/set'))
                ->equal('.id', $secrets[0]['.id'])
                ->equal('disabled', 'false')
                ->equal('profile', $oldpackage['name']);
            $this->client->query($query)->read();
            $updatedData  =  $this->client->query($query_data)->read();
            $user_info =  UserConnectionInfo::where('user_id', $itemid)->first();
            if ($user_info) {
                $user_info->update([
                    'status' => $updatedData[0]['disabled'] == 'true' ? 0 : 1,
                ]);
            }
            return $updatedData;
        }
    }

    /**
     * IP Profile print for ppp
     *
     * @return void
     */
    public function profile_add($req)
    {
        //################## check to add new package in mikrotik  start##################
        $query = (new Query('/ppp/profile/add'))
            ->equal('name', $req->name)
            ->equal('local-address', $req->local_address)
            ->equal('remote-address', $req->ip_pool['label']) //like as ip pool
            ->equal('only-one', 'yes')
            ->equal('rate-limit', $req->bandwidth ?? "")
            ->equal('comment', $req->comment ? $req->comment : Carbon::now() . '|' . Auth::user()->name);
        $this->client->query($query)->read();
        //################## check to add new package in mikrotik  start end ##################
    }
    /**
     * IP pool print for ppp
     *
     * @return void
     */
    public function profile_update($req, $prev_name)
    {
        $query_ppp_profile = (new Query('/ppp/profile/print'))->where('name', $prev_name);
        $query_profile =  $this->client->query($query_ppp_profile)->read();
        $ippool =  IPpool::where('id', $req->ip_pool)->first();
        foreach ($query_profile as $p) {
            $query = (new Query('/ppp/profile/set'))
                ->equal('.id', $p['.id'])
                ->equal('name', $req->name)
                ->equal('local-address', $req->local_address)
                ->equal('remote-address', $ippool ? $ippool->name : $p['remote-address']) //like as ip pool
                ->equal('rate-limit', $req->bandwidth)
                ->equal('comment', $req->comment ? $req->comment : Carbon::now() . '|' . Auth::user()->name);
            $this->client->query($query)->read();
        }
        $query_ppp_profile = (new Query('/ppp/profile/print'))->where('name', $req->name);
        return  $this->client->query($query_ppp_profile)->read();
    }
    /**
     * Ethernet update Status
     *
     * @return void
     */
    public function profileChangeStatus($request)
    {
        $result = $request->status == 'true' ? 'false' : 'true';
        $query = (new Query('/ppp/profile/print'))->where('name', $request->name);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/ppp/profile/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('default', $result);
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/ppp/profile/print"))->where('name', $request->name);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    /**
     * IP pool print for ppp
     *
     * @return void
     */
    public function poolPrint()
    {


        // $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeemvulval');
        // $secrets  =  $this->client->query($query_ppp_pool)->read();

        // foreach ($secrets as $secret) {
        //     $query = (new Query('/ppp/secret/set'))
        //         ->equal('.id', $secret['.id'])
        //         ->equal('name', 'nayeem_moner')
        //         ->equal('password', '98765321')
        //         ->equal('service', 'pppoe')
        //         ->equal('profile', '15Mbps-1000tk')
        //         ->equal('disabled', 'false')
        //         ->equal('comment', 'nayeem comment edited');
        //     $this->client->query($query)->read();
        // }
        // $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeem_moner');
        // return $this->client->query($query_ppp_pool)->read();



        //print pool data 
        $query_ppp_pool = new Query('/ip/pool/print');
        return $this->client->query($query_ppp_pool)->read();
    }

    /* 
        *req for mac bind specific user name
        *
       *@return void $request
        *
        */
    public function mikrotik_user_change_status($name, $status)
    {
        $query = (new Query('/ppp/secret/print'))->where('name', $name);
        $querysData =  $this->client->query($query)->read();
        $loopquery = (new Query('/ppp/secret/set'))
            ->equal('.id', $querysData[0]['.id'])
            ->equal('disabled', $status == 0 ? 'false' : 'true');
        $this->client->query($loopquery)->read();
        return $this->client->query($query)->read();
    }
    /* 
        *req for mac bind specific user name
        *
       *@return void $request
        *
        */
    // public function update_mikrotik_enabled_user($request)
    // {
    //     $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeem');
    //     $secrets  =  $this->client->query($query_ppp_pool)->read();

    //     foreach ($secrets as $secret) {
    //         $query = (new Query('/ppp/secret/set'))
    //             ->equal('.id', $secret['.id'])
    //             ->equal('name', 'nayeem')
    //             ->equal('password', '98765321')
    //             ->equal('service', 'pppoe')
    //             ->equal('profile', '15Mbps-1000tk')
    //             ->equal('disabled', 'false')
    //             ->equal('comment', 'nayeem comment edited');
    //         $this->client->query($query)->read();
    //     }
    //     $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeem');
    //     return $this->client->query($query_ppp_pool)->read();
    // }
    /* 
        *req for mac bind specific user name
        *
       *@return void $request
        *
        */
    public function update_mikrotik_enabled_user($request)
    {
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeem');
        $secrets  =  $this->client->query($query_ppp_pool)->read();

        foreach ($secrets as $secret) {
            $query = (new Query('/ppp/secret/set'))
                ->equal('.id', $secret['.id'])
                ->equal('name', 'nayeem')
                ->equal('password', '98765321')
                ->equal('service', 'pppoe')
                ->equal('profile', '15Mbps-1000tk')
                ->equal('disabled', 'false')
                ->equal('comment', 'nayeem comment edited');
            $this->client->query($query)->read();
        }
        $query_ppp_pool = (new Query("/ppp/secret/print"))->where('name', 'nayeem');
        return $this->client->query($query_ppp_pool)->read();
    }
    /* 
        *req for mac bind specific user name
        *
       *@return void $username
        *
        */
    public function mac_bind($username)
    {
        $query = (new Query("/ppp/secret/print"))->where('name', $username);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                if (isset($secret['last-caller-id']) && $secret['last-caller-id'] !== '') {
                    $secretQuery = (new Query('/ppp/secret/set'))
                        ->equal('.id', $secret['.id'])
                        ->equal('caller-id', $secret['last-caller-id']);
                    $this->client->query($secretQuery)->read();
                } else {
                    $activequery = (new Query("/ppp/active/print"))->where('name', $username);
                    $activrsecrets  =  $this->client->query($activequery)->read();
                    foreach ($activrsecrets as $activrsecret) {
                        $query = (new Query('/ppp/secret/set'))
                            ->equal('.id', $secret['.id'])
                            ->equal('caller-id', $activrsecret['caller-id']);
                        $this->client->query($query)->read();
                    }
                }
            }
            $query = (new Query("/ppp/secret/print"))->where('name', $username);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }

    /*
        *req for mac unbind specific user name
        *
       *@return void $username
        *
        */
    public function mac_unbind($username)
    {
        $query = (new Query("/ppp/secret/print"))->where('name', $username);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/ppp/secret/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('caller-id', '');
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/ppp/secret/print"))->where('name', $username);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }


    /* 
        *add addQueue user name
        *
       *@return void $request
        *
        */
    public function addSimpleQueue($request)
    {
        $query = new Query('/queue/simple/add');
        $query->equal('name', $request->queue_name);
        $query->equal('target', $request->target);
        $query->equal('dst', $request->destination);
        if ($request->priority_upload && $request->priority_download) {
            $query->equal('priority', $request->priority_upload . '/' . $request->priority_download);
        }
        if ($request->queue_type_upload && $request->queue_type_download) {
            $query->equal('queue', $request->queue_type_upload . '/' . $request->queue_type_download);
        }
        if ($request->max_limit_upload && $request->max_limit_download) {
            $query->equal('max-limit', $request->max_limit_upload . '/' . $request->max_limit_download);
        }
        if ($request->burst_limit_upload && $request->burst_limit_download) {
            $query->equal('burst-limit', $request->burst_limit_upload . '/' . $request->burst_limit_download);
        }
        if ($request->burst_threshold_upload && $request->burst_threshold_download) {
            $query->equal('burst-threshold', $request->burst_threshold_upload . "/" . $request->burst_threshold_download);
        }
        if ($request->burst_time_upload && $request->burst_time_download) {
            $query->equal('burst-time', $request->burst_time_upload . '/' . $request->burst_time_download);
        }
        $query->equal('disabled', 'false');
        $query->equal('comment', $request->comment);
        $res = $this->client->query($query)->read();
        if (isset($res['after']['message'])) return $res['after']['message'];
        $query = (new Query("/queue/simple/print"))->where('.id', $res['after']['ret']);
        return $this->client->query($query)->read();
    }

    /* 
        *Remove simple Queue 
        *
       *@return void $request
        *
        */
    public function queueRemove($req)
    {
        $rmData = (new Query('/queue/type/remove'))->equal('numbers', $req->id_in_mkt);
        return $this->client->query($rmData)->read();
    }

    /* 
        *add addQueueType 
        *
       *@return void $request
        *
        */
    public function addQueueType($request)
    {
        if ($request->dst_address === true && $request->src_address === true) {
            $classifer = 'dst-address,src-address';
        } elseif ($request->dst_address === true) {
            $classifer = 'dst-address';
        } elseif ($request->src_address === true) {
            $classifer = 'src-address';
        } else {
            $classifer = '';
        }
        $query = new Query('/queue/type/add');
        $query->equal('name', $request->name);
        $query->equal('kind', $request->queue_kind);
        $query->equal('pcq-rate', $request->rate);
        $query->equal('pcq-burst-rate', $request->burst_rate);
        $query->equal('pcq-burst-threshold', $request->burst_threshol);
        $query->equal('pcq-burst-time', $request->burst_time);
        $query->equal('pcq-classifier', $classifer);
        $res = $this->client->query($query)->read();
        if (isset($res['after']['message'])) return $res['after']['message'];
        $query = (new Query("/queue/type/print"))->where('.id', $res['after']['ret']);
        return $this->client->query($query)->read();
    }
    /* 
        *Update addQueueType 
        *
       *@return void $request
        *
        */
    public function updateQueueType($request)
    {

        $result = $request->status == 'true' ? 'false' : 'true';
        $query = (new Query("/queue/type/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            if ($request->dst_address === true && $request->src_address === true) {
                $classifer = 'dst-address,src-address';
            } elseif ($request->dst_address === true) {
                $classifer = 'dst-address';
            } elseif ($request->src_address === true) {
                $classifer = 'src-address';
            } else {
                $classifer = '';
            }
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/queue/type/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('name', $request->name)
                    ->equal('kind', $request->queue_kind)
                    ->equal('pcq-rate', $request->rate)
                    ->equal('pcq-burst-rate', $request->burst_rate)
                    ->equal('pcq-burst-threshold', $request->burst_threshold)
                    ->equal('pcq-burst-time', $request->burst_time)
                    ->equal('pcq-classifier', $classifer);
                $res = $this->client->query($secretQuery)->read();
            }
            if (isset($res['after']['message'])) return $res['after']['message'];
            $query = (new Query("/queue/type/print"))->where('.id', $request->id_in_mkt);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }

    public function queueTypeRemove($req)
    {
        $rmData = (new Query('/queue/type/remove'))->equal('numbers', $req->id_in_mkt);
        return $this->client->query($rmData)->read();
    }


    /**
     * Print Interfaces 
     *
     * @return void
     */
    public function printInterface()
    {
        $query = new Query("/interface/print");
        return $this->client->query($query)->read();
    }


    /* 
        *add addQueueType 
        *
       *@return void $request
        *
        */
    public function addInterfaces($request)
    {
        $query = new Query('/interface/vlan/add');
        $query->equal('name', $request->name);
        $query->equal('arp', $request->arp);
        $query->equal('mtu', $request->mtu);
        $query->equal('vlan-id', $request->vlan_id);
        $query->equal('interface', $request->interface);
        $query->equal('use-service-tag', $request->use_service_tag);
        $query->equal('comment', $request->comment);
        $res =  $this->client->query($query)->read();
        if (isset($res['after']['message'])) return $res['after']['message'];
        $query = (new Query("/interface/vlan/print"))->where('.id', $res['after']['ret']);
        return $this->client->query($query)->read();
    }

    public function IntrfaceRemove($req)
    {
        $rmData = (new Query('/interface/vlan/remove'))->equal('numbers', $req->id_in_mkt);
        return $this->client->query($rmData)->read();
    }


    /**
     * Ethernet update Status
     *
     * @return void
     */
    public function intrfaceUpdateStatus($request)
    {
        $result = $request->status == 'true' ? 'false' : 'true';
        $query = (new Query("/interface/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/interface/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('disabled', $result);
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            if ($request->type == 'vlan') {
                $query = (new Query("/interface/vlan/print"))->where('.id', $request->id_in_mkt);
            } else {
                $query = (new Query("/interface/print"))->where('.id', $request->id_in_mkt);
            }
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }


    /**
     * Ethernet create
     *
     * @return void
     */
    public function interfaceUpdate($request)
    {
        $query = (new Query("/interface/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/interface/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('name', $request->name)
                    ->equal('arp', $request->arp)
                    ->equal('mtu', $request->mtu)
                    ->equal('vlan-id', $request->vlan_id)
                    ->equal('interface', $request->interface)
                    ->equal('use-service-tag', $request->use_service_tag)
                    ->equal('comment', $request->comment);
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/interface/print"))->where('.id', $request->id_in_mkt);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    /**
     * profile print for ppp
     *
     * @return void
     */
    public function profilePrint()
    {
        $query_ppp_profile = new Query('/ppp/profile/print');
        return $this->client->query($query_ppp_profile)->read();
    }

    /**
     * queue print for ppp
     *
     * @return void
     */
    public function queueSimplePrint()
    {
        $query_ppp_secret = new Query('/queue/simple/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * queue print for ppp
     *
     * @return void
     */
    public function queueTypePrint()
    {
        $query_ppp_secret = new Query('/queue/type/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * Ethernet print for ppp
     *
     * @return void
     */
    public function printEthernet()
    {
        $query_ppp_secret = new Query('/interface/ethernet/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * Ethernet create
     *
     * @return void
     */
    public function ethernetUpdate($request)
    {
        $query = (new Query("/interface/ethernet/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/interface/ethernet/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('name', $request->name)
                    ->equal('arp', $request->arp)
                    ->equal('mtu', $request->mtu)
                    ->equal('disabled', $request->status)
                    ->equal('comment', $request->comment);
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/interface/ethernet/print"))->where('.id', $request->id_in_mkt);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    /**
     * Ethernet update Status
     *
     * @return void
     */
    public function ethernetUpdateStatus($request)
    {
        $result = $request->status == 'true' ? 'false' : 'true';
        $query = (new Query("/interface/ethernet/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/interface/ethernet/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('disabled', $result);
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/interface/ethernet/print"))->where('.id', $request->id_in_mkt);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    /**
     * Ethernet update Status
     *
     * @return void
     */
    public function ethernetDelete($request)
    {
        return   $rmData = (new Query('/interface/ethernet/remove'))->equal('numbers', $request->id_in_mkt);
        return $this->client->query($rmData)->read();
    }
    /**
     * Ethernet print for ppp
     *
     * @return void
     */
    public function changeEthernetArp()
    {
        $query = (new Query("/interface/ethernet/print"))->where('name', 'ether5');
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/interface/ethernet/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('arp', 'enabled');
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/interface/ethernet/print"))->where('name', 'ether5');
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    /**
     * Ip Address print for ppp
     *
     * @return void
     */
    public function ipAddressPrint()
    {
        $query_ppp_secret = new Query('/ip/address/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * Ip Address print for ppp
     *
     * @return void
     */

    public function ipAddressAdd($request)
    {
        $query = new Query('/ip/address/add');
        $query->equal('address', $request->address);
        if (isset($request->network)) {
            $query->equal('network', $request->network);
        }
        $query->equal('interface', $request->interface);
        $query->equal('disabled', 'false');
        $query->equal('comment', $request->comment);
        $res =  $this->client->query($query)->read();
        if (isset($res['after']['message'])) {
            return $res['after']['message'];
        } else {
            $query = (new Query("/ip/address/print"))->where('.id', $res['after']['ret']);
            return $this->client->query($query)->read();
        }
    }
    /**
     * Ip Address print for ppp
     *
     * @return void
     */
    public function ipAddressEdit()
    {
        $query = (new Query("/ip/address/print"))->where('.id', '*7');
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/ip/address/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('disabled', 'true')
                    ->equal('interface', 'true')
                    ->equal('comment', 'true');
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/ip/address/print"))->where('.id', '*7');
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }
    public function ipAddressRemove()
    {
        $query = (new Query("/ip/address/print"))->where('.id', '*7');
        $query_data = $this->client->query($query)->read();
        if (!$query_data) return false;
        $rmData = (new Query('/ip/address/remove'))->equal('numbers', $query_data[0]['.id']);
        return $this->client->query($rmData)->read();
    }

    /**
     * Ethernet print for ppp
     *
     * @return void
     */
    public function printIpArp()
    {
        $query_ppp_secret = new Query('/ip/arp/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * addIPArp 
     *
     * @return void
     */
    public function ipArpAdd($request)
    {
        $query = new Query('/ip/arp/add');
        $query->equal('address', $request->address);
        if (isset($request->network)) {
            $query->equal('network', $request->network);
        }
        $query->equal('interface', $request->interface);
        $query->equal('disabled', 'false');
        $query->equal('mac-address', $request->mac_address);
        $query->equal('disabled', 'false');
        if (isset($request->comment)) {
            $query->equal('comment', $request->comment);
        } else {
            $comment = 'Phone:' . $request->mobile ?? 'null' . '||' . $request->zone . '||' . $request->profile . '||' . $request->connection_date ?? '';
            $query->equal('comment', $comment);
        }
        $res =  $this->client->query($query)->read();
        if (isset($res['after']['message'])) {
            return $res['after']['message'];
        } else {
            $query = (new Query("/ip/arp/print"))->where('.id', $res['after']['ret']);
            return $this->client->query($query)->read();
        }
    }


    /**
     * Ip Arp Change Status
     *
     * @return void
     */
    public function ipArpChangeStatus($request)
    {
        $result = $request->status == 'true' ? 'false' : 'true';
        $query = (new Query("/ip/arp/print"))->where('.id', $request->id_in_mkt);
        $secrets = $this->client->query($query)->read();
        if ($secrets) {
            // Parse secrets and set password
            foreach ($secrets as $secret) {
                $secretQuery = (new Query('/ip/arp/set'))
                    ->equal('.id', $secret['.id'])
                    ->equal('disabled', $result);
                // Update query ordinary have no return
                $this->client->query($secretQuery)->read();
            }
            $query = (new Query("/ip/arp/print"))->where('.id', $request->id_in_mkt);
            return $this->client->query($query)->read();
        } else {
            return false;
        }
    }

    /**
     * Ethernet update Status
     *
     * @return void
     */
    public function ipArpDelete($request)
    {
        return $rmData = (new Query('/ip/arp/remove'))->equal('numbers', $request->id_in_mkt);
        return $this->client->query($rmData)->read();
    }


    /**
     * secret print for ppp
     *
     * @return void
     */
    public function secretprint()
    {
        // $query_ppp_secret = new Query('/queue/type/print');
        // return $this->client->query($query_ppp_secret)->read();


        $query_ppp_secret = new Query('/ppp/secret/print');
        return $this->client->query($query_ppp_secret)->read();
    }
    /**
     * secret print for ppp
     *
     * @return void
     */
    public function activeConnectionUser()
    {
        $query_ppp_active_user = new Query('/ppp/active/print', array("count-only" => "",));
        return $this->client->query($query_ppp_active_user)->read();
    }
    /**
     * secret print for ppp
     *
     * @return void
     */
    public function allUserByMiktoTik()
    {
        $query_ppp_deactive_user = (new Query("/ppp/active/print"));
        return $this->client->query($query_ppp_deactive_user)->read();
        // totaluser e ppc secreat print 
    }
    /**
     * secret print for ppp
     *
     * @return void
     */
    public function getallmikrotikusers()
    {
        $query_ppp_deactive_user = (new Query("/ppp/secret/print"));
        return $this->client->query($query_ppp_deactive_user)->read();
    }
    /**
     * secret print for ppp
     *
     * @return void
     */
    public function getallmikrotikOnlineusers()
    {
        $query_ppp_deactive_user = (new Query("/ppp/active/print"));
        return $this->client->query($query_ppp_deactive_user)->read();
    }


    /**
     * get desabled status
     * in secret print for ppp
     *
     * @return void
     */
    public function getTotalOfflineMikroTikUsers()
    {
        $query_ppp_deactive_user = (new Query("/ppp/secret/print"))->where('disabled', 'true');
        return $this->client->query($query_ppp_deactive_user)->read();
    }


    /**
     *Disconnect User form ppp active users
     *
     * @return void
     */



    // $query_ppp_deactive_user = new Query("/interface/pppoe-server/print")->where('name', 'pppoe-user01');
    // $user =  $this->client->query($query_ppp_deactive_user)->read();
    // $data =   $user[0]['.id'];
    // $deactive_user = new Query("/interface/pppoe-server/remove")->equal('numbers', $data);
    // return $this->client->query($deactive_user)->read();

    public function disconnectConnectedUser($username)
    {

        // $query_ppp_deactive_user = new Query("/ppp/secret/print/count-only/where/disabled");
        // $query_ppp_deactive_user = (new Query("/ppp/secret/print"))->where('disabled', 'true');

        // $query_ppp_deactive_user = (new Query("/interface/monitor-traffic"))->equal('interface', 'pppoe-onm57')->equal('once', '');
        // return $this->client->query($query_ppp_deactive_user)->read();
        // $query_ppp_deactive_user = (new Query("  <pppoe-onm57>"));

        $query_ppp_deactive_user = (new Query("/interface/pppoe-server/print"))->where('name', "<pppoe-$username>");
        $user = $this->client->query($query_ppp_deactive_user)->read();
        if ($user) {
            $rmUser = (new Query('/interface/pppoe-server/remove'))->equal('numbers', $user[0]['.id']);
            $this->client->query($rmUser)->read();
        }
    }
}
