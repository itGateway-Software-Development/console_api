<?php

namespace App\Modules\ServerManagement\Region\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Modules\ServerManagement\Region\Models\Region;
use App\Modules\ServerManagement\Region\Resources\RegionResource;
use App\Modules\ServerManagement\Region\Requests\StoreRegionRequest;
use App\Modules\ServerManagement\Region\Requests\UpdateRegionRequest;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regions = Region::all();

        return response()->json(['status' => 'success', 'regions' => RegionResource::collection($regions)]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegionRequest $request)
    {
        $region = new Region();
        $region->name = $request->name;
        $region->save();

        return response()->json(['status' => 'success',  'message' => 'Successfully created!']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateRegion(UpdateRegionRequest $request, Region $region)
    {

        $region->name = $request->name;
        $region->update();

        return response()->json(['status' => 'success', 'message' => 'Successfully updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region)
    {
        $region->delete();

        $regions = RegionResource::collection(Region::all());

        return response()->json(['status' => 'success',  'message' => 'Successfully deleted!']);
    }

    public function destroyMultiRegions(Request $request) {
        $ids = $request->ids;
        if(count($ids) > 0) {
            Region::destroy($ids);

            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No locations selected!']);
        }

    }
}
