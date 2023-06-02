<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\InterfaceController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Requests\User\NewCustomerRegistrationRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Repositories\User\UserRepository;
use App\Services\User\UserBillingService;
use App\Services\User\UserConnectionService;
use App\Services\User\UserDetailsService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerBalanceHistory;
use App\Models\CustomersGrace;
use App\Models\Invoice;
use App\Models\Models\Mikrotik;
use App\Models\PppUser;
use App\Models\Settings\AdminSetting;
use App\Models\Softwaresystem;
use App\Models\User;
use App\Models\User\UserBillingInfo;
use App\Models\User\UserConnectionInfo;
use App\Models\UserImportModel;
use App\Observers\ActionObserver;
use App\Services\ConnectionService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    private $user_service;
    private $user_details_service;
    private $user_billing_service;
    private $user_connection_service;
    private $user_repo;
    private $interfaceController;
    public $query_data = '';
    // <=== user permission 
    public $user;
    // user permission ===>
    public function __construct(
        UserService           $user_service,
        UserDetailsService    $user_details_service,
        UserBillingService    $user_billing_service,
        UserRepository    $user_repository,
        UserConnectionService $user_connection_service,
        QueueController  $queueController,
        InterfaceController   $interfaceController
    ) {

        $this->user_service = $user_service;
        $this->user_details_service = $user_details_service;
        $this->user_billing_service = $user_billing_service;
        $this->user_connection_service = $user_connection_service;
        $this->user_repo = $user_repository;
        $this->queueController = $queueController;
        $this->interfaceController = $interfaceController;
        // actions observer
        $this->actionObserver = new ActionObserver;
        // <=== user permission 
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('api')->user();
            return $next($request);
        });
        // user permission ===>
    }

    /**
     * Uploads the records in a csv file or excel using maatwebsite package 
     *
     * @param Request $request
     * @return mixed
     */
    public function uploadContentWithPackage(Request $request)
    {
        if ($request->file) {
            $file = $request->file;
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes
            //Checks to see that the valid file types and sizes were uploaded
            $this->checkUploadedFileProperties($extension, $fileSize);
            $import = new PlayersImport();
            Excel::import($import, $request->file);
            foreach ($import->data as $user) {
                //sends email to all users
                $this->sendEmail($user->email, $user->name);
            }
            //Return a success response with the number if records uploaded
            return response()->json([
                'message' => $import->data->count() . " records successfully uploaded"
            ]);
        } else {
            throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * import customer csv file.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerImport(Request $request)
    {
        try {
            if ($this->user->hasPermissionTo('user_import_customer', 'api')) {
                DB::beginTransaction();
                if (!$request->file)  return error_message('Please Input .csv File');
                $file = $request->file('file');
                // File Details 
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                // Valid File Extensions
                $valid_extension = array("csv");
                // 2MB in Bytes
                $maxFileSize = 2097152;

                // Check file extension
                if (in_array(strtolower($extension), $valid_extension)) {
                    // Check file size
                    if ($fileSize <= $maxFileSize) {

                        // File upload location
                        $location = 'uploads';

                        // Upload file
                        $file->move($location, $filename);

                        // Import CSV to Database
                        $filepath = public_path($location . "/" . $filename);

                        // Reading file
                        $file = fopen($filepath, "r");

                        $importData_arr = array();
                        $i = 0;
                        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                            $num = count($filedata);
                            // Skip first row (Remove below comment if you want to skip the first row)
                            if ($i == 0) {
                                $i++;
                                continue;
                            }
                            for ($c = 0; $c < $num; $c++) {
                                $importData_arr[$i][] = $filedata[$c];
                            }
                            $i++;
                        }
                        fclose($file);
                        // Insert to MySQL database
                        foreach ($importData_arr as $row) {
                            $insertData = array(
                                'Name'              => $row[0],
                                'Mobile'            => $row[1],
                                'Email'             => $row[2],
                                'Nationalid'        => $row[2],
                                'Address'           => $row[4],
                                'Zone'              => $row[5],
                                'Date_of_birth'     => $row[6],
                                'Connctinon_type'   => $row[7],
                                'Payment_type'      => $row[8],
                                'Billing_status'    => $row[9],
                                'Customer_type'     => $row[10],
                                'Package_id'        => $row[11],
                                'Package_price'     => $row[12],
                                'Package_discount'  => $row[13],
                                'Monthly_bill'      => $row[14],
                                'connection_date'   => $row[15],
                                'Expire_date'       => $row[16],

                            );
                            UserImportModel::insert($insertData);
                        }
                    }
                }


                // Excel::import(new CustomersImport, $request->file);
                // $import = new UsersImport();
                // Excel::import($import, $request->file);
                DB::commit();
                return success_message("Customers file Import Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            ////=======handle DB exception error==========
            //            info('Database Exception Error', json_encode($e->getMessage()));
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($this->user->hasPermissionTo('Customer', 'api')) {
                //calling user repo to get data
                $customers = $this->user_repo->getUserByType($request);
                //            $customer_resources = new UserResource($customers);
                return success_message("All User Details", $customers);
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            //            info('Database Exception Error', json_encode($e->getMessage()));
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    // newCustomerRegistration

    public function newCustomerRegistration(NewCustomerRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($this->user->hasPermissionTo('Customer Add', 'api')) {
                $user = $this->user_service->create($request);
                $this->user_details_service->create($request, $user);
                $this->user_billing_service->create($request, $user);
                $this->create_invoice($request, $user);
                $this->actionObserver->createAction(Auth::user()->name . ' Create New Customer', Auth::user()->name, 'success');
                activity()
                    ->event('Update status')
                    ->log('Registration New Customer');

                DB::commit();
                return success_message("New user Create Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
    // newCustomerRegistration for all stape
    public function store(UserCreateRequest $request)
    {

        $validator = Validator::make($request->all(), [
            'user_type' => 'required',
        ]);

        ///=======when validation faild================
        if ($validator->fails()) {
            return error_message('Validation Error', $validator->errors()->all(), 422);
        }
        try {
            DB::beginTransaction();
            if ($this->user->hasPermissionTo('Customer Add', 'api')) {
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
                $user = $this->user_service->create($request);
                $this->user_details_service->create($request, $user);
                $this->user_billing_service->create($request, $user);
                // create_invoice
                $this->create_invoice($request, $user);
                // create_invoice
                // add user in mikrotik 
                $query_data = '';
                try {
                    $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
                    if (empty($mikrotik)) return error_message('Mikrotik not found');
                    //create client
                    $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
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
                if (isset($request->import_item_id) && $request->$request->import_item_id !== null) {
                    UserImportModel::where('id', $request->import_item_id)->delete();
                }
                $this->actionObserver->createAction(Auth::user()->name . ' Create New Customer', Auth::user()->name, 'success');
                DB::commit();
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    // create_invoice method
    public function create_invoice($request, $user)
    {
        $lastData =   Invoice::where('user_id', $user->id)->latest()->first();
        if (!$lastData) {
            $invoice_data = [
                'user_id' => $user->id,
                'invoice_no' => "INV-{$user->id}-" . date('m-d-H'),
                'package_id' => $request->package,
                'zone_id' => $request->zone_id,
                'expire_date' => now()->addDays(30),
                'amount' => $request->total_invoice_price,
                'advanced_amount' => 0,
                'month' => date('m'),
                // 'last_payment_date' => isset($lastData) ?? $lastData->created_at,
                'last_payment_date' => $user->created_at,
                'status' => 'Pending',
                'comment' => 'new_user'
            ];
            $invoice = Invoice::create($invoice_data);
            activity()
                ->event('Update status')
                ->log('Create new Invoice');
        }
    }





    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function update_user_balance(Request $request, $id)
    {

        if (empty($request->balance))  return error_message('Validation Error', "Please Enter Balance");
        try {
            if ($this->user->hasPermissionTo('user_update_blance', 'api')) {
                $billing_info =  UserBillingInfo::where('user_id', $id)->first();
                $balance = $request->balance_type == "Add" ? $billing_info->balance += $request->balance : $request->balance;
                $reasons = $request->balance_type == 'Update' && $request->reason !== '' ? $request->reason : '';
                $billing_info->update([
                    'balance' =>   $balance,
                ]);
                CustomerBalanceHistory::create([
                    'users_id' => $id,
                    'balance'  => $balance,
                    'update_Reasons' => $reasons,
                    'admin_id' => Auth::user()->id,
                ]);
                activity()->event('Update status')->performedOn($billing_info)->log('Update User Balance');
                return success_message("Balance Update Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return error_message('Database Exception Error', $th->getMessage(), $th->getCode());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * @OA\Get(
     * path="/api/v1/customer/{userId}",
     * summary="Get User Details",
     * description="Get User Details",
     * operationId="GetUserDetails",
     * tags={"UserDetails"},
     * security={{"passport": {}}},
     * 
     * @OA\Parameter(
     *    description="ID of User",
     *    in="path",
     *    name="userId",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * 
     * @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            //calling user repo to get data

            $customers = $this->user_repo->findId($id);
            //$customer_resources = new UserResource($customers);
            return success_message("get User by id", $customers);
        } catch (Exception $e) {
            ////=======handle DB exception error==========
            // info('Database Exception Error', json_encode($e->getMessage()));
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
        // return $request->all();

        $validator = Validator::make($request->all(), [
            'user_type' => 'required',
        ]);

        ///=======when validation faild================
        if ($validator->fails()) {
            return error_message('Validation Error', $validator->errors()->all(), 422);
        }
        try {
            DB::beginTransaction();
            if ($this->user->hasPermissionTo('User', 'api')) {
                if ($request->ppp_user_id !== null) {
                    PppUser::where('id', $request->ppp_user_id)->update([
                        'user_status' => '0'
                    ]);
                    $user = $this->user_service->create($request);
                    $this->user_details_service->create($request, $user);
                    $this->user_billing_service->create($request, $user);
                    $this->user_connection_service->create($request, $user, $request->expire_date);
                    if (isset($request->expire_date) && isset($request->update) && $request->update == 'mikrotik-user') {
                        $invData =  AdminSetting::select('slug', 'value')->where('slug', 'create_invoice_days')->first();
                        if (!$invData) return error_message('Invoice Generate Date Not set Please Set First');
                        $in_g_day =  Carbon::now()->addDay($invData->create_invoice_days == '' ? '3' : $invData->create_invoice_days)->format('d-m-Y H:i:s A');
                        $today =  Carbon::now()->format('d-m-Y H:i:s A');
                        $expire_day =  Carbon::parse($request->expire_date)->format('d-m-Y H:i:s A');
                        if ($today <= $expire_day && $in_g_day > $expire_day) {
                            $this->create_invoice($request, $user);
                        }
                    }
                    // create new invoice 
                } else {
                    $user = $this->user_service->update($request, $id);
                    $this->user_details_service->update($request, $id);
                    $this->user_billing_service->update($request, $id);
                    //update in mikrotik
                    try {
                        $connection_service = UserConnectionInfo::where('user_id', $id)->first();
                        if (empty($connection_service)) return error_message('Data Not Found');
                        $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
                        if (empty($mikrotik)) return error_message('Mikrotik not found');
                        //create client
                        $connection =  new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                        $connectionData =  $connection->updateUserToMikrotik($request, $connection_service, $request->expire_date);
                        if (empty($connectionData)) return error_message('User Not Found To Update In Mikrotik');
                    } catch (Exception $e) {
                        ////=======handle DB exception error==========
                        return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
                    }
                    $this->user_connection_service->update($request, $id);
                }
                $this->actionObserver->createAction(Auth::user()->name . " Update User Details", Auth::user()->name, 'success');
                DB::commit();
                return success_message("User Update Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * customer_allow_grace
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customer_allow_grace(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->user->hasPermissionTo('User', 'api')) {
                if ($request->grace > Auth::user()->allow_grace_mark) return error_message("Invalid Input");
                $getlastData =  CustomersGrace::where('user_id', $request->id)->latest()->first();
                // if ($this->user->hasPermissionTo('Allow Customer Grace', 'api')) {
                if ($this->user->hasPermissionTo('NetWork', 'api')) {
                    CustomersGrace::create([
                        'user_id' => $request->id,
                        'manager_id' => Auth::user()->id,
                        'expire_date' => $request->expire_date,
                        'grace' => $request->grace,
                    ]);
                    return success_message("Grace Allow Successfully");
                } elseif (!$this->user->hasPermissionTo('Pop', 'api') && $getlastData->expire_date == $request->expire_date) {
                    return error_message("Grace Allowed Already Added in $getlastData->grace days");
                }
                //update in mikrotik
                try {
                    $connection_info = UserConnectionInfo::where('user_id', $request->id)->first();
                    if (empty($connection_info)) return error_message('Data Not Found');
                    $mikrotik = Mikrotik::where('id', $connection_info->mikrotik_id)->first();
                    if (empty($mikrotik)) return error_message('Mikrotik not found');
                    //create client
                    $connection =  new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                    $connectionData =  $connection->activeDisconnectedUser($connection_info->user_id,  $connection_info->username);
                    if (empty($connectionData)) return error_message('User Not Found To Update In Mikrotik');
                } catch (Exception $e) {
                    ////=======handle DB exception error==========
                    return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
                }

                User::where('id', $request->id)->update(['allow_grace_mark' =>  Carbon::now()->addDay($request->grace)->format('d-m-Y H:i:s A')]);
                $c_grace = CustomersGrace::create([
                    'user_id' => $request->id,
                    'manager_id' => Auth::user()->id,
                    'expire_date' => $request->expire_date,
                    'grace' => $request->grace,
                ]);
                activity()->event('Update status')->performedOn($c_grace)->log(' Allow Customer Grace');
                DB::commit();
                return success_message("Grace Allowed Successfully");
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
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


    public function customerSummary(Request $request)
    {
        $summary = $this->user_repo->getSummary($request);
        return success_message("Summary", $summary);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // mac Bind 
    public function mac_bind(Request $request)
    {
        try {
            if ($this->user->hasPermissionTo('User', 'api')) {
                $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
                if (empty($mikrotik)) return error_message('Mikrotik not found');
                //create client
                $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                $user_info =  UserConnectionInfo::where('user_id', $request->itemid)->first();
                return $query_ppp_pool = $connection->mac_bind($request->user_name);
                if ($user_info) {
                    if ($user_info->mac_bind_status == 1) {
                        $query_ppp_pool = $connection->mac_bind($request->user_name);
                        if (gettype($query_ppp_pool) == 'string') return error_message($query_ppp_pool);
                        foreach ($query_ppp_pool as $pppusers) {
                            // return $pppusers['caller-id'];
                            if ($pppusers['caller-id']) {
                                $user_info->update([
                                    'mac_bind_status' => '1',
                                    'mac_address' => $pppusers['caller-id'],
                                ]);
                            } else {
                                return error_message("Something Wrong to Mac Bind");
                                break;
                            }
                            $user_info->update([
                                'mac_bind_status' => '0',
                                'mac_address' => $pppusers['caller-id'],
                            ]);
                        }
                        return success_message("Mac Bind Succssfully", $user_info);
                    } else {
                        $query_ppp_pool = $connection->mac_unbind($request->user_name);
                        if (gettype($query_ppp_pool) == 'string') return error_message($query_ppp_pool);
                        foreach ($query_ppp_pool as $pppusers) {
                            if ($pppusers['caller-id']) {
                                $user_info->update([
                                    'mac_bind_status' => '1',
                                    'mac_address' => $pppusers['caller-id'],
                                ]);
                            } else {
                                $user_info->update([
                                    'mac_bind_status' => '1',
                                    'mac_address' => '',
                                ]);
                            }
                        }
                        return success_message("Mac Unbind Succssfully", $user_info);
                    }
                    activity()->event('Mac Bind')->performedOn($user_info)->log(' Change Mac bind ');
                } else {
                    return error_message('Database Exception Error');
                }
            } else {
                return error_message('You Have No Access Permission', 'Unauthorize permissons', 403);
            }
        } catch (Exception $e) {
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
    //mikrotikuserstatus 
    public static function mikrotikuserstatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_info =  UserConnectionInfo::where('user_id', $request->itemid)->first();
            $billing_info =  UserBillingInfo::where('user_id', $request->itemid)->first();

            if ($user_info->status == 0) {
                if ($billing_info->last_payment_date == '') {
                    return error_message('Something want wrong', 'Please Check Invoice');
                } else if ($billing_info->balance < $billing_info->monthly_bill) {
                    return error_message('Something want wrong', 'Please Check Balance');
                } else if (Carbon::parse($billing_info->last_payment_date)->format('m/d/Y') > Carbon::parse($user_info->expire_date)->format('m/d/Y')) {
                    // $billing_info->update([
                    //     'balance' =>   $billing_info->balance -= $billing_info->monthly_bill,
                    // ]);
                }
            }

            try {
                $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
                if (empty($mikrotik)) return error_message('Mikrotik not found');
                //create client
                $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
                if ($user_info) {
                    $query_data = $connection->mikrotik_user_change_status($user_info->username, $user_info->status);
                    if (gettype($query_data) == 'string') return error_message($query_data);
                    $user_info->update([
                        'status' => $query_data[0]['disabled'] == 'true' ? 0 : 1,
                    ]);

                    activity()->event('Update status')->performedOn($user_info)->log(' Change Mikrotik status ');

                    DB::commit();

                    return success_message("Status Changed Succssfully", $user_info);
                } else {
                    DB::rollBack();
                    return error_message('Database Exception Error');
                }
            } catch (Exception $e) {
                DB::rollBack();
                return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
            }
        } catch (Exception $e) {
            DB::rollBack();
            info(json_encode($e->getMessage()));
            return ['success' => false, 'message' => 'Something went wrong!'];
        }
    }
    //mikrotikuserstatus 
    public function changeUserProfile($request)
    {
        $system_profile = Softwaresystem::select('disconnected_package')->first();
        try {
            $mikrotik = Mikrotik::where('id', $request->mikrotik)->first();
            if (empty($mikrotik)) return error_message('Mikrotik not found');
            //create client
            $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
            $user_info =  UserConnectionInfo::where('user_id', $request->itemid)->first();
            $connection->chnageUserProfile($request->itemid, $request->username, $system_profile->disconnected_package);
            if ($user_info) {
                $query_data = $connection->mikrotik_user_change_status($user_info->username, $user_info->status);
                if (gettype($query_data) == 'string') return error_message($query_data);
                foreach ($query_data as $query_item) {
                    $user_info->update([
                        'status' => $query_item['disabled'] == 'true' ? 0 : 1,
                    ]);
                }
                activity()->event('Update status')->performedOn($user_info)->log(' Change User Package ');
                return success_message("Changed Succssfully", $user_info);
            } else {
                return error_message('Database Exception Error');
            }
        } catch (Exception $e) {
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
}
