<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiIndividu;
use App\Models\Seniman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterIndividuController extends Controller
{
    public function index(){
        try {
            $register = RegistrasiIndividu::whereNull('deleted_at')->get();

            if (count($register) > 0) {
                Log::info('Data Registrasi Individu Berhasil Ditampilkan');
                return response()->json([
                    'data' => $register,
                    'status' => 'success',
                    'message' => 'Data Registrasi Individu Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Kosong',
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
                'nama_seniman'=>'required',
                'nama' => 'required',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|numeric',
                'email' => 'required|email',
                'status_individu' => 'required',
            ]);


            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
            $seniman = Seniman::where('nama_seniman', $storeData['nama_seniman'])->first();

            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['tgl_mulai'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_mulai'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;
            unset($storeData['nama_seniman']);

            $register = RegistrasiIndividu::create($storeData);

            Log::info('Data Registrasi Individu Berhasil Ditambahakan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Ditambahakan',
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
            $register = RegistrasiIndividu::whereNull('deleted_at')->find($id);

            if (!$register) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Ditampilkan',
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
            $register = RegistrasiIndividu::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Individu Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'seniman_id'=>'required',
                'nama' => 'required',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|numeric|max:20',
                'email' => 'required|email',
                'status_individu' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $tgl_lahir = Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d');
            $tgl_mulai = Carbon::createFromFormat('d/m/Y', $request->tgl_mulai)->format('Y-m-d');

            $register->seniman_id = $request->seniman_id;
            $register->nama = $request->nama;
            $register->tgl_lahir = $tgl_lahir;
            $register->tgl_mulai = $tgl_mulai;
            $register->alamat = $request->alamat;
            $register->noTelp = $request->noTelp;
            $register->email = $$request->email;;
            $register->status_individu = $request->status_individu;

            $register->save();

            Log::info('Data Registrasi Individu Berhasil Diupdate');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Diupdate',
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
            $register = RegistrasiIndividu::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Individu Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu Tidak Ditemukan',
                ], 404);
            }

            if ($register->delete()) {
                Log::info('Data Registrasi Individu Berhasil Dihapus');
                return response()->json([
                    'data' => $register,
                    'status' => 'success',
                    'message' => 'Data Registrasi Individu Berhasil Dihapus',
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
