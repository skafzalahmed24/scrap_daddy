<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('user')->latest();

        if ($request->filled('status') && in_array($request->status, SupportTicket::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        return view('admin.tickets', [
            'tickets' => $query->paginate(20)->withQueryString(),
            'openCount' => SupportTicket::where('status', 'open')->count(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,closed',
            'admin_reply' => 'nullable|string|max:2000',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->status = $data['status'];
        if (($data['admin_reply'] ?? '') !== '' && $data['admin_reply'] !== $ticket->admin_reply) {
            $ticket->admin_reply = trim(strip_tags($data['admin_reply']));
            $ticket->replied_at = now();
        }
        $ticket->save();

        return back()->with('success', 'Ticket updated.');
    }
}
