<?php

use Symfony\Component\Process\Process;

Route::get('/v1/run-script', function() {
    // $scriptPath = '/home/ken/Documents/scripts/run.sh';
    $scriptPath = '/home/itg/deploy.sh';

    $process = new Process(['sh', $scriptPath]);
    $process->run();

    if ($process->isSuccessful()) {
        $output = $process->getOutput();

        $data = json_decode($output, true);

        if ($data && isset($data['ip']) && isset($data['server'])) {
            $ip = $data['ip'];
            $server = $data['server'];

            return response()->json([
                'status' => 'success',
                'ip' => $ip,
                'server' => $server,
                'message' => 'Script executed successfully and values retrieved.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to parse script output.'
            ]);
        }
    } else {
        $errorOutput = $process->getErrorOutput();

        return response()->json([
            'status' => 'error',
            'error' => $errorOutput,
            'message' => 'Script execution failed.'
        ]);
    }
});
require_once base_path('app/Modules/Auth/Routes/api.php');
require_once base_path('app/Modules/UserProfile/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/Location/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/ServerTypes/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/OperationSystems/Routes/api.php');
