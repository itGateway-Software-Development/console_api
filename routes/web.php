<?php

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
    return view('welcome');
});

Route::get('/run-script', function() {

    // dynamic
    $cpu = '8 Core';
    $os = 'Ubuntu';

    $output = shell_exec('sh /home/ken/Downloads/run.sh 2>&1');
    return response()->json(['output' => $output]);
});

// jg
