<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomenProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KomenProyekController extends Controller
{
    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'proyek_id' => 'required|string',
                'isi_komenProyek' => 'required|string',
                'waktu_komenProyek' => 'required|date',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $komenProyek = KomenProyek::create($storeData);

            Log::info('Komentar Proyek Berhasil Ditambahkan');
            return response()->json([
                'data' => $komenProyek,
                'status' => 'success',
                'message' => 'Komentar Proyek Berhasil Ditambahkan',
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
            $komenProyek = KomenProyek::whereNull('deleted_at')->find($id);

            if (!$komenProyek) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Proyek tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $komenProyek,
                'status' => 'success',
                'message' => 'Komentar Proyek Berhasil Ditampilkan',
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
            $komenProyek = KomenProyek::whereNull('deleted_at')->find($id);

            if (!$komenProyek) {
                Log::error('Komentar Proyek Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Proyek Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'proyek_id' => 'required|string',
                'isi_komenProyek' => 'required|string',
                'waktu_komenProyek' => 'required|date',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $komenProyek->update($request->all());

            Log::info('Komentar Proyek Berhasil Diupdate');
            return response()->json([
                'data' => $komenProyek,
                'status' => 'success',
                'message' => 'Komentar Proyek Berhasil Diupdate',
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
            $komenProyek = KomenProyek::whereNull('deleted_at')->find($id);

            if (!$komenProyek) {
                Log::error('Komentar Proyek Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Proyek Tidak Ditemukan',
                ], 404);
            }

            if ($komenProyek->delete()) {
                Log::info('Komentar Proyek Berhasil Dihapus');
                return response()->json([
                    'data' => $komenProyek,
                    'status' => 'success',
                    'message' => 'Komentar Proyek Berhasil Dihapus',
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
