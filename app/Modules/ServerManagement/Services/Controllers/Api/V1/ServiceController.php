<?php

namespace App\Modules\ServerManagement\Services\Controllers\Api\V1;

use App\Modules\ServerManagement\Services\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SelectResource;
use App\Modules\ServerManagement\Services\Models\Service;
use App\Modules\ServerManagement\Services\Resources\ServiceResource;
use App\Modules\ServerManagement\Services\Requests\StoreServiceRequest;
use App\Modules\ServerManagement\ServiceCategory\Models\ServiceCategory;

class ServiceController extends Controller
{
    public function index() {
        $services = Service::with('category')->get();
        $service_categories = SelectResource::collection(ServiceCategory::select('id', 'name')->get());

        return response()->json(['status' => 'success', 'services' => ServiceResource::collection($services), 'service_categories' => $service_categories]);
    }

    public function store(StoreServiceRequest $request) {
        DB::beginTransaction();

        try {

            $service = new Service();
            $service->name = $request->name;
            $service->service_category_id = $request->service_category_id;
            $service->description = $request->description;
            $service->link = $request->link;
            $service->save();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateService(UpdateServiceRequest $request, Service $service) {
        DB::beginTransaction();

        try {
            $service->name = $request->name;
            $service->service_category_id = $request->service_category_id;
            $service->description = $request->description;
            $service->link = $request->link;
            $service->update();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Service $service) {
        DB::beginTransaction();

        try {
            $service->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroyMultiServices(Request $request) {
        DB::beginTransaction();

        try {
            $ids = $request->ids;
            if(count($ids) > 0) {
                foreach($ids as $id) {
                    $service = Service::find($id);
                    $service->delete();
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
