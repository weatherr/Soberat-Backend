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
class Sessions extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sessions';

    public static function save_session($id, $drinkList, $normalList, $hoursNeeded){
        $ip = Request::ip();

        if(empty($normalList))
        {
            $serializedCart = serialize($drinkList);
            $saveSession = new Sessions;
            $saveSession->user_id = $id;
            $saveSession->ip_address = $ip;
            $saveSession->products = $serializedCart;
            $saveSession->hours_needed = $hoursNeeded;
            $saveSession->save();
        }
        else if(empty($drinkList))
        {
            $serializedNormal = serialize($normalList);
            $saveSession = new Sessions;
            $saveSession->user_id = $id;
            $saveSession->ip_address = $ip;
            $saveSession->normal_drinks = $serializedNormal;
            $saveSession->hours_needed = $hoursNeeded;
            $saveSession->save();
        }
        else{
            $serializedCart = serialize($drinkList);
            $serializedNormal = serialize($normalList);
            $saveSession = new Sessions;
            $saveSession->user_id = $id;
            $saveSession->ip_address = $ip;
            $saveSession->products = $serializedCart;
            $saveSession->normal_drinks = $serializedNormal;
            $saveSession->hours_needed = $hoursNeeded;
            $saveSession->save();
        }
    }

    public static function retrieve_sessions($user_id)
    {
       $find = Sessions::where('user_id',$user_id)->get()->toArray();

       if(empty($find))
       {
           $emptyArray = array();
           return $emptyArray;
       }
       return $find;
    }

    public static function retrieveCocktails($id)
    {
        $find = Sessions::where('id',$id)->get()->toArray();
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
        return $products;
    }
    public static function retrieveNormals($id)
    {
        $find = Sessions::where('id',$id)->get()->toArray();
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

    public static function deleteSession($id)
    {
        DB::table('sessions')->where('id', '=', $id)->delete();
    }
}
