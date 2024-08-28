<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function index()
    {
        try {
            $forum = Forum::whereNull('deleted_at')->get();

            if (count($forum) > 0) {
                Log::info('Data Forum Berhasil Ditampilkan');
                return response()->json([
                    'data' => $forum,
                    'status' => 'success',
                    'message' => 'Data Forum Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Forum Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Forum Kosong',
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

    public function indexForum()
    {
        try {
            $userId = Auth::id();


            if (!$userId) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Pengguna tidak terautentikasi',
                ], 401);
            }

            $forum = Forum::where('seniman_id', $userId)
                          ->whereNull('deleted_at')
                          ->get();

            if (count($forum) > 0) {
                Log::info('Data Forum Pengguna Berhasil Ditampilkan');
                return response()->json([
                    'data' => $forum,
                    'status' => 'success',
                    'message' => 'Data Forum Pengguna Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Forum Pengguna Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Forum Pengguna Kosong',
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

    public function store(Request $request){
        try {

            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'seniman_id' => 'required|exists:seniman,id',
                'kategori_id' => 'required|exists:kategori_senis,id',
                'judul_forum' => 'required|string|max:100',
                'status_forum' => 'required|boolean',
            ]);


            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $forum = Forum::create($storeData);

            Log::info('Data Forum Berhasil Ditambahakan');
            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Ditambahakan',
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


    public function show($id){
        try {
            $forum = Forum::whereNull('deleted_at')->find($id);

            if (!$forum) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Ditampilkan',
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
            $forum = Forum::whereNull('deleted_at')->find($id);

            if (!$forum) {
                Log::error('Data Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'seniman_id' => 'required|exists:seniman,id',
                'kategori_id' => 'required|exists:kategori_senis,id',
                'judul_forum' => 'required|string|max:100',
                'status_forum' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $forum->seniman_id = $request->seniman_id;
            $forum->kategori_id = $request->kategori_id;
            $forum->judul_forum = $request->judul_forum;
            $forum->status_forum = $request->status_forum;

            $forum->save();

            Log::info('Data Forum Berhasil Diupdate');
            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Diupdate',
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
            $forum = Forum::whereNull('deleted_at')->find($id);

            if (!$forum) {
                Log::error('Data Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum Tidak Ditemukan',
                ], 404);
            }

            if ($forum->delete()) {
                Log::info('Data Forum Berhasil Dihapus');
                return response()->json([
                    'data' => $forum,
                    'status' => 'success',
                    'message' => 'Data Forum Berhasil Dihapus',
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
