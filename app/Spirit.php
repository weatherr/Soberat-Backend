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
class Spirit extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'spirits';

    public static function getAbvOfDrink($spirit)
    {
        // $abv = Spirit::where('drink',$spirit)->get()->toArray();
        // return $abv;
        $abv = DB::select(DB::raw("
            SELECT ABV FROM spirits
            WHERE spirits.drink LIKE '%$spirit%'"));
        return $abv[0]->ABV;
    }

    public static function getNormalDrinkInfo($id)
    {
        $find = Spirit::where('id',$id)->get()->toArray();
        //check if it's empty?
        return $find[0];
    }

    public static function getNormalsForSearch($search)
    {
        //$find = Spirit::where('')
        $find = DB::select(DB::raw("
            SELECT drink,id,mL,ABV FROM spirits
            WHERE spirits.drink LIKE '%$search%'"));
        return $find;
    }
}
