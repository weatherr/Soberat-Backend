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
class Users extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    public static function saveUser($request, $oldEmail)
    {
        $affected = DB::table('users')
        ->where('email', $oldEmail)
        ->update(['name' => $request->name,'email' => $request->email,'weight' => $request->weight,'gender' => $request->gender, 'weightType' => $request->weightType]);
    }
}
