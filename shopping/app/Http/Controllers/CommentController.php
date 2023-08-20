<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Comment;
use App\Models\User;


class CommentController extends Controller
{
    // لاضافة تعليق على المنتج 
   public function AddComment(Request $req , $idproduct)
   {
        $rules=array(
            'comment'=>'required'
        ); 
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => 'failure',
                'message'=> $validator->errors()],404);
        }
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $product = Product::where('id',$idproduct)->first();
        if($product)
        {
            
            $comment = new Comment();
            $comment['comment'] = $req['comment'];
            $comment['product_id'] = $idproduct;
            $comment['user_id'] = $data->id;
            $comment->save();
            return response()->json([
                'status' => 'success' ,
                'message'=>'comment added successfuly'
            ],200);
        }
        return response()->json([
            'status' => 'failure' ,
            'message'=>'product not exists'
        ],404);
   }
   // لتعديل التعليق على المنتج 
   public function EditComment(Request $req , $idcomment)
   {
        $token=$req->header('token');
        $decodedToken=base64_decode($token);
        $data=json_decode($decodedToken);
        $comment = Comment::where('id',$idcomment)->first();
        if($comment)
        {
            if($comment['user_id'] == $data->id){
                $comment['comment'] = $req['comment'];
                $comment->save();
                return response()->json([
                    'status' => 'success' ,
                    'message'=>'comment edited successfuly'],200);
                }else {
                    return response()->json([
                        'status' => 'failure' ,
                        'message'=>'you cannot edit comment'],404);
                }
        }
        return response()->json([
            'status' => 'failure' ,
            'message'=>'comment not exists'
        ],404);
   }
   // لحذف تعليق على المنتج 
   public function DeleteComment(Request $req , $idcomment)
   {
            $token=$req->header('token');
            $decodedToken=base64_decode($token);
            $data=json_decode($decodedToken);
            $comment = Comment::where('id',$idcomment)->first();
            if($comment)
            {
                if($comment['user_id'] == $data->id)
                {
                    $comment->delete();
                    return response()->json([
                        'status' => 'success' ,
                        'message'=>'comment deleted successfuly'],200);
                }
                else
                {
                    return response()->json([
                        'status' => 'failure' ,
                        'message'=>'you cannot delete comment'],404);
                }
            }
            return response()->json([
                'status' => 'failure' ,
                'message'=>'comment not exists'
            ],404);

    }
    // لعرض التعليقات مع اسم المستخدم صاحب التعليق
    public function ViewComments(Request $req , $idproduct)
    {
        $product = Product::where('id',$idproduct)->first();
        if($product)
        {
          $comments = DB::table('comments')
            ->join('users','comments.user_id','=','users.id')
            ->select('comments.comment','users.name')
            ->distinct()
            ->get();
        return response()->json([
               'status' => 'success' ,
              'comments'=> $comments
         ],200);
        }
        return response()->json([
            'status' => 'failure' ,
            'message'=>' product not found'
          ],404);
        
    }
}
