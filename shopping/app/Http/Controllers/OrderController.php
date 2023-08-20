<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\user;
use App\Models\Product;
use App\Models\Attribute;


class OrderController extends Controller
{
    public function AddOrder(Request $req)
    { 
        $token=$req['token'];
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $today= now();
          // order_payment 0:cash , 1: card
        $user=user::where('id',$data->id)->first();
        $cart = cart::where('user_id',$data->id)
        ->where('order','=',0)
        ->get();
        $carttotal  = cart::where('user_id',$data->id)
        ->where('order',0)
        ->sum('total');
        $order = new Order();
        if($req['type_order'] == 1)  // 0:delevery , 1: reseve
        {
            $order['order_price_delivery'] = 20;
            $order['order_price'] = $order['order_price_delivery'] + $carttotal;;
            $order['order_date'] = $today;
            $order['user_id'] = $data->id;
            $order['type_order'] = $req['type_order'];
            $order['address'] = $req['address'];
            $order->save();
            $user['address'] = $req['address'];
            $user->save();
        }
        if($req['type_order'] == 0)  // 0:delevery , 1: reseve
        {
            $order['order_price_delivery'] = 5;
            $order['order_price'] = $order['order_price_delivery'] + $carttotal;;
            $order['order_date'] = $today;
            $order['user_id'] = $data->id;
            $order['address'] = $req['address'];
            $order['type_order'] = $req['type_order'];
            $order->save();
            $user['address'] = $req['address'];
            $user->save();
        }
       $carts=DB::table('carts')
       ->select('carts.*')
       ->where('order','=',0)
       ->where('user_id',$data->id)
       ->get();
        foreach($carts as $item)
        {
            $attribute =Attribute::where('product_id','=',$item->product_id)
            ->where('color',$item->color)
            ->first();
            $attribute['quaintity'] = $attribute['quaintity']- ($item->quaintity) ;
            $attribute->save();
        }
        $cartorder = cart::where('user_id',$data->id)
        ->where('order','=',0)
        ->get();
        $cartorder = cart::where('user_id',$data->id)
        ->update([
            'order' => 1
         ]);
        return response()->json([
            'status' => 'success',
            ],200);

    }
    public function ViewOrders(Request $req)
    {
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $orders = order::where('user_id',$data->id)->get();
        return response()->json([
            'status' => 'success',
            'orders' => $orders
        ],200);
    }
    public function SearchByName(Request $req , $name)
    {
        $Product=Product::where('name',"like","%".$name."%")->get();
       if(count($Product)>0 ){
        return response()->json([
            'status' => 'success',
            'products' => $Product],200);
       }else{
        return response()->json([
            'status' => 'failure',
            'message' => 'not found'],404);
       }
    }
    public function SearchByPrice(Request $req , $price)
    {
        $Product=Product::where('price','<',$price )->get();
        if(count($Product)>0 ){
            return response()->json([
                'status' => 'success',
                'products' => $Product],200);
           }else{
            return response()->json([
                'status' => 'failure',
                'message' => 'not found'],404);
           } }
    public function SearchBydescription(Request $req , $des="" )
    {
        $Product=Product::where('description',"like","%".$des."%")->get();
        if(count($Product)>0 ){
            return response()->json([
                'status' => 'success',
                'products' => $Product],200);
           }else{
            return response()->json([
                'status' => 'failure',
                'message' => 'not found'],404);
           }
    }
}
