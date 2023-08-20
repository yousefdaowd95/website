<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcatigory;
use App\Models\Catigory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class SubcatigoryController extends Controller
{
    // لاضافة قائمة من المشتريات لنوع معين
    public function AddSubcatigory(Request $req)
    {
        $rules=array(
            'name'=>'required',
            'image'=>'required',
            'catigory_id'=>'required'
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure',
                'message'=> $validator->errors()],404);
        }
        $name=Subcatigory::where('name',$req['name'])->first();
        if($name)
        {
            return response()->json([
                'status' => 'failure',
                'message'=>'the Subcatigory is finded'],404);
        }
        if($req['catigory_id']){
            $Catigory=Catigory::where('id',$req['catigory_id'])->first();
            if(!$Catigory)
            return response()->json([
                'status' => 'failure',
                'message'=>'the catigory not found'],404);
        }
        if($req->has('image')){
        $photo = $req['image'];
        $newphoto=$photo->getClientOriginalName();
        $photo->move('uploads/subcatigory/',$newphoto);
        $Subcatigory= new Subcatigory();
        $Subcatigory['name']=$req['name'];
        $Subcatigory['image']='uploads/subcatigory/'.$newphoto;
        $Subcatigory['catigory_id']=$req['catigory_id'];
        if($req['name_ar'])  $Subcatigory['name_ar'] =$req['name_ar'];
        $Subcatigory->save();
        return response()->json([
            'status' => 'success',
            'message'=>'Subcatigory added successfuly'],200);
        }
        else{
            return response()->json([
                'status' => 'failure',
                'message'=>'cannot added Subcatigory '],404);
        }
    }
    // لتعديل البيانات للقائمة 
    public function EditSubcatigory(Request $req,$id)
    {
        $Subcatigory = Subcatigory::find($id);
        if($Subcatigory){
        if($req['name']) $Subcatigory['name'] =$req['name'];
        if ($req->has('photo')) {
            $photo = $req['image'];
            $newphoto=$photo->getClientOriginalName();
            $photo->move('uploads/subcatigory/',$newphoto);
            $image->photo = 'uploads/subcatigory/'.$newphoto;
        }
        if($req['image'])  $Subcatigory['image']=$req['image'];
        $Subcatigory->save();
        return response()->json([
            'status' => 'success',
            'message'=> 'Subcatigory updated successfuly'],200);
         }
         return response()->json([
            'status' => 'failure',
            'message'=> 'Subcatigory  not exist'],404);
    }
    // لحذف القائمة 
    public function DeleteSubcatigory($id)
    {
        $Subcatigory = Subcatigory::find($id);
        if($Subcatigory){
            $Subcatigory->delete();
            return response()->json([
                'status' => 'success',
                'message'=> 'delete Subcatigory successfuly'],200);    
        }else{
            return response()->json([
                'status' => 'failure',
                'message'=> 'the Subcatigory not found'],404);
        }
    }
}
