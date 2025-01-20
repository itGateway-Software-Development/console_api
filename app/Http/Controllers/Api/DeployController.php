<?php

namespace App\Http\Controllers\Api;

use App\DeployServer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{

    public function getServerStatus() {
        // $scriptPath = '/home/ken/Documents/scripts/status.sh';
        // sleep(600);
        $scriptPath = '/home/itg/vm_status.sh';
        try {
            // Initialize and configure the process
            $process = new Process(['sh', $scriptPath]);
            $process->setTimeout(1200);

            // Run the process
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();

                $status_responses = json_decode($output);

                foreach ($status_responses as $status_response) {

                    if (isset($status_response->vm_id) && isset($status_response->status)) {
                        $deployServer = DeployServer::where('vm_id', $status_response->vm_id)->first();

                        if ($deployServer) {
                            $normalizedStatus = strtolower($status_response->status);
                            logger($normalizedStatus);

                            if ($normalizedStatus === '0' || $normalizedStatus === 'stop') {
                                $deployServer->server_status = 'Stopped';
                            } else {
                                $deployServer->server_status = 'Running';
                            }

                            $deployServer->update();
                        }
                    } else {
                        // Log if required fields are missing
                        logger('Missing vm_id or status in response: ' . json_encode($status_response));
                    }
                }

                return response()->json(['status' => 'success']);


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
    public function deploy($os, $password, $server_name ) {
        ini_set('max_execution_time', 0); // No time limit
        ini_set('memory_limit', '-1');   // Unlimited memory
        set_time_limit(1200);

        // logger($os);
        // $scriptPath = $os == 'Ubuntu' ? '/home/ken/Documents/scripts/linux.sh' : '/home/ken/Documents/scripts/window.sh';
        // sleep(600);
        $scriptPath = $os == 'Ubuntu' ? '/home/itg/linux_vm.sh' : '/home/itg/window_vm.sh';
        try {
            // Initialize and configure the process
            $process = new Process(['sh', $scriptPath, $password]);
            $process->setTimeout(1200);

            // Run the process
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();

                if (preg_match('/\{.*\}$/s', $output, $matches)) {
                    $data = json_decode($matches[0], true);
                    if ($data && isset($data['server_status'], $data['ip_address'], $data['server_type'])) {
                        $deployServer = new DeployServer();
                        $deployServer->vm_id = $data['vm_id'];
                        $deployServer->server_type = $data['server_type'];
                        $deployServer->server_name = $server_name;
                        $deployServer->password = $password;
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

    public function shutdownServer($id) {

        $sever = DeployServer::find($id);

        // $scriptPath = '/home/ken/Documents/scripts/shutdown.sh';
        $scriptPath = $sever->server_status == "Running" ? '/home/itg/vm_stop.sh' : '/home/itg/vm_start.sh';

        $process = new Process(['sh', $scriptPath, $sever->vm_id]);
        $process->run();

        $output = $process->getOutput();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();

            $deployServer = DeployServer::find($id);
                    $deployServer->server_status = $deployServer->server_status == "Running" ? "Stopped" : "Running";
                    $deployServer->save();

                    return response()->json([
                        'status' => 'success',
                        // 'data' => $data,
                        'output' => $output,
                        'message' => 'Script executed successfully and values retrieved.',
                    ], Response::HTTP_OK);

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

    }

    public function deleteServer($id) {
        $sever = DeployServer::find($id);

        // $scriptPath = '/home/ken/Documents/scripts/shutdown.sh';
        $scriptPath = '/home/itg/vm_delete.sh';

        $process = new Process(['sh', $scriptPath, $sever->vm_id]);
        $process->run();

        $output = $process->getOutput();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();

            $deployServer = DeployServer::where('id', $id)->delete();

                    return response()->json([
                        'status' => 'success',
                        // 'data' => $data,
                        'output' => $output,
                        'message' => 'Script executed successfully and values retrieved.',
                    ], Response::HTTP_OK);

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
    }

    public function restartServer($id) {
        $server = DeployServer::find($id);

        // $scriptPath = '/home/ken/Documents/scripts/restart.sh';
        $scriptPath = '/home/itg/vm_restart.sh';

        $process = new Process(['sh', $scriptPath, $server->vm_id]);
        $process->run();

        $output = $process->getOutput();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();

            if (preg_match('/\{.*\}$/s', $output, $matches)) {
                $data = json_decode($matches[0], true);
                if ($data && isset($data['status'])) {

                   sleep(5);

                    return response()->json([
                        'status' => 'success',
                        // 'data' => $data,
                        'output' => $output,
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
    }
}
