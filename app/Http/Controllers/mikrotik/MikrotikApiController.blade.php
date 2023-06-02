<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Models\Mikrotik;
use App\Services\ConnectionService;
use Exception;
use Illuminate\Http\Request;

class MikrotikApiController extends Controller
{
    public function mikrotikApiRequest(Request $request)
    {
        try {
            $mikrotik = Mikrotik::select(['id', 'host', 'username', 'password', 'port'])->find($request->mikrotik);
            if (empty($mikrotik)) return error_message('Mikrotik not found');
            //create client
            $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
            return $connection->check_milrotiok_user_status($request->username);
        } catch (Exception $e) {
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());

            //throw $th;
        }
    }
}
