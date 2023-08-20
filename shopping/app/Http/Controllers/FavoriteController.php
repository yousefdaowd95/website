<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\favorite;
use App\Models\product;

class FavoriteController extends Controller
{
    public function AddFavorite(Request $req)
    {
        $token=$req['token'];
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $product = product::where('id',$req['product_id'])->first();
        $found = favorite::where('product_id',$req['product_id'])
        ->where('user_id',$data->id)->first();
        if($found)
        {
            return response()->json([
                'status' => 'success',
                'message'=> 'product exist in your favorite'
                 ],404);
        
        }
        if($product)
        {
            $favorite = new favorite();
            $favorite['product_id']  = $req['product_id'];
            $favorite['user_id']  = $data->id;
            $favorite->save();
            return response()->json([
            'status' => 'success',
            'message'=> 'add product to favorite'
            ],200);
        }
        return response()->json([
            'status' => 'success',
            'message'=> 'product not found'
            ],404);
        
    }
    public function DeleteFavorite(Request $req)
    {
        $token=$req['token'];
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $favorite = favorite::where('product_id',$req['product_id'])
        ->where('user_id',$data->id)->first();
        if($favorite)
        {
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'message'=> 'remove product successfuly'
            ],200);
        }
        return response()->json([
            'status' => 'failure',
            'message'=> 'not found product or user_id failure '
        ],404);
    }
    public function ViewFavorite(Request $req)
    {
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $favorites=DB::table('products')
        ->join('favorites','products.id','=','favorites.product_id')
        ->select('products.*')
        ->where('favorites.user_id','=',$data->id)
        ->get();
        return response()->json([
            'status' => 'success',
            'favorites'=> $favorites
        ],200);
    }
    
}
