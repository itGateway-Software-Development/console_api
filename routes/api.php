<?php

Route::get('/v1/run-script', function() {

    // dynamic
    $cpu = '8 Core';
    $os = 'Ubuntu';

    $password = "Veeam123!@#";
    $output = shell_exec("echo '$password' | sudo -S bash /home/itg/deploy.sh 2>&1");
    return response()->json(['output' => $output]);
});
require_once base_path('app/Modules/Auth/Routes/api.php');
require_once base_path('app/Modules/UserProfile/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/Location/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/ServerTypes/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/OperationSystems/Routes/api.php');
