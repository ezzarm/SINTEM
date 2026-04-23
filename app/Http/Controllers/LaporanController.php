<?php

namespace App\Http\Controllers;

use App\Http\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // ==========================================
    // BAGIAN 1: LAPORAN ANONIM
    // ==========================================

    public function create()
    {
        $categories = DB::table('report_categories')->orderBy('id')->get();
        return view('laporan.buat', compact('categories'));
    }

    public function store(Request $request)
    {
        // ── DIAGNOSTIC: tangkap semua error dan tampilkan, jangan 500 ──
        set_exception_handler(null);
        try {
            $request->validate([
                'category_id'    => 'required|integer|exists:report_categories,id',
                'report_content' => 'required|string|min:10',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            // Generate unique ticket number, e.g. TKT-00123
            do {
                $ticket = 'TKT-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            } while (DB::table('anonymous_reports')->where('ticket_number', $ticket)->exists());

            $reportId = DB::table('anonymous_reports')->insertGetId([
                'ticket_number'  => $ticket,
                'category_id'    => $request->category_id,
                'report_content' => $request->report_content,
                'status'         => 'pending',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::store($request->file('photo'), 'anonymous_report', $reportId, null);
            }

            $myTickets   = session('my_tickets', []);
            $myTickets[] = $ticket;
            session(['my_tickets' => $myTickets]);

            return redirect()->route('laporan.anonim')
                ->with('success', "Laporan berhasil dikirim! Nomor tiketmu: {$ticket}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            // Tampilkan error detail supaya bisa di-debug — HAPUS setelah fix
            return response()->json([
                'error'   => $e->getMessage(),
                'class'   => get_class($e),
                'file'    => str_replace(base_path(), '', $e->getFile()),
                'line'    => $e->getLine(),
                'trace'   => collect($e->getTrace())->take(8)->map(fn($t) => [
                    'file' => str_replace(base_path(), '', $t['file'] ?? ''),
                    'line' => $t['line'] ?? '',
                    'func' => ($t['class'] ?? '') . ($t['type'] ?? '') . ($t['function'] ?? ''),
                ])->toArray(),
                'request' => [
                    'has_photo'      => $request->hasFile('photo'),
                    'category_id'    => $request->category_id,
                    'has_content'    => !empty($request->report_content),
                    'content_length' => strlen($request->report_content ?? ''),
                    'photo_error'    => $request->hasFile('photo') ? $request->file('photo')->getError() : null,
                ],
            ], 200);
        }
    }

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

    public function destroyAnonim($id)
    {
        $myTickets = session('my_tickets', []);

        $report = DB::table('anonymous_reports')
            ->where('id', $id)
            ->where('status', 'pending')
            ->whereIn('ticket_number', $myTickets)
            ->first();

        if (!$report) {
            return redirect()->route('laporan.anonim')->with('error', 'Laporan tidak ditemukan.');
        }

        PhotoHelper::delete('anonymous_report', $id);
        DB::table('anonymous_reports')->where('id', $id)->delete();

        $updated = array_values(array_filter($myTickets, fn($t) => $t !== $report->ticket_number));
        session(['my_tickets' => $updated]);

        return redirect()->route('laporan.anonim')->with('success', 'Laporan berhasil dihapus.');
    }

    // ==========================================
    // BAGIAN 2: LAPORAN TEMUAN (LOST & FOUND)
    // ==========================================

    public function temuan(Request $request)
    {
        $sort   = $request->get('sort', 'terbaru');
        $search = $request->get('search', '');

        $query = DB::table('lost_founds')
            ->where('user_id', Auth::id());

        if ($search) {
            $query->where('item_name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', $sort === 'terlama' ? 'asc' : 'desc');
        $items = $query->paginate(15);

        $ids      = $items->pluck('id')->toArray();
        $photoMap = [];
        if (!empty($ids)) {
            $photoMap = DB::table('photos')
                ->where('source_type', 'lost_found')
                ->whereIn('source_id', $ids)
                ->get()->keyBy('source_id')->toArray();
        }

        return view('laporan.temuan', compact('items', 'sort', 'search', 'photoMap'));
    }

    public function storeTemuan(Request $request)
    {
        try {
            $request->validate([
                'type'        => 'required|in:found,lost',
                'item_name'   => 'required|string|max:100',
                'description' => 'nullable|string',
                'found_at'    => 'nullable|string|max:150',
                'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $reportId = DB::table('lost_founds')->insertGetId([
                'user_id'     => Auth::id(),
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::store($request->file('photo'), 'lost_found', $reportId, Auth::id());
            }

            return redirect()->route('laporan.temuan')
                ->with('success', 'Laporan Temuan/Kehilangan berhasil diposting.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateTemuan(Request $request, $id)
    {
        try {
            $request->validate([
                'type'        => 'required|in:found,lost',
                'item_name'   => 'required|string|max:100',
                'description' => 'nullable|string',
                'found_at'    => 'nullable|string|max:150',
                'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $report = DB::table('lost_founds')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->first();

            if (!$report) {
                return redirect()->route('laporan.temuan')->with('error', 'Laporan tidak ditemukan.');
            }

            DB::table('lost_founds')->where('id', $id)->update([
                'type'        => $request->type,
                'item_name'   => $request->item_name,
                'description' => $request->description,
                'found_at'    => $request->found_at,
                'updated_at'  => now(),
            ]);

            if ($request->hasFile('photo')) {
                PhotoHelper::delete('lost_found', $id);
                PhotoHelper::store($request->file('photo'), 'lost_found', $id, Auth::id());
            }

            return redirect()->route('laporan.temuan')->with('success', 'Laporan berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui laporan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyTemuan($id)
    {
        $report = DB::table('lost_founds')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$report) {
            return redirect()->route('laporan.temuan')->with('error', 'Laporan tidak bisa dihapus.');
        }

        PhotoHelper::delete('lost_found', $id);
        DB::table('lost_founds')->where('id', $id)->delete();

        return redirect()->route('laporan.temuan')->with('success', 'Laporan berhasil dihapus.');
    }
}