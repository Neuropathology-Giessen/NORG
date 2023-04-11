<?php

use App\Http\Controllers\CombinedTankController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\RemovedSampleController;
use App\Http\Controllers\RestoreSampleController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\ShippedSampleController;
use App\Http\Controllers\StorageTankController;
use App\Http\Controllers\TankModelController;
use App\Http\Controllers\MaterialTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Route::get('/sentSamples',function() {
//  return view('sentSamples');
//});

Route::get('/imprint',function() {
    return view('imprint');
});

Route::get('/privacy',function() {
    return view('privacy');
});

Route::get('/download',function() {
    $pathToFile = public_path().'/sqldumps/NorgDBdump'.date("Ymd",strtotime("-1 days")).'/NorgSQLdump'.date("Ymd",strtotime("-1 days")).'.sql';
    $name = 'NorgDBdump'.date("Y-m-d",strtotime("-1 days"));
    $headers = array('Content-Type: application/sql');
    return response()->download($pathToFile, $name, $headers);
});

Route::get('/insideTank/{idTank}/',[CombinedTankController::class, 'indexContainer']);

Route::get('/insideTank/{idTank}/{idContainer}/',[CombinedTankController::class, 'indexTube']);

Route::get('/insideTank/{idTank}/{idContainer}/{idTube}/',[CombinedTankController::class, 'indexSample']);
Auth::routes();

Route::get('/', [CombinedTankController::class, 'index']);

Route::get('/sampleList', [SampleController::class, 'index']);

Route::get('/removedSamples', [RemovedSampleController::class, 'index']);

Route::post('/newSamples/pos/confirm', [SampleController::class, 'store']);

Route::post('/newSamples/pos', [SampleController::class, 'create']);

Route::get('/manageMaterialTypes', [MaterialTypeController::class, 'index']);

Route::post('/manageMaterialTypes', [MaterialTypeController::class, 'store'])->name('material-type.store');

Route::post('/materialDestroy', [MaterialTypeController::class, 'destroy']);

Route::post('/transfer', [RemovedSampleController::class, 'store']);

Route::post('/transferSentSample', [RemovedSampleController::class, 'deleteSentSample']);

Route::post('/transferSampleDelete', [RemovedSampleController::class, 'deleteSampleFromList']);

Route::get('/manageTanks', [TankModelController::class, 'index']);

Route::post('/addTank', [StorageTankController::class, 'store']);

Route::post('/addTankmodel', [TankModelController::class, 'store']);

Route::post('/tankDestroy', [StorageTankController::class, 'destroy']);

Route::post('/shipped', [ShippedSampleController::class, 'store']);

Route::post('/restore', [RestoreSampleController::class, 'create']);

Route::get('/manageUser', [ManageUserController::class, 'create']);

Route::post('/manageUser/updateRights', [ManageUserController::class, 'updateRights']);

Route::post('/manageUser/confirm-delete', [ManageUserController::class, 'confirmDelete'])->name('manageUser.confirmDelete');

Route::post('/manageUser/delete', [ManageUserController::class, 'delete'])->name('manageUser.delete');

Route::post('/manageUser/reset', [ManageUserController::class, 'resetPassword']);

Route::post('/restore/confirm', [RestoreSampleController::class, 'store']);

//Test Section
Route::get('/dataTest', [CombinedTankController::class, 'indextest']);

Route::get('/sentSamples', [ShippedSampleController::class, 'index']);