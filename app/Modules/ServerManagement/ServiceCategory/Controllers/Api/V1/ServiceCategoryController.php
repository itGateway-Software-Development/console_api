<?php

namespace App\Modules\ServerManagement\ServiceCategory\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Modules\ServerManagement\ServiceCategory\Models\ServiceCategory;
use App\Modules\ServerManagement\ServiceCategory\Resources\ServiceCategoryResource;
use App\Modules\ServerManagement\ServiceCategory\Requests\StoreServiceCategoryRequest;
use App\Modules\ServerManagement\ServiceCategory\Requests\UpdateServiceCategoryRequest;

class ServiceCategoryController extends Controller
{
    public function index() {
        $service_categories = ServiceCategory::all();

        return response()->json(['status' => 'success', 'service_categories' => ServiceCategoryResource::collection($service_categories)]);
    }

    public function store(StoreServiceCategoryRequest $request) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/service_category', $file_name);
            }

            $os = new ServiceCategory();
            $os->name = $request->name;
            $os->image = $file_name ? '/images/service_category/' . $file_name : null;
            $os->save();

            DB::commit();

            return response()->json(['status' => 'success',  'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateServiceCategory(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory) {
        DB::beginTransaction();

        try {
            $file_name = null;
            if($request->hasFile('image') && gettype($request->image) == 'object') {
                //delete old image
                if($serviceCategory->image) {
                    File::delete(public_path('/storage' . $serviceCategory->image));
                }

                $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/service_category', $file_name);
            }

            $serviceCategory->name = $request->name;
            $serviceCategory->image = $file_name ? '/images/service_category/' . $file_name : $serviceCategory->image;
            $serviceCategory->update();

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Successfully created!']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(ServiceCategory $serviceCategory) {
        DB::beginTransaction();

        try {
            if($serviceCategory->image) {
                File::delete(public_path('/storage' . $serviceCategory->image));
            }

            $serviceCategory->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function destroyMultiServiceCategories(Request $request) {
        DB::beginTransaction();

        try {
            $ids = $request->ids;
            if(count($ids) > 0) {
                foreach($ids as $id) {
                    $serviceCategory = ServiceCategory::find($id);
                    if($serviceCategory->image) {
                        File::delete(public_path('/storage' . $serviceCategory->image));
                    }

                    $serviceCategory->delete();
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
