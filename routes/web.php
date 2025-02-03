<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admins.dashboards.dash-board');
});
Route::get('/login', function () {
    return view('auths.login');
});

Route::resource('tags', TagController::class);

$objects = [
    'users' => UserController::class
];
foreach ($objects as $object => $controller) {
    Route::resource($object, $controller);
    Route::post($object . '/import', [$controller, 'import'])->name($object . '.' . 'import');
    Route::put($object . '/export', [$controller, 'export'])->name($object . '.' . 'export');
}

use App\Http\Controllers\MailController;

Route::get('/send-email', [MailController::class, 'sendEmail']);

