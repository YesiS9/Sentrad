<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomenForum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KomenForumController extends Controller
{
    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'forum_id' => 'required|string',
                'isi_komenForum' => 'required|string',
                'waktu_komenForum' => 'required|date',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $komenForum = KomenForum::create($storeData);

            Log::info('Komentar Forum Berhasil Ditambahkan');
            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Ditambahkan',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);

            if (!$komenForum) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Forum tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Ditampilkan',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);

            if (!$komenForum) {
                Log::error('Komentar Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Forum Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'forum_id' => 'required|string',
                'isi_komenForum' => 'required|string',
                'waktu_komenForum' => 'required|date',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $komenForum->update($request->all());

            Log::info('Komentar Forum Berhasil Diupdate');
            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Diupdate',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);

            if (!$komenForum) {
                Log::error('Komentar Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Forum Tidak Ditemukan',
                ], 404);
            }

            if ($komenForum->delete()) {
                Log::info('Komentar Forum Berhasil Dihapus');
                return response()->json([
                    'data' => $komenForum,
                    'status' => 'success',
                    'message' => 'Komentar Forum Berhasil Dihapus',
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
