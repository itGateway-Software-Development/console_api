<?php

namespace App\Modules\ServerManagement\Location\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Modules\ServerManagement\Location\Models\Location;
use App\Modules\ServerManagement\Location\Resources\LocationResource;
use App\Modules\ServerManagement\Location\Requests\StoreLocationRequest;
use App\Modules\ServerManagement\Location\Requests\UpdateLocationRequest;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all();

        return response()->json(['status' => 'success', 'locations' => LocationResource::collection($locations)]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $file_name = null;
        if($request->hasFile('image') && gettype($request->image) == 'object') {
            $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images/locations', $file_name);
        }

        $location = new Location();
        $location->name = $request->name;
        $location->image = $file_name ? '/images/locations/' . $file_name : null;
        $location->status = $request->status;
        $location->save();

        return response()->json(['status' => 'success', 'location' => $location, 'message' => 'Successfully created!']);
    }



    /**
     * Update the specified resource in storage.
     */
    public function updateLocation(UpdateLocationRequest $request, Location $location)
    {
        $file_name = null;
        if($request->hasFile('image') && gettype($request->image) == 'object') {
            //delete old image
            if($location->image) {
                File::delete(public_path('/storage' . $location->image));
            }

            $file_name = uniqid().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images/locations', $file_name);
        }

        $location->name = $request->name;
        $location->image = $file_name ? '/images/locations/' . $file_name : $location->image;
        $location->status = $request->status ?? $location->status;
        $location->update();

        return response()->json(['status' => 'success', 'location' => $location, 'message' => 'Successfully updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        if($location->image) {
            File::delete(public_path('/storage' . $location->image));
        }
        $location->delete();

        return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
    }

    public function destroyMultiLocations(Request $request) {
        $ids = $request->ids;
        if(count($ids) > 0) {
            foreach($ids as $id) {
                $location = Location::find($id);
                if($location->image) {
                    File::delete(public_path('/storage' . $location->image));
                }
                $location->delete();
            }

            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No locations selected!']);
        }

    }
}
