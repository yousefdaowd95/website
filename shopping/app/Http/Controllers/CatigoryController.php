<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catigory;
use App\Models\Subcatigory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CatigoryController extends Controller
{
    // لاضافة قائمة من نوع المشتريات 
   public function AddCatigory(Request $req){
    $rules=array(
        'name'=>'required',
        'image'=>'required',
    ); 
    $validator=Validator::make($req->all(),$rules);
    if($validator->fails())
    {
        return response()->json([
            'status' => 'failure',
            'message'=> $validator->errors()],404);
    }
    $name=Catigory::where('name',$req->name)->first();
    if($name)
    {
        return response()->json([
            'status' => 'failure',
            'message'=>'the catigory is finded'],404);
    }
    if($req->has('image')){
    $photo = $req['image'];
    $newphoto=$photo->getClientOriginalName();
    $photo->move('uploads/photos',$newphoto);
    $Catigory= new Catigory();
    $Catigory['name']=$req['name'];
    $Catigory['image']='uploads/photos/'.$newphoto;
    if($req['name_ar'])$Catigory['name_ar']=$req['name_ar'];
    $Catigory->save();
    return response()->json([
        'status' => 'success',
        'message'=>'catigory added successfuly'],200);
    }
    else{
        return response()->json([
            'status' => 'failure',
            'message'=>'cannot added catigory '],404);
    }
   }
   // لتعديل بيانات نوع قائمة المشتريات
   public function EditCatigory(Request $req,$id)
    {
        $Catigory = Catigory::find($id); 
        if($Catigory){
        if ($req->has('image')) {
            $photo = $req['image'];
            $newphoto=$photo->getClientOriginalName();
            $photo->move('uploads/photos',$newphoto);
            $Catigory['image'] ='uploads/photos/'.$newphoto;
        }
        if($req['name']) $Catigory['name'] =$req['name'];
        if($req['image']) $Catigory['image']=$req['image'];
        if($req['image']) $Catigory['name_ar']=$req['name_ar'];
        $Catigory->save();
        return response()->json([
            'status' => 'success',
            'message'=> 'catigory updated successfuly'],200);
         }else
         return response()->json([
            'status' => 'failure',
            'message'=> 'catigory  not exist'],404);

    }
    // لحذف القائمة 
    public function DeleteCatigory($id)
    {
        $Catigory = Catigory::find($id);
        if($Catigory){
            $Catigory->delete();
            return response()->json([
                'status' => 'success',
                'message'=> 'delete catigory successfuly'],200);    
        }else{
            return response()->json([
                'status' => 'failure',
                'message'=> 'the catigory not found'],404);
        }
    }
}
