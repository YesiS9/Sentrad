<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PortofolioController extends Controller
{
    public function index()
    {
        try {
            $portofolios = Portofolio::whereNull('deleted_at')->get();

            if (count($portofolios) > 0) {
                Log::info('Data Portofolio Berhasil Ditampilkan');
                return response()->json([
                    'data' => $portofolios,
                    'status' => 'success',
                    'message' => 'Data Portofolio Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Portofolio Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Portofolio Kosong',
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
                'kelompok_id' => 'nullable|exists:registrasi_kelompoks,id',
                'seniman_id' => 'nullable|exists:seniman,id',
                'judul_portofolio' => 'required|string|max:100',
                'tgl_dibuat' => 'required|date',
                'deskripsi_portofolio' => 'required',
                'jumlah_karya' => 'required|integer',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $portofolio = Portofolio::create($request->all());

            Log::info('Data Portofolio Berhasil Ditambahkan');
            return response()->json([
                'data' => $portofolio,
                'status' => 'success',
                'message' => 'Data Portofolio Berhasil Ditambahkan',
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
            $portofolio = Portofolio::whereNull('deleted_at')->find($id);

            if (!$portofolio) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Portofolio tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $portofolio,
                'status' => 'success',
                'message' => 'Data Portofolio Berhasil Ditampilkan',
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
            $portofolio = Portofolio::whereNull('deleted_at')->find($id);

            if (!$portofolio) {
                Log::error('Data Portofolio Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Portofolio Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'kelompok_id' => 'nullable|exists:registrasi_kelompoks,id',
                'seniman_id' => 'nullable|exists:seniman,id',
                'judul_portofolio' => 'required|string|max:100',
                'tgl_dibuat' => 'required|date',
                'deskripsi_portofolio' => 'required',
                'jumlah_karya' => 'required|integer',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $portofolio->update($request->all());

            Log::info('Data Portofolio Berhasil Diupdate');
            return response()->json([
                'data' => $portofolio,
                'status' => 'success',
                'message' => 'Data Portofolio Berhasil Diupdate',
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
            $portofolio = Portofolio::whereNull('deleted_at')->find($id);

            if (!$portofolio) {
                Log::error('Data Portofolio Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Portofolio Tidak Ditemukan',
                ], 404);
            }

            if ($portofolio->delete()) {
                Log::info('Data Portofolio Berhasil Dihapus');
                return response()->json([
                    'data' => $portofolio,
                    'status' => 'success',
                    'message' => 'Data Portofolio Berhasil Dihapus',
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
