<?php

namespace App\Modules\ServerManagement\OperatingSystem\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Modules\ServerManagement\OperatingSystem\Models\OperatingSystem;
use App\Modules\ServerManagement\OperatingSystem\Resources\OperatingSystemResource;
use App\Modules\ServerManagement\OperatingSystem\Requests\StoreOperatingSystemRequest;
use App\Modules\ServerManagement\OperatingSystem\Requests\UpdateOperatingSystemRequest;

class OperatingSystemController extends Controller
{
    public function index() {
        $operation_systems = OperatingSystem::all();

        return response()->json(['status' => 'success', 'operationSystems' => OperatingSystemResource::collection($operation_systems)]);
    }

    public function store(StoreOperatingSystemRequest $request) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/os', $file_name);
            }

            $os = new OperatingSystem();
            $os->name = $request->name;
            $os->image = $file_name ? '/images/os/' . $file_name : null;
            $os->type = $request->type;
            $os->save();

            DB::commit();

            return response()->json(['status' => 'success',  'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateOS(UpdateOperatingSystemRequest $request, OperatingSystem $operatingSystem) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                //delete old image
                if($operatingSystem->image) {
                    File::delete(public_path('/storage' . $operatingSystem->image));
                }

                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/os', $file_name);
            }

            $operatingSystem->name = $request->name;
            $operatingSystem->image = $file_name ? '/images/os/' . $file_name : $operatingSystem->image;
            $operatingSystem->type = $request->type;
            $operatingSystem->update();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(OperatingSystem $operatingSystem) {
        DB::beginTransaction();

        try {
            if($operatingSystem->image) {
                File::delete(public_path('/storage' . $operatingSystem->image));
            }

            $operatingSystem->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function destroyMultiOS(Request $request) {
        DB::beginTransaction();

        try {
            $ids = $request->ids;
            if(count($ids) > 0) {
                foreach($ids as $id) {
                    $operatingSystem = OperatingSystem::find($id);
                    if($operatingSystem->image) {
                        File::delete(public_path('/storage' . $operatingSystem->image));
                    }

                    $operatingSystem->delete();
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
