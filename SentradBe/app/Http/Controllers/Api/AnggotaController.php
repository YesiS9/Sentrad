<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    public function index()
    {
        try {
            $anggotas = Anggota::whereNull('deleted_at')->get();

            if (count($anggotas) > 0) {
                Log::info('Data Anggota Berhasil Ditampilkan');
                return response()->json([
                    'data' => $anggotas,
                    'status' => 'success',
                    'message' => 'Data Anggota Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Anggota Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Anggota Kosong',
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
    public function indexByKelompok($kelompok_id)
    {
        try {
            $anggotas = Anggota::where('kelompok_id', $kelompok_id)
                        ->whereNull('deleted_at')
                        ->get();

            if (count($anggotas) > 0) {
                Log::info('Data Anggota untuk Kelompok ID: ' . $kelompok_id . ' Berhasil Ditampilkan');
                return response()->json([
                    'data' => $anggotas,
                    'status' => 'success',
                    'message' => 'Data Anggota untuk Kelompok Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Anggota untuk Kelompok ID: ' . $kelompok_id . ' Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Anggota untuk Kelompok Kosong',
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



    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kelompok_id' => 'required|exists:registrasi_kelompoks,id',
                'anggota' => 'required|array',
                'anggota.*.nama_anggota' => 'required|string|max:100',
                'anggota.*.tgl_lahir' => 'required|date_format:d/m/Y',
                'anggota.*.tgl_gabung' => 'required|date_format:d/m/Y',
                'anggota.*.alamat_anggota' => 'required|string',
                'anggota.*.noTelp_anggota' => 'required|regex:/^08\d{8,12}$/',
                'anggota.*.tingkat_skill' => 'required|string|max:100',
                'anggota.*.peran_anggota' => 'required|string|max:100',
                'anggota.*.status_anggota' => 'required|string|in:Aktif,Nonaktif',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $anggotaData = [];
            foreach ($request->anggota as $anggota) {
                $anggota['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $anggota['tgl_lahir'])->format('Y-m-d');
                $anggota['tgl_gabung'] = Carbon::createFromFormat('d/m/Y', $anggota['tgl_gabung'])->format('Y-m-d');
                $anggota['kelompok_id'] = $request->kelompok_id;
                $newAnggota = Anggota::create($anggota);
                $anggotaData[] = $newAnggota;
            }

            Log::info('Data Anggota Berhasil Ditambahkan');
            return response()->json([
                'data' => $anggotaData,
                'status' => 'success',
                'message' => 'Data Anggota Berhasil Ditambahkan',
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
            $anggota = Anggota::whereNull('deleted_at')->find($id);

            if (!$anggota) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Anggota tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $anggota,
                'status' => 'success',
                'message' => 'Data Anggota Berhasil Ditampilkan',
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
            $anggota = Anggota::whereNull('deleted_at')->find($id);

            if (!$anggota) {
                Log::error('Data Anggota Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Anggota Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'kelompok_id' => 'required|exists:registrasi_kelompoks,id',
                'tingkatan_id' => 'nullable|exists:tingkatans,id',
                'nama_anggota' => 'required|string|max:100',
                'tgl_lahir' => 'required|date',
                'tgl_gabung' => 'required|date',
                'alamat_anggota' => 'required|string',
                'noTelp_anggota' => 'required|string|max:20',
                'tingkat_skill' => 'required|string|max:100',
                'peran_anggota' => 'required|string|max:100',
                'status_anggota' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $anggota->update($request->all());

            Log::info('Data Anggota Berhasil Diupdate');
            return response()->json([
                'data' => $anggota,
                'status' => 'success',
                'message' => 'Data Anggota Berhasil Diupdate',
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
            $anggota = Anggota::whereNull('deleted_at')->find($id);

            if (!$anggota) {
                Log::error('Data Anggota Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Anggota Tidak Ditemukan',
                ], 404);
            }

            if ($anggota->delete()) {
                Log::info('Data Anggota Berhasil Dihapus');
                return response()->json([
                    'data' => $anggota,
                    'status' => 'success',
                    'message' => 'Data Anggota Berhasil Dihapus',
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
