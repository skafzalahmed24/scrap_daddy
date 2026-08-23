<?php

namespace App\Http\Controllers;

use App\Models\RewardConfiguration;
use App\Models\RewardSetting;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    // GET configurations
    public function index(Request $request)
    {
        $configs = RewardConfiguration::latest()->paginate(10);
        return response()->json($configs);
    }

    // POST create configuration
    public function store(Request $request)
    {
        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'reward_coins' => 'required|integer|min:1',
            'status' => 'required|boolean',
            'validity_days' => 'nullable|integer|min:1',
        ]);

        // Check for overlapping ranges
        $overlap = RewardConfiguration::where(function($q) use ($request) {
            $q->whereBetween('min_amount', [$request->min_amount, $request->max_amount])
              ->orWhereBetween('max_amount', [$request->min_amount, $request->max_amount])
              ->orWhere(function($q2) use ($request) {
                  $q2->where('min_amount', '<=', $request->min_amount)
                     ->where('max_amount', '>=', $request->max_amount);
              });
        })->exists();

        if ($overlap) {
            return response()->json(['message' => 'The amount range overlaps with an existing configuration.'], 422);
        }

        $config = RewardConfiguration::create($request->only(['min_amount', 'max_amount', 'reward_coins', 'status', 'validity_days']));
        return response()->json(['message' => 'Configuration created successfully', 'config' => $config]);
    }

    // GET single configuration
    public function show($id)
    {
        $config = RewardConfiguration::findOrFail($id);
        return response()->json($config);
    }

    // POST update configuration
    public function update(Request $request, $id)
    {
        $config = RewardConfiguration::findOrFail($id);
        
        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'reward_coins' => 'required|integer|min:1',
            'status' => 'required|boolean',
            'validity_days' => 'nullable|integer|min:1',
        ]);

        // Check for overlapping ranges
        $overlap = RewardConfiguration::where('id', '!=', $id)
            ->where(function($q) use ($request) {
                $q->whereBetween('min_amount', [$request->min_amount, $request->max_amount])
                  ->orWhereBetween('max_amount', [$request->min_amount, $request->max_amount])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('min_amount', '<=', $request->min_amount)
                         ->where('max_amount', '>=', $request->max_amount);
                  });
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'The amount range overlaps with an existing configuration.'], 422);
        }

        $config->update($request->only(['min_amount', 'max_amount', 'reward_coins', 'status', 'validity_days']));
        return response()->json(['message' => 'Configuration updated successfully', 'config' => $config]);
    }

    // DELETE configuration
    public function destroy($id)
    {
        $config = RewardConfiguration::findOrFail($id);
        $config->delete();
        return response()->json(['message' => 'Configuration deleted successfully']);
    }

    // POST toggle status
    public function toggleStatus(Request $request, $id)
    {
        $config = RewardConfiguration::findOrFail($id);
        $request->validate(['status' => 'required|boolean']);
        $config->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated successfully', 'config' => $config]);
    }

    // GET settings
    public function getSettings()
    {
        $setting = RewardSetting::first();
        if (!$setting) {
            $setting = RewardSetting::create(['coin_value_in_rupees' => 0.10]);
        }
        return response()->json($setting);
    }

    // POST update settings
    public function updateSettings(Request $request)
    {
        $request->validate([
            'coin_value_in_rupees' => 'required|numeric|min:0'
        ]);

        $setting = RewardSetting::first();
        if (!$setting) {
            $setting = RewardSetting::create(['coin_value_in_rupees' => $request->coin_value_in_rupees]);
        } else {
            $setting->update(['coin_value_in_rupees' => $request->coin_value_in_rupees]);
        }

        return response()->json(['message' => 'Settings updated successfully', 'setting' => $setting]);
    }
}
