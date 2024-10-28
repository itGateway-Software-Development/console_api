<?php

namespace App\Modules\ServerManagement\OperationSystems\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Modules\ServerManagement\Versions\Models\Version;
use App\Modules\ServerManagement\ServerTypes\Models\ServerType;
use App\Modules\ServerManagement\OperationSystems\Models\OperationSystem;
use App\Modules\ServerManagement\OperationSystems\Resources\ServerTypeResource;
use App\Modules\ServerManagement\OperationSystems\Resources\OperationSystemResource;
use App\Modules\ServerManagement\OperationSystems\Requests\StoreOperationSystemRequest;
use App\Modules\ServerManagement\OperationSystems\Requests\UpdateOperationSystemRequest;

class OperationSystemController extends Controller
{
    public function index()
    {
        $operationSystems = OperationSystem::with('serverTypes', 'versions')->get();
        $serverTypes = ServerType::select('id', 'name')->get();

        return response()->json(['status' => 'success', 'operationSystems' => OperationSystemResource::collection($operationSystems), 'serverTypes' => ServerTypeResource::collection($serverTypes)]);
    }

    public function store(StoreOperationSystemRequest $request) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/os', $file_name);
            }

            $os = new OperationSystem();
            $os->name = $request->name;
            $os->image = $file_name ? '/images/os/' . $file_name : null;
            $os->status = $request->status;
            $os->save();

            foreach ($request->versions as $ver) {
                $version = new Version();
                $version->version_no = $ver;
                $version->operation_system_id = $os->id;
                $version->save();
            }

            $os->serverTypes()->attach($request->server_types);

            DB::commit();

            return response()->json(['status' => 'success', 'operationSystem' => $os, 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateOperationSystem(UpdateOperationSystemRequest $request, OperationSystem $operationSystem) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                //delete old image
                if($operationSystem->image) {
                    File::delete(public_path('/storage' . $operationSystem->image));
                }

                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/os', $file_name);
            }

            $operationSystem->name = $request->name;
            $operationSystem->image = $file_name ? '/images/os/' . $file_name : $operationSystem->image;
            $operationSystem->status = $request->status;
            $operationSystem->update();

            $operationSystem->versions()->delete();

            foreach ($request->versions as $ver) {
                $version = new Version();
                $version->version_no = $ver;
                $version->operation_system_id = $operationSystem->id;
                $version->save();
            }

            $operationSystem->serverTypes()->detach();
            $operationSystem->serverTypes()->attach($request->server_types);

            DB::commit();

            return response()->json(['status' => 'success', 'operationSystem' => $operationSystem, 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(OperationSystem $operationSystem)
    {
        DB::beginTransaction();

        try {
            if($operationSystem->image) {
                File::delete(public_path('/storage' . $operationSystem->image));
            }
            $operationSystem->serverTypes()->detach();
            $operationSystem->versions()->delete();
            $operationSystem->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroyMultiOperationSystems(Request $request) {
        DB::beginTransaction();

        try {
            $ids = $request->ids;
            if(count($ids) > 0) {
                foreach($ids as $id) {
                    $os = OperationSystem::find($id);
                    if($os->image) {
                        File::delete(public_path('/storage' . $os->image));
                    }
                    $os->serverTypes()->detach();
                    $os->versions()->delete();
                    $os->delete();
                }

                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No locations selected!']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

    }
}
