<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Session;
use DB;
use Schema;
use DateTime;
use DateTimeZone;
use Auth;
use Request;
// use Carbon;
class Cart extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cart';

    public static function save_session_cart_db($id, $drinkList){
        // $user_id = Auth::id();
        $ip = Request::ip();
        $serializedCart = serialize($drinkList);
        $check = Cart::retrieve_cart_db($id);
        $checkNormal = Cart::retrieve_normal_drinks($id);

        // print_r($checkNormal);

        if(!empty($check))
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'products' => $serializedCart]);
        }
        else if(empty($check) && !empty($checkNormal))
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'products' => $serializedCart]);
        }
        else if(!empty($check) && !empty($checkNormal)) //only if it doesn't have normals but does have cocktails
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'products' => $serializedCart]);
        }
        else{
            $saveCart = new Cart;
            $saveCart->user_id = $id;
            $saveCart->ip_address = $ip;
            //turn the cart into JSON?
            $saveCart->products = $serializedCart;
            // print_r($saveCart);
            // exit();
            $saveCart->save();
            // echo 'success' . "<br>";
        }
    }

    public static function save_session_cart_normal_db($id, $drinkList){
        // $user_id = Auth::id();
        $ip = Request::ip();
        $serializedCart = serialize($drinkList);
        $checkNormal = Cart::retrieve_normal_drinks($id);
        $checkCocktails = Cart::retrieve_cart_db($id);

        if(empty($checkNormal) && !empty($checkCocktails)) //only if it doesn't have normals but does have cocktails
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'normal_drinks' => $serializedCart]);
        }
        else if(!empty($checkNormal) && empty($checkCocktails)) //only if it doesn't have normals but does have cocktails
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'normal_drinks' => $serializedCart]);
        }
        else if(!empty($checkNormal) && !empty($checkCocktails)) //only if it doesn't have normals but does have cocktails
        {
            $affected = DB::table('cart')
              ->where('user_id', $id)
              ->update(['ip_address' => $ip,'normal_drinks' => $serializedCart]);
        }
        else{
            $saveCart = new Cart;
            $saveCart->user_id = $id;
            $saveCart->ip_address = $ip;
            //turn the cart into JSON?
            $saveCart->normal_drinks = $serializedCart;
            $saveCart->save();
        }
    }

    public static function retrieve_cart_db($user_id)
    {
       $find = Cart::where('user_id',$user_id)->get()->toArray();

       if(empty($find))
       {
           $emptyArray = array();
           return $emptyArray;
       }
       $prod = $find[0]['products'];
       if($prod != '') //in case it exists and the products are '' or {}
       {
            $products = unserialize($prod);
       }else{
            $products = array();
       }
    //    $products = unserialize($prod);
       return $products;
    }

    public static function retrieve_normal_drinks($user_id)
    {
       $find = Cart::where('user_id',$user_id)->get()->toArray();

       if(empty($find))
       {
           $emptyArray = array();
           return $emptyArray;
       }
       $prod = $find[0]['normal_drinks'];
       if($prod != '') //in case it exists and the products are '' or {}
       {
            $products = unserialize($prod);
       }else{
            $products = array();
       }
       return $products;
    }

    public static function deleteRow($id)
    {
        DB::table('cart')->where('user_id', '=', $id)->delete();
    }
}
