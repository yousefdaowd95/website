<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Catigory;
use App\Models\Attribute;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcatigory;
use App\Models\User;

class ViewController extends Controller
{
    /////////1
    public function ViewCatigory(){
        $Catigoryes=Catigory::all();
        if ($Catigoryes->count()==0){
            return response()->json([
                'status' => 'failure' ,
                'message'=> "Empty"],404
            );
        }else{
            return response()->json([
                'status' => 'success' ,
                'Catigoryes' => $Catigoryes],200);
        }
    }
    /////////2
    public function ViewSubCatigory($IdCatigory){
            $Subcatigoryes=Subcatigory::where('catigory_id',$IdCatigory)->get();
            if ($Subcatigoryes->count()>0)
            {
                return response()->json([
                    'status' => 'success' ,
                    'Subcatigoryes' => $Subcatigoryes],200);
            }else{
                return response()->json([
                    'status' => 'failure' ,
                    'message' => 'no subcatigoryes '],404);
            }
    }
    /////////3
    public function ViewProducts($IdSubCatigory)
    {
        $products=Product::where('subcatigory_id',$IdSubCatigory)->get();
        if($products->count() > 0)
        {
            return response()->json([
                'status' => 'success' ,
                'products' => $products
            ],200);
        }
        else
        {
            return response()->json([
                'status' => 'failure' ,
                'message' => 'not found products'
            ],404);
        }
    }
    /////////
    public function ProductInfo($Idproduct)
    {
        $product = product::where('id',$Idproduct)->get();
        $attribute = DB::table('attributes')
        ->select('attributes.quaintity','attributes.color')
        ->where('attributes.product_id','=',$Idproduct)
        ->get();
        return response()->json([
            'status' => 'success',
            'product' => $product,
            'attribute' => $attribute,
           ],200);
    }
    /////////
    public function HomePage(Request $req){
        //catigory
        $catigores =Catigory::all();
        //subcatigory
        $Subcatigoryes=Subcatigory::all();
        //porducts
        $products = Product::orderByDesc('rating')->take(5)->get();
        //discounts
        $discounts =product::where('discount','!=',0)->get();
             return response()->json([
                'status' => 'success' ,
                'catigores' => $catigores,
                'Subcatigoryes' => $Subcatigoryes,
                'highestproducts' => $products ,
                'discounts' =>$discounts
            ]);     
         }
}