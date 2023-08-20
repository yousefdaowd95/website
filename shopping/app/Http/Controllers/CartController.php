<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartView;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function Add(Request $req)
    {
        $token=$req['token'];
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $product = product::where('id',$req['product_id'])->first();
        $attribute = Attribute::where('color',$req['color'])
        ->where('product_id',$req['product_id'])
        ->first();
        if($product && $attribute)
        {
            if($attribute['quaintity'] >= $req['quaintity']) 
            {
                $cart = cart::where('product_id',$req['product_id'])
                ->where('color',$req['color'])
                ->where('order',0)
                ->first();
                if($cart)
                {
                    if(($attribute['quaintity'] >= $req['quaintity'])&&($attribute['quaintity'] >= $req['quaintity']+$cart['quaintity']))
                    {
                        $cart['quaintity'] = $cart['quaintity']+$req['quaintity'];
                        $cart['total']  = ($cart['quaintity'])*$product['price'];
                        $cart->save();
                        return response()->json([
                            'status' => 'success',
                            'message'=> 'add product to Cart'
                            ],200);
                    }else
                    {
                        return response()->json([
                            'status' => 'failure',
                            'message'=> 'quaintity not enough'
                            ],404);
                    }
                }else{
                    $cart = new Cart();
                    $cart['product_id']  = $req['product_id'];
                    $cart['quaintity']  = $req['quaintity'];
                    $cart['price']  = $product['price'];
                    $cart['color']  = $req['color'];
                    $cart['total']  = $req['quaintity']*$product['price'];
                    $cart['user_id']  = $data->id;
                    $cart->save();
                    return response()->json([
                    'status' => 'success',
                    'message'=> 'add product to Cart'
                    ],200);
                }
                
            }
            return response()->json([
                'status' => 'failure',
                'message'=> 'quaintity not enough'
                ],404);
           
        }
        return response()->json([
            'status' => 'failure',
            'message'=> 'product not found'
            ],404);
        
    }
    public function Remove(Request $req)
    {
        $token=$req['token'];
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $product = product::where('id',$req['product_id'])->first();
        $cart = cart::where('product_id',$req['product_id'])
        ->where('color',$req['color'])
        ->where('order',0)
        ->first();
        if($cart && ($cart['user_id'] == $data->id) && $product)
        {
            if($cart['quaintity'] >= $req['quaintity'])
            {
                $cart['quaintity'] = $cart['quaintity']-$req['quaintity'];
                $cart['total']  = $cart['quaintity']*$cart['price'];
                $cart->save();
                if($cart['quaintity'] == 0)
                    {
                        $cart->delete();
                        return response()->json([
                        'status' => 'success',
                        'message'=> 'remove product successfuly'
                        ],200);
                    }
                return response()->json([
                    'status' => 'success',
                    'message'=> 'remove quaintity product successfuly'
                    ],200);
            }else
            {
                return response()->json([
                    'status' => 'success',
                    'message'=> 'please request quaintity bigger than quaintity product'
                    ],200);
            }
        }
        return response()->json([
            'status' => 'failure',
            'message'=> 'user not auth or product id is not found the cart'
            ],404);
    }
    public function CountProduct(Request $req,$id)
    {
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
         $cart =Cart::where('user_id',$data->id)
            ->where('product_id',$id)
            ->where('carts.order',0)
            ->sum('quaintity');
        return response()->json([
            'status' => 'success',
            'count'=> $cart
             ],200);
    }
    public function ViewCart(Request $req)
    {
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $cart = DB::table('carts')
        ->join('products','carts.product_id','=','products.id')
        ->select('products.*','carts.*')
        ->where('carts.user_id','=',$data->id)
        ->where('carts.order','=',0)
        ->get();
        $totalprice =Cart::where('carts.user_id','=',$data->id)
        ->where('carts.order' ,'=',0)
        ->sum('total');
        $counts =Cart::where('carts.user_id','=',$data->id)
        ->where('carts.order' ,'=',0)
        ->distinct('product_id')->count();
        return response()->json([
            'status' => 'success',
            'Cart'=> $cart,
            'totalprice' =>$totalprice,
            'counts' =>$counts
            ],200);
    }    
}
