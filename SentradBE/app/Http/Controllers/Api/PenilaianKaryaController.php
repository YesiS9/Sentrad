<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenilaianKarya;
use App\Models\Penilai;
use App\Models\Rubrik;
use App\Models\RubrikPenilaian;
use App\Models\KuotaPenilai;
use App\Models\Tingkatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PenilaianKaryaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $penilaiId = $request->input('penilai_id');

            if (!$penilaiId) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Penilai ID tidak ditemukan dalam permintaan',
                ], 400);
            }

            $kuota = DB::table('kuota_penilais')
                ->select('id')
                ->where('penilai_id', $penilaiId)
                ->first();

            if (!$kuota) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Kuota tidak ditemukan untuk Penilai ID ini',
                ], 404);
            }

            $kuotaId = $kuota->id;

            $perPage = $request->input('per_page', 10);

            $penilaianKarya = PenilaianKarya::where('kuota_id', $kuotaId)
                ->with(['registrasiIndividu', 'registrasiKelompok'])
                ->select('id','kuota_id', 'regisIndividu_id', 'regisKelompok_id', 'tingkatan_id', 'tgl_penilaian', 'total_nilai', 'komentar')
                ->paginate($perPage);

            if ($penilaianKarya->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $penilaianKarya->items(),
                    'current_page' => $penilaianKarya->currentPage(),
                    'last_page' => $penilaianKarya->lastPage(),
                    'per_page' => $penilaianKarya->perPage(),
                    'total' => $penilaianKarya->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Penilaian Karya tidak ditemukan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




    public function getKuotaId(Request $request)
    {
        $request->validate([
            'penilai_id' => 'required|exists:penilais,id',
        ]);

        $kuotaPenilai = DB::table('kuota_penilais')
            ->where('penilai_id', $request->penilai_id)
            ->first();

        if (!$kuotaPenilai) {
            return response()->json(['error' => 'Kuota tidak ditemukan untuk penilai ini.'], 404);
        }

        return response()->json(['kuota_id' => $kuotaPenilai->id, 'kuota_terpakai' => $kuotaPenilai->kuota_terpakai], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kuota_id' => 'required|exists:kuota_penilais,id',
                'regisIndividu_id' => 'nullable|exists:registrasi_individus,id',
                'regisKelompok_id' => 'nullable|exists:registrasi_kelompoks,id',
                'rubrik_penilaians' => 'required|array|min:5',
                'rubrik_penilaians.*.nama_rubrik' => 'required|exists:rubriks,nama_rubrik',
                'rubrik_penilaians.*.skor' => 'required|numeric|min:1|max:100',
                'komentar' => 'nullable|string|max:500',
            ]);

            $total_nilai = array_sum(array_column($validated['rubrik_penilaians'], 'skor'));

            $penilai = DB::table('penilais')
                ->where('id', $validated['kuota_id'])
                ->first();

            $kuotaPenilai = DB::table('kuota_penilais')
                ->where('id', $validated['kuota_id'])
                ->first();

            if ($kuotaPenilai && $penilai && $kuotaPenilai->kuota_terpakai <= $penilai->kuota) {
                return response()->json(['error' => 'Kuota penilai sudah habis.'], 400);
            }

            $tingkatan = DB::table('tingkatans')
                ->where('nilai_min', '<=', $total_nilai)
                ->where('nilai_max', '>=', $total_nilai)
                ->first();

            if (!$tingkatan) {
                return response()->json(['error' => 'Total nilai tidak sesuai dengan tingkatan yang tersedia.'], 400);
            }

            // Create PenilaianKarya
            $penilaianKarya = PenilaianKarya::create([
                'kuota_id' => $validated['kuota_id'],
                'regisIndividu_id' => $validated['regisIndividu_id'],
                'regisKelompok_id' => $validated['regisKelompok_id'],
                'tgl_penilaian' => now(),
                'total_nilai' => $total_nilai,
                'komentar' => $validated['komentar'],
                'tingkatan_id' => $tingkatan->id,
            ]);

            foreach ($validated['rubrik_penilaians'] as $rubrik) {
                $rubrikId = DB::table('rubriks')
                    ->where('nama_rubrik', $rubrik['nama_rubrik'])
                    ->value('id');

                if ($rubrikId) {
                    RubrikPenilaian::create([
                        'rubrik_id' => $rubrikId,
                        'penilaian_karya_id' => $penilaianKarya->id,
                        'skor' => $rubrik['skor'],
                    ]);
                }
            }


            DB::table('kuota_penilais')
                ->where('id', $validated['kuota_id'])
                ->update([
                    'kuota_terpakai' => DB::raw('kuota_terpakai + 1')
                ]);

            if ($validated['regisIndividu_id']) {
                DB::table('registrasi_individus')
                    ->where('id', $validated['regisIndividu_id'])
                    ->update(['status_individu' => 'Penilaian Selesai']);
            }

            if ($validated['regisKelompok_id']) {
                DB::table('registrasi_kelompoks')
                    ->where('id', $validated['regisKelompok_id'])
                    ->update(['status_kelompok' => 'Penilaian Selesai']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been saved successfully.',
                'data' => $penilaianKarya,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }




    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rubrik_penilaians' => 'required|array',
                'rubrik_penilaians.*.nama_rubrik' => 'required|string|exists:rubriks,nama_rubrik',
                'rubrik_penilaians.*.skor' => 'required|numeric|min:1|max:100',
                'komentar' => 'nullable|string|max:500',
            ]);

            $total_nilai = array_sum(array_column($validated['rubrik_penilaians'], 'skor'));

            $penilaianKarya = PenilaianKarya::findOrFail($id);

            $penilaianKarya->update([
                'total_nilai' => $total_nilai,
                'komentar' => $validated['komentar'],
                'tgl_penilaian' => now(),
            ]);

            foreach ($validated['rubrik_penilaians'] as $rubrikData) {
                $rubrik = Rubrik::where('nama_rubrik', $rubrikData['nama_rubrik'])->first();

                if ($rubrik) {
                    $rubrikPenilaian = RubrikPenilaian::where('penilaian_karya_id', $id)
                        ->where('rubrik_id', $rubrik->id)
                        ->first();

                    if ($rubrikPenilaian) {
                        $rubrikPenilaian->update([
                            'skor' => $rubrikData['skor'],
                        ]);
                        Log::info('Rubrik updated: ' . $rubrikPenilaian->rubrik_id . ' with score: ' . $rubrikData['skor']);
                    } else {
                        Log::warning('RubrikPenilaian not found for penilaian_karya_id: ' . $id . ' and rubrik_id: ' . $rubrik->id);
                    }
                } else {
                    Log::warning('Rubrik not found with nama_rubrik: ' . $rubrikData['nama_rubrik']);
                }
            }

            $tingkatan = DB::table('tingkatans')
                ->where('nilai_min', '<=', $total_nilai)
                ->where('nilai_max', '>=', $total_nilai)
                ->first();

            if (!$tingkatan) {
                return response()->json(['error' => 'Total nilai tidak sesuai dengan tingkatan yang tersedia.'], 400);
            }

            $penilaianKarya->update([
                'tingkatan_id' => $tingkatan->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated successfully.',
                'data' => $penilaianKarya,
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }








    public function show($id)
    {
        try {
            // Ambil data PenilaianKarya beserta rubrik penilaian yang terkait
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')->find($id);

            if (!$penilaianKarya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya tidak ditemukan',
                ], 404);
            }

            // Ambil rubrik-rubrik yang terkait dengan PenilaianKarya
            $rubrikPenilaians = RubrikPenilaian::where('penilaian_karya_id', $penilaianKarya->id)
                ->join('rubriks', 'rubrik_penilaians.rubrik_id', '=', 'rubriks.id')
                ->select('rubriks.nama_rubrik', 'rubrik_penilaians.skor')
                ->get();

            return response()->json([
                'data' => [
                    'penilaianKarya' => $penilaianKarya,
                    'rubrik_penilaians' => $rubrikPenilaians
                ],
                'status' => 'success',
                'message' => 'Data Penilaian Karya Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function destroy($id)
    {
        try {
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')->find($id);

            if (!$penilaianKarya) {
                Log::error('Data Penilaian Karya Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya Tidak Ditemukan',
                ], 404);
            }

            if ($penilaianKarya->delete()) {
                Log::info('Data Penilaian Karya Berhasil Dihapus');
                return response()->json([
                    'data' => $penilaianKarya,
                    'status' => 'success',
                    'message' => 'Data Penilaian Karya Berhasil Dihapus',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
