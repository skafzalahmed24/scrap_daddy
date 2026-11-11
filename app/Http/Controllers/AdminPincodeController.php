<?php

namespace App\Http\Controllers;

use App\Models\ServicePincode;
use Illuminate\Http\Request;

class AdminPincodeController extends Controller
{
    public function index(Request $request)
    {
        $query = ServicePincode::query()->orderBy('pin_code');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('pin_code', 'like', "%{$s}%")
                    ->orWhere('area', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%");
            });
        }

        return view('admin.pincodes', [
            'pincodes' => $query->paginate(20)->withQueryString(),
            'restrictionActive' => ServicePincode::restrictionActive(),
        ]);
    }

    /** Add one or many PIN codes (comma / space / new-line separated). */
    public function store(Request $request)
    {
        $request->validate([
            'pin_codes' => 'required|string|max:5000',
            'area' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);

        $pins = collect(preg_split('/[\s,;]+/', $request->pin_codes, -1, PREG_SPLIT_NO_EMPTY))->unique()->values();

        $invalid = $pins->reject(fn ($p) => preg_match(ServicePincode::FORMAT, $p));
        if ($invalid->isNotEmpty()) {
            return back()->withInput()->with('error', 'Invalid PIN code(s): ' . $invalid->take(5)->implode(', ') . '. PIN codes must be 6 digits and not start with 0.');
        }

        $added = 0;
        foreach ($pins as $pin) {
            $row = ServicePincode::firstOrNew(['pin_code' => $pin]);
            if (!$row->exists) {
                $added++;
            }
            $row->status = true;
            if ($pins->count() === 1) {
                $row->area = $request->area ?: $row->area;
                $row->city = $request->city ?: $row->city;
            }
            $row->save();
        }

        return back()->with('success', "{$added} new PIN code(s) added; any others were re-enabled.");
    }

    public function toggle($id)
    {
        $row = ServicePincode::findOrFail($id);
        $row->status = !$row->status;
        $row->save();

        return back()->with('success', "PIN code {$row->pin_code} " . ($row->status ? 'enabled.' : 'disabled.'));
    }

    public function destroy($id)
    {
        $row = ServicePincode::findOrFail($id);
        $row->delete();

        return back()->with('success', "PIN code {$row->pin_code} removed.");
    }
}
