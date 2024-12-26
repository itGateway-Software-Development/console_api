<?php

namespace App\Http\Controllers\Api;

use App\DeployServer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    public function deploy($os) {
        ini_set('max_execution_time', 0); // No time limit
        ini_set('memory_limit', '-1');   // Unlimited memory

        // logger($os);
        // $scriptPath = $os == 'Ubuntu' ? '/home/ken/Documents/scripts/linux.sh' : '/home/ken/Documents/scripts/window.sh';
        // sleep(600);
        $scriptPath = $os == 'Ubuntu' ? '/home/itg/linux_vm.sh' : '/home/itg/window_vm.sh';
        try {
            // Initialize and configure the process
            $process = new Process(['sh', $scriptPath]);
            $process->setTimeout(300);

            // Run the process
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();

                if (preg_match('/\{.*\}$/s', $output, $matches)) {
                    $data = json_decode($matches[0], true);
                    if ($data && isset($data['server_status'], $data['ip_address'], $data['server_type'])) {
                        $deployServer = new DeployServer();
                        $deployServer->server_type = $data['server_type'];
                        $deployServer->server_status = $data['server_status'] == "status: running" ? "Running" : "Stopped";
                        $deployServer->ip = $data['ip_address'];
                        $deployServer->save();

                        return response()->json([
                            'status' => 'success',
                            'server_status' => $data['server_status'],
                            'ip_address' => $data['ip_address'],
                            'server_type' => $data['server_type'],
                            // 'data' => $data,
                            // 'output' => $output,
                            'message' => 'Script executed successfully and values retrieved.',
                        ], Response::HTTP_OK);
                    }
                }

                // If JSON parsing fails, return the raw output for debugging
                return response()->json([
                    'status' => 'error',
                    'output' => $output,
                    'message' => 'Failed to parse script output. Ensure the script returns valid JSON.'
                ]);

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
                'status' => 'script error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
