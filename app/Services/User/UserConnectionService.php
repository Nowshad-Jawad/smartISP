<?php

namespace App\Services\User;

use App\Repositories\User\UserConnectionRepository;
use Carbon\Carbon;

use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isTrue;

class UserConnectionService
{
    private $connection_repo;

    public function __construct(UserConnectionRepository $user_connection_repository)
    {
        $this->connection_repo = $user_connection_repository;
    }

    public function create($user, $expire_date = '')
    {
        // $ex_day = $expire_date !== '' ?  Carbon::parse($expire_date)->format('d-m-Y H:i A') :  Carbon::now()->format('d-m-Y H:i A');
        $data = [
            // 'connection_type'  => $request->connection_type,
            'user_id'          => $user->id,
            'mikrotik_id'      => $user->mikrotik_id ?? NULL,
            'username'         => $user->username,
            'user_password'    => $user->password ?? NULL,
            'service'          => 'PPPoE',
            // 'remote_address'   => $request->remote_address ?? NULL,
            // 'mac_address'      => $request->mac_address,
            // 'remote_ip'        => $request->remote_ip ?? NULL,
            // 'router_component' => $request->router_component ?? NULL,
            'expire_date'      => $expire_date,
            // 'is_queue'         => $request->is_queue ?? 0,
            // 'status'           => isset($request->update) && $request->update == 'mikrotik-user' && $request->mikrotik_status == true ? STATUS_SUCCESS : STATUS_PENDING,
        ];
        return $this->connection_repo->create($data);
    }
    public function update($request, $id)
    {
        $data = [
            'connection_type'  => $request->connection_type,
            'user_id'          => $id,
            'mikrotik_id'      => $request->mikrotik ?? NULL,
            'user_password'    => $request->userpassword ?? NULL,
            'service'          => $request->service ?? 'PPPoE',
            'remote_address'   => $request->remote_address ?? NULL,
            'mac_address'      => $request->mac_address ?? Null,
            'remote_ip'        => $request->remote_ip ?? NULL,
            'router_component' => $request->router_component ?? NULL,
            'expire_date'      => $request->expire_date,
            'is_queue'         => $request->is_queue ?? NULL,
            'status'           => STATUS_PENDING
        ];
        return $this->connection_repo->update($data, $id);
    }
}
