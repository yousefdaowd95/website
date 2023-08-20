<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Attribute;
class AttributeController extends Controller
{
    /// لاضافة الخصائص المتوافرة للمنتج 
    public function AddAttribute(Request $req){
        $rules=array(
            'color'=>'required',
            'product_id'=>'required',
            'quaintity'=>'required',
        ); 
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure',
                'message'=> $validator->errors()],404);
        }
        $product = product::where('id',$req['product_id'])->first();
        if($product)
        {
            $attribute = new Attribute();
            $attribute['product_id'] = $req['product_id'];
            $attribute['color'] = $req['color'];
            $attribute['quaintity'] = $req['quaintity'];
            $attribute->save();
            return response()->json([
                'status' => 'success',
                'message'=> ' add Attribute successfuly '],200);
        }
        return response()->json([
            'status' => 'failure',
            'message'=> ' the information is incorrect '],404);  
    }
    /// لتعديل الخصائص
    public function EditAttribute(Request $req , $id)
    {  
            $attribute =Attribute::where('id',$id)->first();
            if($attribute)
            {
                if($req['product_id'])    $attribute['product_id'] = $req['product_id'];
                if($req['color'])    $attribute['color'] = $req['color'];
                if($req['quaintity']) $attribute['quaintity'] = $req['quaintity'];
                $attribute->save();
                return response()->json([
                    'status' => 'success',
                    'message'=> ' edit Attribute successfuly '],200);
            }
            return response()->json([
                'status' => 'failure',
                'message'=> ' the attribute not found '],404);  
    }
    // لحذف المنتج 
    public function DeleteAttribute($id)
    {
        $Attribute = Attribute::find($id);
        if($Attribute){
            $Attribute->delete();
            return response()->json([
                'status' => 'success',
                'message'=> 'delete Attribute successfuly'],200);    
        }else{
            return response()->json([
                'status' => 'failure',
                'message'=> 'the Attribute not found '],404);
        }
    }
    
}
