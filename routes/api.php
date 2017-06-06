<?php

use App\Repositories\DataBankEodRepository;
use App\Repositories\DataBanksIntradayRepository;
use App\Repositories\InstrumentRepository;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', 'MyApiLoginController@login');

/*Route::middleware('auth:api')->get('/test', function (Request $request) {
    return $data = DataBankEodRepository::getEodDataAsc(12,'2016-06-01','2017-04-01');
});*/


Route::get('/test', function () {
    return $data = DataBankEodRepository::getEodDataAsc(12,'2016-06-01','2017-04-01');
    // Access token has both "check-status" and "place-orders" scopes...
})->middleware(['auth:api', 'scopes:paid-plugin-data']);


Route::get('symbol_list/', function () {
    $data = InstrumentRepository::getInstrumentsScripWithIndex();
    return json_encode($data,JSON_UNESCAPED_SLASHES );
})->middleware(['auth:api', 'scopes:paid-plugin-data']);

Route::get('eod_data/{from}/{to}/{instrument_code}/{adjusted?}', function ($from,$to,$instrument_code,$adjusted=1) {
    $data = DataBankEodRepository::getPluginEodData($instrument_code,$from,$to,$adjusted);
    return json_encode($data,JSON_UNESCAPED_SLASHES );
})->middleware(['auth:api', 'scopes:paid-plugin-data']);

Route::get('eod_data_all/{from}/{to}/{adjusted?}/{instrument_codes?}', function ($from,$to,$adjusted=1,$instrument_codes=null) {

    $instrument_code_arr=array();
    if(!is_null($instrument_codes))
    $instrument_code_arr=explode(',',$instrument_codes);

    $data=DataBankEodRepository::getPluginEodDataAll($from,$to,$adjusted,$instrument_code_arr);
    return json_encode($data,JSON_UNESCAPED_SLASHES );
})->middleware(['auth:api', 'scopes:paid-plugin-data']);

//$tradeDate=2017-05-29
Route::get('intraday_data/{minute?}/{tradeDate?}/{instrument_code?}', function ($minute=1, $tradeDate=null, $instrument_codes=null) {

    if($tradeDate=='null')
        $tradeDate=null;
    $instrument_code_arr = array();
    if (!is_null($instrument_codes))
        $instrument_code_arr = explode(',', $instrument_codes);

    $data = DataBanksIntradayRepository::getIntraForPlugin($minute, $tradeDate,1,$instrument_code_arr);
    return json_encode($data,JSON_UNESCAPED_SLASHES );
})->middleware(['auth:api', 'scopes:paid-plugin-data']);


Route::get('intraday_data_lastday/{last_update_time?}/{skip?}/{take?}/', function ($last_update_time=0,$skip=0,$take=0) {
    $data = DataBanksIntradayRepository::getLastDayIntraForPlugin($last_update_time, $skip,$take);
    return json_encode($data, JSON_UNESCAPED_SLASHES);
})->middleware(['auth:api', 'scopes:paid-plugin-data']);

Route::get('user_stats/{username}/{ip}/{pc_info}/', function ($username, $ip, $pc_info) {
    $user_info=\DB::select("select * from users where email like '$username'");
    $user_id= $user_info[0]->id;
    DB::table('plugin_stats')->insert(
        ['user_id' => $user_id, 'login_from_ip' => $ip, 'pc_information' => $pc_info]
    );
    return 1;
})->middleware(['auth:api', 'scopes:paid-plugin-data']);
