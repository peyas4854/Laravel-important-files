<?php

namespace Horsefly\Http\Controllers;


use Horsefly\employee;
use Horsefly\user;
use Horsefly\department;
use Horsefly\desigtation;
use Horsefly\company_detail;
use Horsefly\bank_detail;
use Horsefly\document;
use Notification;
use Auth;
use Horsefly\Notifications\EmployeeAccountNotification;


use Illuminate\Http\Request;
use Horsefly\Http\Requests\employeeForm;
use Carbon\carbon;



class EmployeeController extends Controller
{

      public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $departments = department::all();

        //$desigtations = desigtation::all();

        return view('/employee/view',compact('departments','desigtations'));
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
     * @param  \Illuminate\Http\Request  $request  employeeForm
     * @return \Illuminate\Http\Response
     */
    public function store(employeeForm $request)
    {

       
        
    $random_password = str_random(4);
       
    $user_id = user::create([
    'name'=>$request->employee_name,
    'email'=>$request->employee_email,
    'password'=>bcrypt($random_password)

       ]);
    //echo " $user_id->id";


    $employee_id=employee::insertGetId([
    'user_id'=>$user_id->id,
    'employee_father_name'=>$request->employee_father_name,
    'employee_dob'=>$request->employee_dob,
    'employee_gender'=>$request->employee_gender,
    'employee_phone_no'=>$request->employee_phone_no,
    'employee_present_address'=>$request->employee_present_address,
    'employee_permanent_address'=>$request->employee_permanent_address,
     "created_at"=>Carbon::now()
]);

 
 
 company_detail::insert([
    'employee_id'=>$employee_id,
    'employee_no'=>$request->employee_no,
    'department_id'=>$request->department_id,
    
    'date_of_joning'=>$request->date_of_joning,
    'Joining_salary'=>$request->Joining_salary,
    "created_at"=>Carbon::now()
 ]);

 bank_detail::insert([
    'employee_id'=>$employee_id,
    'account_holder_name'=>$request->account_holder_name,
    'account_number'=>$request->account_number,
    'bank_name'=>$request->bank_name,
    'ific_number'=>$request->ific_number,
    'pan_number'=>$request->pan_number,
    'branch'=>$request->branch,
    "created_at"=>Carbon::now()
 ]);
    if(!empty($request->document_file)){
    foreach ($request->document_file as $file_name => $file) {
        
        $path = $file->store('documents');

        document::insert([
        'employee_id'=>$employee_id,
        'file_name'=>$file_name,
        'file_location'=>$path,
        "created_at"=>Carbon::now()
        ]);
        
    }
   

}
    

                       
Notification::route('mail', $request->employee_email)->notify(new EmployeeAccountNotification($random_password));



return back()->withStatus(' Successfully added ');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(employee $employee)
    {
        //
    }

    public function getDesignation_list(Request $request)
    {
       


       //   $getdesigtation = desigtation::where('department_id',$request->department_id)->get();
        

       //  foreach ($getdesigtation as  $value) {
       //      echo $value;
       //  } 
        $string_to_send='';
        $getdesigtation = desigtation::where('department_id','=',$request->department_id)->get();

        foreach ($getdesigtation as $get) {
           
            $string_to_send="<option value='$get->id'>$get->designation_name</option>";
        }
       

      
       
        
        


    }
}
 