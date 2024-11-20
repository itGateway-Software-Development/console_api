<?php

use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;

Route::get('/v1/run-script', function() {
    // $scriptPath = '/home/ken/Documents/scripts/run.sh';
    $scriptPath = '/home/itg/deploy.sh';

    try {
        // Initialize and configure the process
        $process = new Process(['sh', $scriptPath]);
        $process->setTimeout(300);

        // Run the process
        $process->run();

        // Check if the process executed successfully
        if ($process->isSuccessful()) {
            $output = $process->getOutput();

            // Attempt to extract JSON from the last part of the output
            if (preg_match('/\{.*\}$/s', $output, $matches)) {
                $data = json_decode($matches[0], true);
                if ($data && isset($data['ip'], $data['server'])) {
                    return response()->json([
                        'status' => 'success',
                        'ip' => $data['ip'],
                        'server' => $data['server'],
                        'data' => $data,
                        'message' => 'Script executed successfully and values retrieved.'
                    ], Response::HTTP_OK);
                }
            }

            return response()->json([
                'status' => 'error',
                'output' => $output,
                'message' => 'Failed to parse script output. Ensure the script returns valid JSON.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } else {
            // Handle process execution failure
            $errorOutput = $process->getErrorOutput();
            logger('Script error: ' . $errorOutput);

            return response()->json([
                'status' => 'error',
                'error' => $errorOutput,
                'message' => 'Script execution failed.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
        // Handle script timeout
        logger('Script timed out: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Script execution timed out.'
        ], Response::HTTP_GATEWAY_TIMEOUT);
    } catch (\Exception $e) {
        // Handle other unexpected exceptions
        logger('Unexpected error: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'An unexpected error occurred while running the script.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
});

require_once base_path('app/Modules/Auth/Routes/api.php');
require_once base_path('app/Modules/UserProfile/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/Location/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/ServerTypes/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/OperationSystems/Routes/api.php');
