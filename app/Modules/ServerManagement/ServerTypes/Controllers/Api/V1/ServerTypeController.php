<?php

namespace App\Modules\ServerManagement\ServerTypes\Controllers\Api\V1;

use App\Modules\ServerManagement\ServerTypes\Resources\ServerTypeResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\ServerManagement\ServerTypes\Models\ServerType;

class ServerTypeController extends Controller
{
    public function index()
    {
        $serverTypes = ServerType::all();

        return response()->json(['status' => 'success', 'serverTypes' => ServerTypeResource::collection($serverTypes)]);
    }

    public function store(Request $request)
    {

       $serverType = new ServerType();
       $serverType->name = $request->name;
       $serverType->status = $request->status;
       $serverType->save();

       return response()->json(['status' => 'success', 'serverType' => $serverType, 'message' => 'Successfully created!']);
    }

    public function updateServerType(Request $request, ServerType $serverType)
    {
        $serverType->name = $request->name;
        $serverType->status = $request->status ?? $serverType->status;
        $serverType->update();

        return response()->json(['status' => 'success', 'serverType' => $serverType, 'message' => 'Successfully updated!']);
    }

    public function destroy(ServerType $serverType)
    {

        $serverType->delete();

        return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
    }

    public function destroyMultiServerTypes(Request $request) {
        $ids = $request->ids;
        if(count($ids) > 0) {
            ServerType::destroy($ids);

            return response()->json(['status' => 'success', 'message' => 'Successfully deleted!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No locations selected!']);
        }

    }
}
