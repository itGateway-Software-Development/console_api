<?php

use App\DeployServer;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Http\Resources\DeployServerResource;
use Symfony\Component\HttpFoundation\Response;

// Route::get('/v1/run-script', function() {

//     $scriptPath = '/home/ken/Documents/scripts/run.sh';
//     // $scriptPath = '/home/itg/deploy.sh';
//     try {
//         // Initialize and configure the process
//         $process = new Process(['sh', $scriptPath]);
//         $process->setTimeout(300);

//         // Run the process
//         $process->run();

//         if ($process->isSuccessful()) {
//             $output = $process->getOutput();

//             if (preg_match('/\{.*\}$/s', $output, $matches)) {
//                 $data = json_decode($matches[0], true);
//                 if ($data && isset($data['server_status'], $data['ip_address'], $data['server_type'])) {
//                     $deployServer = new DeployServer();
//                     $deployServer->server_type = $data['server_type'];
//                     $deployServer->server_status = $data['server_status'] == "status: running" ? "Running" : "Stopped";
//                     $deployServer->ip = $data['ip_address'];
//                     $deployServer->save();

//                     return response()->json([
//                         'status' => 'success',
//                         'server_status' => $data['server_status'],
//                         'ip_address' => $data['ip_address'],
//                         'server_type' => $data['server_type'],
//                         // 'data' => $data,
//                         // 'output' => $output,
//                         'message' => 'Script executed successfully and values retrieved.',
//                     ], Response::HTTP_OK);
//                 }
//             }

//             // If JSON parsing fails, return the raw output for debugging
//             return response()->json([
//                 'status' => 'error',
//                 'output' => $output,
//                 'message' => 'Failed to parse script output. Ensure the script returns valid JSON.'
//             ]);

//         } else {
//             // Handle process execution failure
//             $errorOutput = $process->getErrorOutput();
//             logger('Script error: ' . $errorOutput);

//             return response()->json([
//                 'status' => 'error',
//                 'error' => $errorOutput,
//                 'message' => 'Script execution failed.'
//             ], Response::HTTP_INTERNAL_SERVER_ERROR);
//         }

//     } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
//         // Handle script timeout
//         logger('Script timed out: ' . $e->getMessage());

//         return response()->json([
//             'status' => 'error',
//             'message' => 'Script execution timed out.'
//         ], Response::HTTP_GATEWAY_TIMEOUT);
//     } catch (\Exception $e) {
//         // Handle other unexpected exceptions
//         logger('Unexpected error: ' . $e->getMessage());

//         return response()->json([
//             'status' => 'script error',
//             'message' => $e->getMessage()
//         ], Response::HTTP_INTERNAL_SERVER_ERROR);
//     }
// });

Route::get('/v1/run-script/{os}', [App\Http\Controllers\Api\DeployController::class, 'deploy']);

Route::get('/v1/deploy-servers', function() {
    $servers = DeployServer::all();

    return response()->json(['status' => 'success', 'deploy_servers' => DeployServerResource::collection($servers)]);
});

Route::get('/v1/delete-deploy-server', function(Request $request) {
    $ids = $request->ids;

    foreach($ids as $id) {
        $server = DeployServer::find($id);
        $server->delete();
    }

    return response()->json(['status' => 'success']);
});

require_once base_path('app/Modules/Auth/Routes/api.php');
require_once base_path('app/Modules/UserProfile/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/Location/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/ServerTypes/Routes/api.php');
require_once base_path('app/Modules/ServerManagement/OperationSystems/Routes/api.php');
