<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Models\ScrapVehicle;

class ScrapVehicleController extends Controller
{
    /**
     * Customer API to submit a scrap vehicle form
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|string',
            'vehicle_number' => 'required|string',
            'vehicle_brand' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'photos' => 'nullable|array', // Expecting an array of strings (e.g. from upload api)
            'photos.*' => 'string',
            'remark' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 200);
        }

        $scrapVehicle = ScrapVehicle::create([
            'user_uuid' => $request->user()->uuid,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_brand' => $request->vehicle_brand,
            'vehicle_model' => $request->vehicle_model,
            'photos' => $request->photos ?? [],
            'remark' => $request->remark,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Scrap vehicle details submitted successfully',
            'data' => $scrapVehicle
        ], 200);
    }

    /**
     * Customer API to list their own scrap vehicle requests
     */
    public function customerIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:pending,completed',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'data' => ['errors' => $validator->errors()]
            ], 200);
        }

        $query = ScrapVehicle::where('user_uuid', $request->user()->uuid);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $total_count = $query->count();

        $query->orderBy('created_at', 'desc');

        if ($request->has('min') && is_numeric($request->min)) {
            $query->offset((int)$request->min);
        }
        if ($request->has('max') && is_numeric($request->max)) {
            $query->limit((int)$request->max);
        }

        $vehicles = $query->get();

        return response()->json([
            'status' => 1,
            'message' => 'Scrap vehicles fetched successfully',
            'data' => [
                'count' => $total_count,
                'rows' => $vehicles
            ]
        ]);
    }

    /**
     * Admin API to list scrap vehicles
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:pending,completed',
            'search' => 'nullable|string',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'data' => ['errors' => $validator->errors()]
            ], 200);
        }

        $query = ScrapVehicle::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_brand', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $total_count = $query->count();

        $query->orderBy('created_at', 'desc');

        if ($request->has('min') && is_numeric($request->min)) {
            $query->offset((int)$request->min);
        }
        if ($request->has('max') && is_numeric($request->max)) {
            $query->limit((int)$request->max);
        }

        $vehicles = $query->get();

        return response()->json([
            'status' => 1,
            'message' => 'Scrap vehicles fetched successfully',
            'data' => [
                'count' => $total_count,
                'rows' => $vehicles
            ]
        ]);
    }

    /**
     * Admin SSR View for Scrap Vehicles
     */
    public function adminIndex(Request $request)
    {
        $query = ScrapVehicle::with('user')->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_brand', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }

        $vehicles = $query->paginate(15);
        
        return view('admin.scrap_vehicles', compact('vehicles'));
    }

    /**
     * Admin API to update scrap vehicle status
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:scrap_vehicles,id',
            'status' => 'required|string|in:pending,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'data' => ['errors' => $validator->errors()]
            ], 200);
        }

        $vehicle = ScrapVehicle::find($request->id);
        $vehicle->status = $request->status;
        $vehicle->save();

        return response()->json([
            'status' => 1,
            'message' => 'Scrap vehicle status updated successfully',
            'data' => $vehicle
        ]);
    }
}
