<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Users;


class AdminController extends Controller
{
    ////    معرفة عدد المستخدمين للتطبيق
    public function Userscounts(Request $req)
    {
        $users = DB::table('users')->count();
        return response()->json([
            'status' => 'success' ,
            'users count'=> $users
        ],200);
    }
    
}
