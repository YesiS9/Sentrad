<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;

class PenilaiController extends Controller
{
    public function index(){
        try {
            $penilai = Penilai::whereNull('deleted_at')->get();

            if (count($penilai) > 0) {
                Log::info('Data Penilai Berhasil Ditampilkan');
                return response()->json([
                    'data' => $penilai,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Penilai Kosong',
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
                'username' => 'required|exists:users,username',
                'nama_penilai' => 'required|string|max:100',
                'alamat_penilai' => 'required|string',
                'noTelp_penilai' => 'required|numeric',
                'bidang_ahli' => 'required|string',
                'lembaga' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'status_penilai' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $user = User::where('username', $storeData['username'])->first();
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }
	        $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['user_id'] = $user->id;
            unset($storeData['username']);

            $penilai = Penilai::create($storeData);

            Log::info('Data Penilai Berhasil Ditambahkan');
            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Ditambahkan',
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
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Ditampilkan',
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


    public function update(Request $request, $id){
        try {
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                Log::error('Data Penilai Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'nama_penilai' => 'required|string|max:100',
                'alamat_penilai' => 'required|string',
                'noTelp_penilai' => 'required|numeric',
                'bidang_ahli' => 'required|string',
                'lembaga' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'status_penilai' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $user = User::where('username', $request->username)->first();
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            $tgl_lahir = Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d');

            $penilai->user_id = $user->id;
            $penilai->nama_penilai = $request->nama_penilai;
            $penilai->alamat_penilai = $request->alamat_penilai;
            $penilai->noTelp_penilai = $request->noTelp_penilai;
            $penilai->bidang_ahli = $request->bidang_ahli;
            $penilai->lembaga = $request->lembaga;
            $penilai->tgl_lahir = $tgl_lahir;
            $penilai->status_penilai = $request->status_penilai;

            $penilai->save();

            Log::info('Data Penilai Berhasil Diupdate');
            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Diupdate',
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



    public function destroy($id){
        try {
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                Log::error('Data Penilai Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai Tidak Ditemukan',
                ], 404);
            }

            if ($penilai->delete()) {
                Log::info('Data Penilai Berhasil Dihapus');
                return response()->json([
                    'data' => $penilai,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Dihapus',
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

    public function showLaporan()
    {
        try {
            $penilai = Penilai::whereNull('deleted_at')->get();

            if (count($penilai) > 0) {
                Log::info('Data Penilai Berhasil Ditampilkan');
                return response()->json([
                    'data' => $penilai,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Penilai Kosong',
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

    public function downloadPenilaiLaporan()
    {
        try {
            $penilais = Penilai::whereNull('deleted_at')
                ->withCount([
                    'rubrik as jumlah_rubrik',
                    'penilaian_karya as jumlah_penilaian_karya'
                ])
                ->get();

            $data = [
                'penilais' => $penilais
            ];

            $pdf = PDF::loadView('pdf.penilai_laporan', $data);

            return $pdf->download('penilai_laporan.pdf');
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
