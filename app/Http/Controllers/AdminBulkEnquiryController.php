<?php

namespace App\Http\Controllers;

use App\Models\BulkEnquiry;
use App\Models\BulkEnquiryField;
use Illuminate\Http\Request;

/** Admin: bulk enquiry inbox + the form builder (dynamic fields / dropdowns). */
class AdminBulkEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = BulkEnquiry::latest();
        if (in_array($status, ['new', 'contacted', 'closed'], true)) {
            $query->where('status', $status);
        }

        return view('admin.bulk-enquiries', [
            'enquiries' => $query->paginate(15)->withQueryString(),
            'fields' => BulkEnquiryField::orderBy('sort')->orderBy('id')->get(),
            'counts' => BulkEnquiry::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    private function rules(): array
    {
        return [
            'label' => 'required|string|max:120',
            'type' => 'required|in:' . implode(',', array_keys(BulkEnquiryField::TYPES)),
            'options' => 'nullable|string|max:2000',   // one choice per line / comma separated
            'sort' => 'nullable|integer|min:0|max:999',
        ];
    }

    private function payload(Request $request, bool $creating = false): array
    {
        $data = $request->validate($this->rules());
        $options = null;
        if ($data['type'] === 'dropdown') {
            $options = collect(preg_split('/[\n,]+/', (string) ($data['options'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
                ->map(fn ($o) => trim($o))->filter()->unique()->values()->all();
            abort_if(count($options) < 2, 422, 'A dropdown needs at least two choices.');
        }

        return [
            'label' => $data['label'],
            'type' => $data['type'],
            'options' => $options,
            'required' => $request->boolean('required'),
            'is_active' => $creating ? true : $request->boolean('is_active'),
            'sort' => (int) ($data['sort'] ?? 0),
        ];
    }

    public function storeField(Request $request)
    {
        BulkEnquiryField::create($this->payload($request, true));

        return back()->with('success', 'Field added to the enquiry form.');
    }

    public function updateField(Request $request, $id)
    {
        BulkEnquiryField::findOrFail($id)->update($this->payload($request));

        return back()->with('success', 'Field updated.');
    }

    public function destroyField($id)
    {
        BulkEnquiryField::findOrFail($id)->delete();

        return back()->with('success', 'Field removed. Past enquiries keep their saved answers.');
    }

    public function updateEnquiry(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:new,contacted,closed',
            'admin_note' => 'nullable|string|max:1000',
        ]);
        BulkEnquiry::findOrFail($id)->update($data);

        return back()->with('success', 'Enquiry updated.');
    }
}
