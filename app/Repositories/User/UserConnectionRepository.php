<?php

namespace App\Repositories\User;

use App\Models\Models\Mikrotik;
use App\Models\UserConnectionInfo;
use App\Services\ConnectionService;
use Exception;

class UserConnectionRepository
{
    private $model;

    public function __construct(UserConnectionInfo $connection_info)
    {
        $this->model = $connection_info;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }
    public function update($data, $id)
    {
        return  $this->model->where('user_id', $id)->update($data);
    }
}
