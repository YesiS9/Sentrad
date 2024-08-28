<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenilaianKarya;
use App\Models\Penilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PenilaianKaryaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $id = Auth::id();

            if (!$user->penilai) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Penilai not found for the user',
                ], 404);
            }

            $penilaiId = $user->penilai->id;
            $perPage = $request->input('per_page', 10);

            $penilaianKarya = PenilaianKarya::where('penilai_id', $penilaiId)
                ->select(
                    'penilaian_karya.id',
                    'penilaian_karya.tgl_penilaian',
                    'penilaian_karya.total_nilai',
                    DB::raw('COALESCE(registrasi_individu.nama_seniman, registrasi_kelompok.nama_kelompok) as nama')
                )
                ->leftJoin('registrasi_individu', 'penilaian_karya.seniman_id', '=', 'registrasi_individu.id')
                ->leftJoin('registrasi_kelompok', 'penilaian_karya.seniman_id', '=', 'registrasi_kelompok.id')
                ->paginate($perPage);

            if ($penilaianKarya->isNotEmpty()) {
                Log::info('Data Penilaian Karya Berhasil Ditampilkan');
                return response()->json([
                    'data' => $penilaianKarya->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'tgl_penilaian' => $item->tgl_penilaian,
                            'total_nilai' => $item->total_nilai,
                            'nama' => $item->nama ?? 'N/A',
                        ];
                    }),
                    'id' => $id,
                    'current_page' => $penilaianKarya->currentPage(),
                    'per_page' => $penilaianKarya->perPage(),
                    'total' => $penilaianKarya->total(),
                    'last_page' => $penilaianKarya->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Penilaian Karya Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilaian Karya Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Penilaian Karya Kosong',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }









    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_penilai' => 'required|string',
                'total_nilai' => 'required|numeric',
                'komentar' => 'required|string',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $penilai = Penilai::where('nama_penilai', $request->nama_penilai)->first();
            if (!$penilai) {
                Log::error('Penilai not found');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai not found',
                ], 404);
            }

            $penilaianKarya = PenilaianKarya::create([
                'penilai_id' => $penilai->id,
                'tgl_penilaian' => now(),
                'total_nilai' => $request->total_nilai,
                'komentar' => $request->komentar,
            ]);

            Log::info('Data Penilaian Karya Berhasil Ditambahkan');
            return response()->json([
                'data' => $penilaianKarya,
                'status' => 'success',
                'message' => 'Data Penilaian Karya Berhasil Ditambahkan',
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

    public function show($id)
    {
        try {
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')->find($id);

            if (!$penilaianKarya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $penilaianKarya,
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

    public function update(Request $request, $id)
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

            $validate = Validator::make($request->all(), [
                'nama_penilai' => 'required|string',
                'total_nilai' => 'required|numeric',
                'komentar' => 'required|string',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $penilai = Penilai::where('nama_penilai', $request->nama_penilai)->first();
            if (!$penilai) {
                Log::error('Penilai not found');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai not found',
                ], 404);
            }

            $penilaianKarya->penilai_id = $penilai->id;
            $penilaianKarya->tgl_penilaian = now();
            $penilaianKarya->total_nilai = $request->total_nilai;
            $penilaianKarya->komentar = $request->komentar;

            $penilaianKarya->save();

            Log::info('Data Penilaian Karya Berhasil Diupdate');
            return response()->json([
                'data' => $penilaianKarya,
                'status' => 'success',
                'message' => 'Data Penilaian Karya Berhasil Diupdate',
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
