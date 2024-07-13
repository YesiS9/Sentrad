<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSeni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KategoriSeniController extends Controller
{
    public function index()
    {
        try {
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->get();

            if (count($kategoriSeni) > 0) {
                Log::info('Data Kategori Seni Berhasil Ditampilkan');
                return response()->json([
                    'data' => $kategoriSeni,
                    'status' => 'success',
                    'message' => 'Data Kategori Seni Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Kategori Seni Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Kategori Seni Kosong',
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
                'user_id' => 'required|uuid',
                'nama_kategori' => 'required|string|max:100',
                'deskripsi_kategori' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategoriSeni = KategoriSeni::create($storeData);

            Log::info('Data Kategori Seni Berhasil Ditambahkan');
            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Ditambahkan',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Ditampilkan',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                Log::error('Data Kategori Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'user_id' => 'required|uuid',
                'nama_kategori' => 'required|string|max:100',
                'deskripsi_kategori' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategoriSeni->user_id = $request->user_id;
            $kategoriSeni->nama_kategori = $request->nama_kategori;
            $kategoriSeni->deskripsi_kategori = $request->deskripsi_kategori;

            $kategoriSeni->save();

            Log::info('Data Kategori Seni Berhasil Diupdate');
            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Diupdate',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                Log::error('Data Kategori Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni Tidak Ditemukan',
                ], 404);
            }

            if ($kategoriSeni->delete()) {
                Log::info('Data Kategori Seni Berhasil Dihapus');
                return response()->json([
                    'data' => $kategoriSeni,
                    'status' => 'success',
                    'message' => 'Data Kategori Seni Berhasil Dihapus',
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
