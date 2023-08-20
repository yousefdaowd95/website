<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Subcatigory;

class ProductController extends Controller
{
    //   لاضافة منتج  
    public function AddProduct(Request $req){
        $rules=array(
            'name'=>'required',
            'size'=>'required',
            'price'=>'required',
            'description'=>'required',
            'image'=>'required',
            'subcatigory_id'=>'required',
        ); 
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure',
                'message'=> $validator->errors()],404);
        }
        if ($req->has('image')) {
        $photo = $req['image'];
        $newphoto=$photo->getClientOriginalName();
        $photo->move('uploads/products',$newphoto);
        $product=new Product();
        $product['image']='uploads/products/'.$newphoto;
        $product['name']=$req['name'];
        $product['size']=$req['size'];
        $product['price']=$req['price'];
        $product['size']=$req['size'];
        $product['description']=$req['description'];
        if($req['subcatigory_id'])
        {
            $Subcatigory = Subcatigory::where('id',$req['subcatigory_id'])->first();
            if(!($Subcatigory))
            return response()->json([
                'status' => 'failure',
                'message'=>'the subcatigory not found'],404);
        }
        $product['subcatigory_id']=$req['subcatigory_id'];
        if($req['name_ar']) $product['name_ar']=$req['name_ar'];
        if($req['description_ar'])$product['description_ar']=$req['description_ar'];
        $product->save();
        return response()->json([
            'status' => 'success',
            'message' =>'add product successfuly'],200);
        }
    }
    // للتعديل على بيانات منتج
    public function EditProduct(Request $req,$id)
    {
        $Product = Product::find($id);
        if($Product){
        if($req['name']) $Product['name'] = $req['name'];
        if($req['description)'])  $Product['description'] = $req['description'];
        if($req['image'])  $Product['description'] = $req['description'];
        if($req['size'])  $Product['size'] = $req['size'];
        if($req['price']){
            $Product['price'] = $req['price'];
            $Product['discount']=0;
        }    
        if ($req->has('image')) {
        $photo = $req['image'];
        $newphoto=$photo->getClientOriginalName();
        $photo->move('uploads/products',$newphoto);
        }
        if($req['image'])  $Product['image']='uploads/products/'.$newphoto;
        $Product->save();
        return response()->json([
            'status' => 'success',
            'message'=> 'Product updated successfuly'],200);
         }
         return response()->json([
            'status' => 'failure',
            'message'=> 'Product  not exist'],404);

    }
    // لحذف منتج 
    public function DeleteProduct($id)
    {
        $Product = Product::find($id);
        if($Product){
            $Product->delete();
            return response()->json([
                'status' => 'success',
                'message'=> 'delete Product successfuly'],200);    
        }else{
            return response()->json([
                'status' => 'failure',
                'message'=> 'the product not found '],404);
        }
    }
    // لاضافة تقييم على المنتج 
    public function AddRating(Request $req , $id)
    {
        if($req['rating'] < 6 && $req['rating'] >0)
        {
            $product= Product::where('id',$id)->first();
            if($product)
            {
                $new = ($product['rating'] + $req['rating'])/2;
                $product['rating'] = $new ;
                $product->save();  
                return response()->json([
                    'status' => 'success' ,
                    'message'=>'Rating product seccussfuly '
                ],404);
            }
            else
            {
                return response()->json([
                    'status' => 'failure' ,
                    'message'=>' product not exist '
                ],404);
            }
        }
        else
        {
            return response()->json([
                'status' => 'failure' ,
                'message'=>'Rating product between 1 to 5 '
            ],404);
        }
    }
    // لاضافة حسم على منتج 
    public function Discount(Request $req , $id)
    {
        $product =product::where('id',$id)->first();
        if($product){
        $rules=array(
            'discount'=>'required|numeric|max:100|min:1',
        ); 
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure',
                'message'=> $validator->errors()],404);
        }
        if(($req['discount'] < 100) && ($req['discount'] > 0) ){
        $dis=$product['price'] -((($product['price']) * ($req['discount']/100)));
        $product['discount']=$req['discount'];
        $product['price']=$dis;
        $product->save();
        return response()->json([
            'status' => 'success',
            'message'=> 'add discount successfuly'],200);
        }else
        {
            return response()->json([
                'status' => 'failure',
                'message'=> ' attribute not found '],404);
        }
    }  
}
}