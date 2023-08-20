<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Token;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use App\Mail\SendCodeRegister;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    // لانشاء حساب في التطبيق
    public function regiser(Request $req)
    {
        $rules=array(
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8',
            'phone'=>'required|min:10',
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()]
            ,400);
        }
        $email=User::where('email',$req->email)->first();
        if($email)
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=>'Invalid Email'
            ],401);
        }
        $data['code']=mt_rand(100000,999999);
        $user=new User();
        $user['name']=$req['name'];
        $user['email']=$req['email'];
        $user['password']=Hash::make($req['password']);
        $user['phone']=$req['phone'];
        $user['verfication_code']=$data['code'];
        $user->save();
        // send email to user
        if($req['email'] === 'Admin@gmail.com')
        {
            return response()->json([
                'status' => 'success',
                'message'=>'Admin successfuly']
            ,200);
        }else{
           $user= User::where('email',$req->email)->first();
            Mail::to($req['email'])->send(new SendCodeRegister($data['code']));
            return response()->json([
                'status' => 'success' ,
                'message'=>'sent code to email user',
                'data' => $user
            ],200);
        }
    }
    // للتحقق من الكود الذي تم ارساله الى المسنخدم 
    public function checkcode(Request $req)
    {
        $rules=array(
            'verfication_code' => ['required','string','exists:users'],
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()
            ],400);
        }
        $user =User::where('verfication_code',$req['verfication_code'])->first();
        if($user){
            return response()->json([
                'status' => 'success' ,
                'message'=>'user create successfuly']
                ,200);
        }
        else{
            return response()->json([
                'status' => 'failure' ,
                'message'=>'incorrect code'
            ],404);
        }
        
    }
    // لتسجيل دخول الى التطبيق
    public function login(Request $req)
    {
        $rules=array(
            'email'=>'required|email',
            'password'=>'required|min:8',
        ); 
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()
            ],400);
        }
    $check = user::where('email',$req['email'])->first();
        if(!$check)
        {
            return response()->json([
                'status' => 'failure' ,
                'message' =>'uncorrect email'
            ],400);
        }
        $checkpassword=Hash::check($req->password,$check->password);
        if(!$checkpassword)
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=>'uncorrect password'
            ],400);
        }
        $data=["email"=>$check['email'],
        "id" =>$check['id'],
        "rule"=>"User",
        'name' =>$check['name']
        ];
        if($check->email == "Admin@gmail.com")
            $data['rule']="Admin";
        $datajson= json_encode($data);
        $token =base64_encode($datajson);
        $Dtoken=new token();
        $Dtoken['user_id']=$check->id;
        $Dtoken['token']=$token;
        $Dtoken->save();
        return response()->json([
            'status' => 'success' ,
            'message'=>'successfuly login',
            'token'=>$token
        ],200);
    }
    // لتسجيل الخروج من التطبيق
    public function logout(Request $req)
    {
        $token=Token::where('token',$req->header('token'))->delete();
        if($token)
        return response()->json([
            'status' => 'success' ,
            'message'=>'successfuly logout'
        ],200);
        else
        return response()->json([
            'status' => 'failure' ,
            'message'=>'invalid logout'
        ],400);
    }
    // توابع لتغيير كلمة المرور لحساب المستخدم 
    public function UserForgetPassword(Request $req)
    {        
        $data=array(
            'email' =>['required','email','exists:users'],
        );
        $validator=Validator::make($req->all(),$data);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()
            ],404);
        }
        $data['email'] = $req->email;
        // delete all old code that user send before
        $email=ResetCodePassword::where('email',$req['email'])->first();
        if($email)   $email->delete();
        // generate random code
        $data['code']=mt_rand(100000,999999);
        //create new code
        $codedata =ResetCodePassword::create($data);
        // send email to user
        Mail::to($req['email'])->send(new SendCodeResetPassword($codedata['code']));
        return response()->json([
            'status' => 'success' ,
            'message' => trans('code.sent')
        ]);
    }
    public function UserCheckCode(Request $req)
    {
        $rules=array(
            'code' => ['required','string','exists:reset_code_passwords'],
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()
            ],404);
        }
        // find the code
        $passwordReset =ResetCodePassword::where('code',$req['code'])->first();
        // check if it is not expired the time is one hour
        if($passwordReset['created_at'] > now()->addHour()){
            $passwordReset->delete();
            return response()->json([
                'status' => 'failure' ,
                'message' => trans('password.code is expire')
            ],404);
        }
        return response()->json([
            'status' => 'success' ,
            'code' =>$passwordReset['code'],
            'message' => trans('password.code is valid')
        ]);
    }
    ///////////////////////////////////////////////
    public function UserResetPassword(Request $req)
    { 
        $input=array(
            'code' => ['required','string','exists:reset_code_passwords'],
            'password' => 'required'
        );
        $validator=Validator::make($req->all(),$input);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=> $validator->errors()],404);
        }
        $input['code'] = $req['code'];
        $input['password'] = $req['password'];
        // find code 
        $ResetCodePassword =ResetCodePassword::where('code',$req['code'])->first();
        //check if it is not expired:the time is one hour
        if($ResetCodePassword['created_at'] > now()->addHour()){
            $ResetCodePassword->delete();
            return response()->json([
                'status' => 'failure' ,
                'message' => trans('password.code is expire')],404);
        }
        // find users email
        $user= User::where('email',$ResetCodePassword['email'])->first();
        //update user password 
        $input['password']=Hash::make($input['password']);
        $user->update([
            'password' => $input['password'],
        ]);
        // delete current code 
        $ResetCodePassword->delete();
        return response()->json([
            'status' => 'success' ,
            'message' => 'password has been successfully reset']);   
    }
}
