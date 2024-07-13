<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilai;
use App\Models\Rubrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RubrikController extends Controller
{
    public function index()
    {
        try {
            $rubrik = Rubrik::whereNull('deleted_at')->get();

            if (count($rubrik) > 0) {
                Log::info('Data Rubrik Berhasil Ditampilkan');
                return response()->json([
                    'data' => $rubrik,
                    'status' => 'success',
                    'message' => 'Data Rubrik Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Rubrik Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Rubrik Kosong',
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
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'nama_penilai' => 'required|string|exists:penilais,nama',
                'nama_rubrik' => 'required|string|max:100',
                'deskripsi_rubrik' => 'required|string',
                'bobot' => 'required|numeric',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $penilai = Penilai::where('nama', $storeData['nama_penilai'])->first();
            if (!$penilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai tidak ditemukan',
                ], 404);
            }

            $storeData['penilai_id'] = $penilai->id;

            $rubrik = Rubrik::create($storeData);

            Log::info('Data Rubrik Berhasil Ditambahkan');
            return response()->json([
                'data' => $rubrik,
                'status' => 'success',
                'message' => 'Data Rubrik Berhasil Ditambahkan',
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
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $rubrik,
                'status' => 'success',
                'message' => 'Data Rubrik Berhasil Ditampilkan',
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
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                Log::error('Data Rubrik Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'nama_penilai' => 'required|string|exists:penilais,nama',
                'nama_rubrik' => 'required|string|max:100',
                'deskripsi_rubrik' => 'required|string',
                'bobot' => 'required|numeric',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $penilai = Penilai::where('nama', $request->nama_penilai)->first();
            if (!$penilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai tidak ditemukan',
                ], 404);
            }

            $rubrik->penilai_id = $penilai->id;
            $rubrik->nama_rubrik = $request->nama_rubrik;
            $rubrik->deskripsi_rubrik = $request->deskripsi_rubrik;
            $rubrik->bobot = $request->bobot;

            $rubrik->save();

            Log::info('Data Rubrik Berhasil Diupdate');
            return response()->json([
                'data' => $rubrik,
                'status' => 'success',
                'message' => 'Data Rubrik Berhasil Diupdate',
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
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                Log::error('Data Rubrik Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik Tidak Ditemukan',
                ], 404);
            }

            if ($rubrik->delete()) {
                Log::info('Data Rubrik Berhasil Dihapus');
                return response()->json([
                    'data' => $rubrik,
                    'status' => 'success',
                    'message' => 'Data Rubrik Berhasil Dihapus',
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
