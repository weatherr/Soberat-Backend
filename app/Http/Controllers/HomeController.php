<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::id();
        $data = array();

        if($userId != '' && $userId != NULL)
        {
            $userData = User::where('id',$userId)->first();
            $weight = $userData->weight;
            $height = $userData->height;
            $age = $userData->age;

            if(($weight != '' || $height != '' || $age != '') && ($weight != NULL || $height != NULL || $age != NULL))
            {
                $data['userInfo'] = true;
                $data['userNotLogged'] = false;
            }
            else{
                $data['userInfo'] = false;
                $data['userNotLogged'] = false;
            }
        }
        else{
            $data['userInfo'] = false;
            $data['userNotLogged'] = true;
        }

        var_dump($data);

        return view('home', $data);
    }

    function save_user_info(Request $request)
    {
        $body_weight = $_POST['weight'];
        $height = $_POST['height'];
        $age = $_POST['age'];

        $userId = Auth::id();
        $update = User::where('id',$userId)->update(['age' => $age, 'weight' => $body_weight, 'height' => $age]);

        return route('home');
    }
}
