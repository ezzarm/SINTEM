<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // ── GET /laporan/buat ──
    public function create()
    {
        return view('laporan.buat');
    }

    // ── POST /laporan/buat ──
    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => 'required|integer|exists:report_categories,id',
            'title'          => 'required|string|max:255',
            'report_content' => 'required|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $lastId = DB::table('anonymous_reports')->max('id') ?? 0;
        $ticket = 'TKT-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        $reportId = DB::table('anonymous_reports')->insertGetId([
            'ticket_number'  => $ticket,
            'category_id'    => $request->category_id,
            'report_content' => $request->report_content,
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('upload/photos/reports', 'public');
            DB::table('photos')->insert([
                'source_type' => 'anonymous_report',
                'source_id'   => $reportId,
                'file_name'   => $request->file('photo')->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => $request->file('photo')->getMimeType(),
                'file_size'   => $request->file('photo')->getSize(),
                'uploaded_by' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Store ticket in session so the user can track it
        $tickets   = session('my_tickets', []);
        $tickets[] = $ticket;
        session(['my_tickets' => $tickets]);

        return redirect()->route('laporan.anonim')
            ->with('success', "Laporan berhasil dikirim. Nomor tiket kamu: {$ticket}");
    }

    // ── GET /laporan/temuan — user's own lost/found reports ──
    public function temuan(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');

        $query = DB::table('lost_founds')
            ->where('user_id', Auth::user()->id);

        if ($search) {
            $query->where('item_name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', $sort === 'terlama' ? 'asc' : 'desc');
        $items = $query->paginate(15);

        // Build photo map keyed by lost_found id
        $ids = $items->pluck('id')->toArray();
        $photoMap = [];
        if (!empty($ids)) {
            $photoMap = DB::table('photos')
                ->where('source_type', 'lost_found')
                ->whereIn('source_id', $ids)
                ->get()
                ->keyBy('source_id')
                ->toArray();
        }

        return view('laporan.temuan', compact('items', 'sort', 'search', 'photoMap'));
    }

    // ── PUT /temuan/{id} ──
    public function updateTemuan(Request $request, $id)
    {
        $request->validate([
            'type'        => 'required|in:found,lost',
            'item_name'   => 'required|string|max:100',
            'description' => 'nullable|string',
            'found_at'    => 'nullable|string|max:150',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $report = DB::table('lost_founds')
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->where('status', 'pending')
            ->first();

        if (!$report) {
            return redirect()->route('laporan.temuan')
                ->with('error', 'Laporan tidak ditemukan atau tidak bisa diedit.');
        }

        DB::table('lost_founds')->where('id', $id)->update([
            'type'        => $request->type,
            'item_name'   => $request->item_name,
            'description' => $request->description,
            'found_at'    => $request->found_at,
            'updated_at'  => now(),
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo record
            DB::table('photos')
                ->where('source_type', 'lost_found')
                ->where('source_id', $id)
                ->delete();

            $path = $request->file('photo')->store('upload/photos/lost_founds', 'public');
            DB::table('photos')->insert([
                'source_type' => 'lost_found',
                'source_id'   => $id,
                'file_name'   => $request->file('photo')->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => $request->file('photo')->getMimeType(),
                'file_size'   => $request->file('photo')->getSize(),
                'uploaded_by' => Auth::user()->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return redirect()->route('laporan.temuan')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // ── DELETE /temuan/{id} ──
    public function destroyTemuan($id)
    {
        $report = DB::table('lost_founds')
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->where('status', 'pending')
            ->first();

        if (!$report) {
            return redirect()->route('laporan.temuan')
                ->with('error', 'Laporan tidak ditemukan atau tidak bisa dihapus.');
        }

        DB::table('photos')
            ->where('source_type', 'lost_found')
            ->where('source_id', $id)
            ->delete();

        DB::table('lost_founds')->where('id', $id)->delete();

        return redirect()->route('laporan.temuan')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    // ── GET /laporan/anonim ──
    public function anonim(Request $request)
    {
        $myTickets = session('my_tickets', []);
        $sort      = $request->get('sort', 'terbaru');
        $search    = $request->get('search', '');

        $query = DB::table('anonymous_reports')
            ->join('report_categories', 'anonymous_reports.category_id', '=', 'report_categories.id')
            ->select('anonymous_reports.*', 'report_categories.category_name');

        if (!empty($myTickets)) {
            $query->whereIn('anonymous_reports.ticket_number', $myTickets);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($search) {
            $query->where('anonymous_reports.report_content', 'like', "%{$search}%");
        }

        $query->orderBy('anonymous_reports.created_at', $sort === 'terlama' ? 'asc' : 'desc');
        $items = $query->paginate(15);

        return view('laporan.anonim', compact('items', 'sort', 'search'));
    }

    // ── DELETE /laporan/anonim/{id} ──
    public function destroyAnonim($id)
    {
        $myTickets = session('my_tickets', []);

        $report = DB::table('anonymous_reports')
            ->where('id', $id)
            ->where('status', 'pending')
            ->whereIn('ticket_number', $myTickets)
            ->first();

        if (!$report) {
            return redirect()->route('laporan.anonim')
                ->with('error', 'Laporan tidak ditemukan atau tidak bisa dihapus.');
        }

        DB::table('anonymous_reports')->where('id', $id)->delete();

        $updated = array_values(array_filter($myTickets, fn($t) => $t !== $report->ticket_number));
        session(['my_tickets' => $updated]);

        return redirect()->route('laporan.anonim')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}