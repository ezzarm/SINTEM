<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // GET /laporan/buat — anonymous complaint form
    public function create()
    {
        return view('laporan.buat');
    }

    // POST /laporan/buat — store anonymous complaint
    public function store(Request $request)
    {
        $request->validate([
            'category_id'  => 'required|integer|exists:report_categories,id',
            'title'        => 'required|string|max:255',
            'report_content' => 'required|string',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Generate unique ticket number e.g. TKT-042
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

        // Store photo if uploaded
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('upload/photos/reports', 'public');
            DB::table('photos')->insert([
                'source_type' => 'anonymous_report',
                'source_id'   => $reportId,
                'file_name'   => $request->file('photo')->getClientOriginalName(),
                'file_path'   => $path,
                'file_type'   => $request->file('photo')->getMimeType(),
                'file_size'   => $request->file('photo')->getSize(),
                'uploaded_by' => null, // anonymous
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return redirect()->route('pengumuman.index')
            ->with('success', "Laporan berhasil dikirim. Nomor tiket kamu: {$ticket}");
    }

    // GET /laporan/temuan — view own temuan reports (Manajemen Laporan sub-menu)
    public function temuan()
    {
        return view('laporan.temuan');
    }

    // GET /laporan/anonim — view own anonymous reports (Manajemen Laporan sub-menu)
    public function anonim()
    {
        return view('laporan.anonim');
    }
}