<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
// use Illuminate\Http\Request;
use Request;
use Session;
use DB;
use Schema;
use Auth;
use App\Cart;
use App\User;
use App\Spirit;
use App\Sessions;


class AlcoholController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    function index()
    {
        $cartArray = array();

        session()->put('cart',$cartArray);
        return view('overview');
        // return redirect()->route('alcohol');
    }

    function logout(Request $request)
    {
        $userId = Auth::id();
        $cart = session()->get('cart');
        // if(empty($cart))
        // {
        //     echo'its empty';
        // }
        if($userId != '' && $userId != NULL && !empty($cart))
        {
            $whatever = Cart::save_session_cart($cart);

            Auth::logout();
            echo 'Logged out!';
        }
        else{
            echo 'Not logged in!';
        }
    }

    public function retrievePreviousSessions($userId, Request $request)
    {
        $prevSessions = Sessions::retrieve_sessions($userId);
        // $final = array();
        // foreach($prevSessions as $session)
        // {
        //     $final[] = $session;
        //     // break;
        // }
        // print_r($prevSessions);
        // exit();
        $final = json_encode($prevSessions);
        return response($final);
    }


    function checkIfInArray($drink,$array, $forCalculate = false)
    {
        //Will have to see what to do about Light Rum and stuff like that
        if($forCalculate == true)
        {
            foreach($array as $var)
            {
                if (strpos($drink, $var) !== false)
                {
                    return $var;
                }
            }
        }
        foreach($array as $var)
        {
            if (strpos($var, $drink) !== false)
            {
                return true;
            }
        }
        return false;
    }

    public function searchSuggsNormals($normalDrink)
    {
        //$normalDrink = $_POST['search'];
        $normals = Spirit::getNormalsForSearch($normalDrink);
        //$normals = Spirit::getAbvOfDrink($drink);
        return $normals;
    }

    public function forSearchSuggestions(Request $request)
    {
        // $drink = 'Mojito';
        $drink = $_GET['drink'];
        //cocktails
        $differentDrinksWithName = AlcoholController::chooseSpecificDrink($drink);
        //normals
        $normals = AlcoholController::searchSuggsNormals($drink);
        $drinks = get_object_vars($differentDrinksWithName);
        $drinks = $drinks['drinks'];

        //normalDrinks - pull by name. if it has no normals,final stays empty
        $final = array();
        foreach($normals as $normal)
        {
            $final[] = array('id' => $normal->id, 'name' => $normal->drink);
        }
        // $final = json_encode($final);
        // return response($final);
        // print_r($final);exit();

        if($drinks == NULL && $normals == NULL)
        {
            $emptyArray = array();
            $final = json_encode($emptyArray);
            return response($final);
        }

        // $arrayWithDrinks = array();
        // $final = array();

        //3. if it has drinks, array gets populated
        if($drinks != NULL)
        {
            foreach($drinks as $d)
            {
                $final[] = array('id' => $d->idDrink, 'name' => $d->strDrink);
            }
        }
        
        $final = json_encode($final);
        return response($final);
    }

    // FOR session controller
    public function getSessionCocktails(Request $request)
    {
        $id = $_GET['id'];

        $cocktails = Sessions::retrieveCocktails($id);
        // print_r($cocktails);
        // exit();

        $final = array();
        foreach($cocktails as $drink)
        {
            $final[] = array('id' => $drink['id'], 'name' => $drink['name'], 'ingredients' => $drink['ingredients'] ,'quantity' => $drink['quantity'], 'img' => $drink['img']);
        }
        $final = json_encode($final);
        return response($final);
    }
    public function getSessionNormalDrinks(Request $request)
    {
        $id = $_GET['id'];
        $normals = Sessions::retrieveNormals($id);

        $final = array();
        foreach($normals as $drink)
        {
            $final[] = array('id' => $drink['id'], 'name' => $drink['name'], 'mL' => $drink['mL'], 'abv' => $drink['abv'],'img' => 'http://localhost/imagesSoberat/'.$drink['name'].'.jpg');
        }
        $final = json_encode($final);
        return response($final);
    }

    public function deleteSession(Request $request)
    {
        $sessionId = $_POST['sessionId'];
        Sessions::deleteSession($sessionId);
    }

    public function addDrink(Request $request) // add quantity later, and searching for id
    {
        $drinkId = $_POST['id'];
        $userId = $_POST['userId'];
        
        //check if id is in cart and update quantity if it is
        $retrieve = Cart::retrieve_cart_db($userId);

        if(array_key_exists($drinkId, $retrieve))
        {
            $retrieve[$drinkId]['quantity'] += 1;
            Cart::save_session_cart_db($userId,$retrieve);

            $final = $retrieve[$drinkId];
            $final = json_encode($final);
            return response($final);
        }

        $drinkInfo = AlcoholController::chooseDrinkById($drinkId);
        // print_r($drinkInfo);exit();
        $drinkInfo = get_object_vars($drinkInfo->drinks[0]);
        // id, name, ingredients
        $listOfIngredientsOfADrink = '';
        foreach($drinkInfo as $keyey=>$element)
        {
            if (strpos($keyey, 'strIngredient') !== false)
            {
                if($element != '' && $element != NULL)
                {
                    $listOfIngredientsOfADrink .= $element . ', ';
                }
            }
        }
        $listOfIngredientsOfADrink = substr($listOfIngredientsOfADrink, 0, -2);

        $idDrink = (int)$drinkInfo['idDrink'];
        $final = array('id' => $idDrink, 'name' => $drinkInfo['strDrink'], 'ingredients' => $listOfIngredientsOfADrink, 'quantity' => 1, 'img' => $drinkInfo['strDrinkThumb']);

        // ADDING TO CART
        $retrieve = Cart::retrieve_cart_db($userId);
        // print_r($retrieve);
        // echo '<br>';
        $retrieve[$idDrink] =  $final;

        // print_r($retrieve);
        // exit();
        Cart::save_session_cart_db($userId,$retrieve);

        $final = json_encode($final);
        return response($final);
    }

    public function getNormalDrinks(Request $request)
    {
        $all = Spirit::all();
        $returnArray = array();
        foreach ($all as $drink) {
            $returnArray[] = array('id' => $drink->id, 'name' => $drink->drink);
            // $returnArray[] = $drink->drink;
        }
        $final = json_encode($returnArray);
        return response($final);
    }

    public function addOrdinaryDrink(Request $request)
    {
        $userId = $_POST['userId'];
        $normalDrinkId = $_POST['id'];
        $volume = $_POST['volume']; // not needed

        $getSpirit = Spirit::getNormalDrinkInfo($normalDrinkId);
        $volume = (float) $getSpirit['mL'];
        
        $retrieve = Cart::retrieve_normal_drinks($userId);
        if(array_key_exists($normalDrinkId, $retrieve))
        {
            $retrieve[$normalDrinkId]['mL'] += $volume;
            Cart::save_session_cart_normal_db($userId,$retrieve);

            $final = $retrieve[$normalDrinkId];
            $final = json_encode($final);
            return response($final);
        }
        $idDrink = (int)$normalDrinkId;
        $final = array('id' => $idDrink, 'name' => $getSpirit['drink'], 'mL' => $volume, 'abv' => (double)$getSpirit['ABV'], 'img' => 'http://localhost/imagesSoberat/'.$getSpirit['drink'].'.jpg');

        $retrieve[$idDrink] =  $final;
        Cart::save_session_cart_normal_db($userId,$retrieve);
        //save cart here

        $final = json_encode($final);
        return response($final);
    }

    public function getSingularDrinkInfo($id, Request $request)
    {
        $drinkInfo = AlcoholController::chooseDrinkById($id);
        $drinkInfo = get_object_vars($drinkInfo->drinks[0]);

        $listOfIngredientsOfADrink = '';
        foreach($drinkInfo as $keyey=>$element)
        {
            if (strpos($keyey, 'strIngredient') !== false)
            {
                if($element != '' && $element != NULL)
                {
                    $getNumberForMeasure = substr($keyey, -1);
                    $measure = 'strMeasure' . $getNumberForMeasure;
                    $listOfIngredientsOfADrink .= $drinkInfo[$measure] . ' ' . $element . ', ';
                }
            }
        }
        $listOfIngredientsOfADrink = substr($listOfIngredientsOfADrink, 0, -2);

        $idDrink = (int)$drinkInfo['idDrink'];
        $final = array('id' => $idDrink, 'name' => $drinkInfo['strDrink'], 'ingredients' => $listOfIngredientsOfADrink, 'quantity' => 1, 'img' => $drinkInfo['strDrinkThumb'], 'instructions' => $drinkInfo['strInstructions']);
        $final = json_encode($final);
        return response($final);
    }

    public function getList(Request $request)
    {
        $userId = $_POST['userId'];
        // var_dump($userId);
        // $userId = (int)$userId;
        // var_dump($userId);
        // exit();
        $retrieve = Cart::retrieve_cart_db($userId);
        // print_r($retrieve);exit();
        $final = array();
        foreach($retrieve as $element)
        {
            $final[] = $element;
        }
        $final = json_encode($final);
        return response($final);
    }

    public function getNormalList(Request $request)
    {
        $userId = $_POST['userId'];
        $retrieve = Cart::retrieve_normal_drinks($userId);
        $final = array();
        foreach($retrieve as $element)
        {
            $final[] = $element;
        }
        $final = json_encode($final);
        return response($final);
    }

    public function removeFromCartApi(Request $request)
    {
        $idOfDrink = $_POST['id'];
        $userId = $_POST['userId'];
        $retrieve = Cart::retrieve_cart_db($userId);

        unset($retrieve[$idOfDrink]);
        Cart::save_session_cart_db($userId, $retrieve);
    }

    public function removeFromCartNormal(Request $request)
    {
        $idOfDrink = $_POST['id'];
        $userId = $_POST['userId'];
        $retrieve = Cart::retrieve_normal_drinks($userId);

        unset($retrieve[$idOfDrink]);
        Cart::save_session_cart_normal_db($userId, $retrieve);
    }

    public function changeQuantityApi(Request $request)
    {
        $quantity = $_POST['quantity'];
        $drinkId = $_POST['drinkId'];
        $userId = $_POST['userId'];

        $retrieve = Cart::retrieve_cart_db($userId);
        $retrieve[$drinkId]['quantity'] = $quantity;

        Cart::save_session_cart_db($userId, $retrieve);
    }

    public function changeQuantityNormal(Request $request)
    {
        $mL = $_POST['mL'];
        $drinkId = $_POST['drinkId'];
        $userId = $_POST['userId'];

        $retrieve = Cart::retrieve_normal_drinks($userId);
        $retrieve[$drinkId]['mL'] = $mL;

        Cart::save_session_cart_normal_db($userId, $retrieve);
    }

    public function changeABV(Request $request)
    {
        $abv = $_POST['abv'];
        $drinkId = $_POST['drinkId'];
        $userId = $_POST['userId'];

        $retrieve = Cart::retrieve_normal_drinks($userId);
        $abvToDecimal = (float) $abv;
        $retrieve[$drinkId]['abv'] = $abvToDecimal;

        Cart::save_session_cart_normal_db($userId, $retrieve);
    }

    public function emptyCart(Request $request)
    {
        $userId = $_POST['userId'];
        $user = Cart::deleteRow($userId);
    }

    public function chooseDrinkById($id)
    {
        $url = 'https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=' . $id;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $apiKey = '1';
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $apiKey
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);

        return $result;
    }

    public function chooseSpecificDrink($beverage)
    {
        if(isset($_POST['selectedBeverage'])){
            // Storing form values into PHP variables
            $beverage = $_POST['selectedBeverage']; // Since method=”post” in the form
        }

        // $beverage = 'vodka';

        $url = 'https://www.thecocktaildb.com/api/json/v1/1/search.php?s=' . $beverage;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $apiKey = '1';
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $apiKey
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);

        return $result;
    }

    public function getMeasuresForDrink($cartKeys)
    {
        $measures = array();
        foreach($cartKeys as $key)
        {
            $forMeasure = AlcoholController::chooseDrinkById($key);
            $drinkInfo = get_object_vars($forMeasure->drinks[0]);
            //$onlyNumOz = (int)preg_replace('/[^0-9.]+/', '', $oz);
            // print_r($drinkInfo);
            // exit();
            $listOfIngredientsOfADrink = '';
            foreach($drinkInfo as $keyey=>$element) //check all ingredients. $element = ingredient
            {
                if (strpos($keyey, 'strMeasure') !== false) //check all keys to find ingredients
                {
                    if($element != '' && $element != NULL) //check if there is an ingredient
                    {
                        // checking for SLASHES '/'
                        $pos = strpos($element, '/');
                        if($pos == 1) //case 2
                        {
                            $first = (int)$element[0];
                            $second = (int)$element[2];
                            $final = $first/$second;
                            $element = $final . ' ' . substr($element, 3);
                            // var_dump($element);
                        }
                        else if($pos == 3)
                        {
                            $initial = $element[0];
                            $first = (int)$element[2];
                            $second = (int)$element[4];
                            $final = $initial+($first/$second);
                            $element = $final . ' ' . substr($element, 5);
                            // var_dump($element);
                        }
                        // checking for DASHES '-'
                        $pos = strpos($element, '-');
                        if($pos !== false)
                        {
                            $element = $element[0] . substr($element, (int)$pos+2);
                            // var_dump($element);
                            // exit();
                        }

                        // proverka samiq ingredient ($element) kakuv measurement izpolzva
                        if(strpos($element, 'part') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else if(strpos($element, 'cl') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $onlyNumOz *= 0.33814;
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else if(strpos($element, 'shot') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $onlyNumOz *= 1.5;
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else if(strpos($element, 'tsp') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $onlyNumOz *= 0.1667;
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else if(strpos($element, 'dash') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $onlyNumOz *= 0.021;
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else if(strpos($element, 'mL') !== false || strpos($element, 'ml') !== false)
                        {
                            $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $element);
                            $onlyNumOz *= 0.033814;
                            $toString = $onlyNumOz . ' oz';
                            $measures[$key][] = $toString;
                        }
                        else{
                            $measures[$key][] = $element;
                        }
                    }
                }
            }
        }

        return $measures;
    }

    public function calculate(Request $request)
    {
        $userId = $_POST['userId'];
        // $dateTimeFromDevice = $_POST['date'];
        // $dateTimeFromDevice = '13/03/2024, 16:00:09';

        // $timeRightNow = $_POST['timeRightNow'];
        $userInfo = User::getUser($userId);
        $gender = $userInfo[0]['gender'];
        $gender = strtolower($gender);
        $weight = $userInfo[0]['weight'];
        $weightType = $userInfo[0]['weightType'];

        $finalCalculation = 0;

        $cart = Cart::retrieve_cart_db($userId);
        $normalCart = Cart::retrieve_normal_drinks($userId);

        // print_r($cart);
        // print_r($normalCart);
        // exit();

        foreach($normalCart as $normalDrink) // if no cart it will just skip this
        {
            $mL = (double)$normalDrink['mL'];
            $onlyNumOz = $mL * 0.033814;
            // $abv = Spirit::getAbvOfDrink($normalDrink['name']);
            $abv = $normalDrink['abv'];
            $onlyAlcoholOz = ($abv/100) * $onlyNumOz;
            $finalCalculation += $onlyAlcoholOz;
        }

        $cartKeys = array_keys($cart);
        $measures = AlcoholController::getMeasuresForDrink($cartKeys);
        // print_r($measures);
        // exit();

        //Takes only alcohol from drinks and sums it up
        $popularAlcoholicDrinks = array('whiskey', 'vodka', 'wine', 'sherry', 'port', 'brandy', 'rum', 'gin', 'tequila', 'hock', 'vermouth', 'absinthe', 'rye', 'beer', 'ale', 'champagne', 'cognac', 'saké', 'sake', 'galliano', 'triple sec', 'liqueur', 'cider', 'bourbon', 'schnapps', 'aperol', 'jägermeister', 'prosecco', 'baijiu', 'soju', 'ricard', 'amaretto', 'pisco');
        foreach($cart as $id => $drink)
        {
            $ingredients = $drink['ingredients'];
            $arrayWithIng = explode(', ', $ingredients);
            // echo 'Ingredients:';
            // print_r($arrayWithIng);
            foreach($arrayWithIng as $idOfIng => $ing)
            {
                $toLower = strtolower($ing);
                $checkUp = AlcoholController::checkIfInArray($toLower, $popularAlcoholicDrinks,true);
                if($checkUp != false)
                {
                    $abv = Spirit::getAbvOfDrink($checkUp);
                    $drinkId = $id;
                    $oz = $measures[$drinkId][$idOfIng];

                    if(strpos($oz, '-') !== false)
                    {
                        $oz = $oz[0];
                    }

                    $onlyNumOz = (double)preg_replace('/[^0-9.]+/', '', $oz);// moga i (int)$oz[0] maybe float or double here

                    $quantity = (int)$cart[$drinkId]['quantity'];
                    $onlyNumOz *= $quantity;

                    $onlyAlcoholOz = ($abv/100) * $onlyNumOz; //samo alcoholic value za toq ingredient

                    // var_dump($abv);
                    // var_dump($onlyAlcoholOz);
                    $finalCalculation += $onlyAlcoholOz;
                }
            }
        }
        // OLD formula
        // if ($gender == 'male')
        // {
        //     $gend = 3.75;
        // }else{
        //     $gend = 4.7;
        // }
        // if($weightType == 'lb/pounds')
        // {
        //     $weight *= 0.453592;
        // }
        // $finalCalculation = ($finalCalculation * $gend) / $weight;
        // $hoursNeeded = $finalCalculation / 0.016;

        // NEW formula
        if ($gender == 'male')
        {
            $gend = 0.73;
        }else{
            $gend = 0.66;
        }
        if($weightType == 'kg')
        {
            $weight *= 2.20462; // to lb pounds
        }
        $onlyOzAlcohol = $finalCalculation;
        $hoursNeeded = (($onlyOzAlcohol*5.14)/($weight*$gend))/0.015; // BAC = 0.00
        $finalCalculation = (($onlyOzAlcohol*5.14)/($weight*$gend)); // BAC at the moment -> hours = 0

        // $testingHours = fmod($hoursNeeded, 1) * 60;
        // var_dump($testingHours);
        // exit();
        // echo 'hours needed:' . $hoursNeeded . '<br>';
        $finalHours = sprintf('%02d:%02d', (int) $hoursNeeded, fmod($hoursNeeded, 1) * 60); //works wow
        // var_dump($finalHours);exit();

        // Save Session
        if(empty($cart)) //edno ot dvete moje da e empty
        {
            Sessions::save_session($userId,array(),$normalCart,$finalHours);
        }
        else if(empty($normalCart))
        {
            Sessions::save_session($userId,$cart,array(),$finalHours);
        }
        else{
            Sessions::save_session($userId,$cart,$normalCart,$finalHours);
        }

        // $timeRightNow = date("d-m-Y h:i:sa"); //neka angular go podava za da e spored device-a

        // TimeNeeded section
        $hoursTest = (int) $hoursNeeded;
        $minutesTest = fmod($hoursNeeded, 1) * 60; //you can check after with formula
        $minutesTest = (int) $minutesTest;

        // $myTime = '2024-03-14 16:00:09';
        // $myTime = '14-03-2024 16:00:09';
        $myTime = $_POST['date'];
        $myTime = str_replace(',','',$myTime);
        $myTime = str_replace('/','-',$myTime);

        //Minutes success
        // $purvo = '2011-11-17 05:05:00'; //success!
        $newtimestamp = strtotime($myTime . ' + ' . $minutesTest . ' minute');
        // echo date('d-m-Y H:i:s', $newtimestamp) . '<br>';
        $newtimestamp = date('d-m-Y H:i:s', $newtimestamp); // backwards conversion to date string to use in next strtotime

        //Hours success
        // $hoursTest = 16;
        // $vtoro = '2011-11-17 05:05:00';
        $newtimestamp = strtotime($newtimestamp . '+'.$hoursTest.' hours');
        // echo date('d-m-Y H:i:s', $newtimestamp);
        $soberAtHour = date('d-m-Y H:i:s', $newtimestamp);

        //old not working way
        // $timeRightNow = strtotime($timeRightNow);
        // $finals = strtotime($finalHours);
        // $forFinalSober = $timeRightNow + $finals;
        // $soberAtHour = date('d-m-Y h:i:sa', $forFinalSober);
        $final = array('hoursNeeded' => $finalHours, 'soberAt' => $soberAtHour, 'bac' => $finalCalculation);
        $final = json_encode($final);
        return response($final);
        // $x = 0.015 / 0.016;
        // echo $x;
    }

    public function favouriteDrink($userId, Request $request)
    {
        $prevSessions = Sessions::retrieve_sessions($userId);
        $mixedDrinks = array();
        $quantityTotal = array();
        $mLTotal = array();
        $arrayForIds = array();
        $imgArray = array();
        $imgArrayNormal = array();
        foreach($prevSessions as $session)
        {
            $products = $session['products'];
            if($products != '')
            {
                $products = unserialize($products);
                // print_r($products);
                // echo '<br>';
                $arrayKeysCocktails = array_keys($products);
                //set to get the name from cocktailDBApi
                $onlyNames = array();
                foreach($products as $product)
                {
                    $name = $product['name'];
                    $arrayForIds[$name] = $product['id'];

                    $onlyNames[] = $name;
                    $imgArray[$name] = $product['img'];

                    if(array_key_exists($name, $quantityTotal)) //check if it stores strings and not ints
                    {
                        $quantityTotal[$name] += $product['quantity'];
                    }else{
                        $quantityTotal[$name] = $product['quantity'];
                    }
                }
                // $mixedDrinks = array_merge($arrayKeysCocktails,$mixedDrinks); - uncomm for ids
                $mixedDrinks = array_merge($onlyNames,$mixedDrinks);
            }
            $normalDrinks = $session['normal_drinks'];
            if($normalDrinks != '')
            {
                $normalDrinks = unserialize($normalDrinks);
                $arrayKeysNormals = array_keys($normalDrinks);
                //set to get the name from spirits
                $onlyNames = array();
                foreach($arrayKeysNormals as $id)
                {
                    $getInfo = Spirit::getNormalDrinkInfo($id);
                    $name = ucfirst($getInfo['drink']);
                    $arrayForIds[$name] = $id;
                    $onlyNames[] = $name;
                    // $imgArrayNormal[$name] = $getInfo['img'];

                    $current = $normalDrinks[$id]['mL'];
                    if(array_key_exists($name, $mLTotal))
                    {
                        $mLTotal[$name] += $current;
                    }else{
                        $mLTotal[$name] = $current;
                    }
                }
                // $mixedDrinks = array_merge($arrayKeysNormals,$mixedDrinks); - uncomm for ids
                $mixedDrinks = array_merge($onlyNames,$mixedDrinks);
            }
        }
        // print_r($imgArray);
        // exit();
        $values = array_count_values($mixedDrinks);
        arsort($values);
        $popular = array_slice(array_keys($values), 0, 3, true);
        // assign quantity to drink
        $final = array();
        foreach($popular as $drink)
        {
            $id = $arrayForIds[$drink];
            if(array_key_exists($drink, $quantityTotal))
            {
                $final[] = array('id' => $id,'name' => $drink ,'quantity' => $quantityTotal[$drink], 'img' => $imgArray[$drink]);
            }else{
                $final[] = array('id' => $id,'name' => $drink ,'mL' => $mLTotal[$drink], 'img' => 'http://localhost/imagesSoberat/'.$drink.'.jpg'); // 'img' => $imgArrayNormal[$drink]);
            }
        }

        $final = json_encode($final);
        return response($final);
    }
}
