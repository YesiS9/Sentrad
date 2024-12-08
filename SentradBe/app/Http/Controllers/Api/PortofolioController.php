<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\KategoriSeni;
use App\Models\Seniman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PortofolioController extends Controller
{
    public function index()
{
    try {
        $user = Auth::user();

        if (!$user->seniman) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'Seniman tidak ditemukan untuk pengguna ini',
            ], 404);
        }

        $senimanId = $user->seniman->id;

        // Query portofolio individu
        $individualPortfolios = Portofolio::where('seniman_id', $senimanId)
            ->whereNull('kelompok_id')
            ->select('id', 'judul_portofolio', 'jumlah_karya', 'created_at')
            ->paginate(10, ['*'], 'individual_page'); // Custom pagination name

        // Query portofolio kelompok
        $groupPortfolios = Portofolio::where('seniman_id', $senimanId)
            ->whereNotNull('kelompok_id')
            ->select('id', 'judul_portofolio', 'jumlah_karya', 'created_at')
            ->paginate(10, ['*'], 'group_page'); // Custom pagination name

        // Response JSON dengan kedua data
        return response()->json([
            'status' => 'success',
            'data' => [
                'individual_portfolios' => [
                    'items' => $individualPortfolios->items(),
                    'current_page' => $individualPortfolios->currentPage(),
                    'last_page' => $individualPortfolios->lastPage(),
                    'per_page' => $individualPortfolios->perPage(),
                    'total' => $individualPortfolios->total(),
                ],
                'group_portfolios' => [
                    'items' => $groupPortfolios->items(),
                    'current_page' => $groupPortfolios->currentPage(),
                    'last_page' => $groupPortfolios->lastPage(),
                    'per_page' => $groupPortfolios->perPage(),
                    'total' => $groupPortfolios->total(),
                ],
            ],
        ]);
    } catch (\Exception $e) {
        Log::error('Exception Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'data' => null,
            'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
        ], 500);
    }
}




    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kelompok_id' => 'nullable|exists:registrasi_kelompoks,id',
                'seniman_id' => 'required|exists:seniman,id',
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'judul_portofolio' => 'required|string|max:100',
                'deskripsi_portofolio' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }


            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();
            $senimanId = $request->seniman_id;
            $jumlahKarya = 0;

            $portofolio = Portofolio::create([
                'kelompok_id' => $request->kelompok_id,
                'seniman_id' => $senimanId,
                'kategori_id' => $kategori->id,
                'judul_portofolio' => $request->judul_portofolio,
                'tgl_dibuat' => now(),  // Set tgl_dibuat to the current timestamp
                'deskripsi_portofolio' => $request->deskripsi_portofolio,
                'jumlah_karya' => $jumlahKarya,
            ]);

            Log::info('Data Portofolio Berhasil Disimpan');
            return response()->json([
                'data' => $portofolio,
                'status' => 'success',
                'message' => 'Data Portofolio Berhasil Disimpan',
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
                'deskripsi_portofolio' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();

            $senimanId = $request->seniman_id ?? $portofolio->seniman_id;

            $jumlahKarya = DB::table('karyas')->where('portofolio_id', $id)->count();

            $portofolio->update([
                'kelompok_id' => $request->kelompok_id,
                'seniman_id' => $senimanId,
                'kategori_id' => $kategori ? $kategori->id : $portofolio->kategori_id,
                'judul_portofolio' => $request->judul_portofolio,
                'tgl_dibuat' => $portofolio->tgl_dibuat,  // Keep the original creation date
                'deskripsi_portofolio' => $request->deskripsi_portofolio,
                'jumlah_karya' => $jumlahKarya,
            ]);

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


    public function showData($id){
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



    public function show($id)
    {
        try {
            $portofolio = Portofolio::with(['seniman', 'kelompok'])
                ->whereNull('deleted_at')
                ->find($id);

            if (!$portofolio) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Portofolio tidak ditemukan',
                ], 404);
            }

            $data = [
                'portofolio' => [
                    'id' => $portofolio->id,
                    'judul_portofolio' => $portofolio->judul_portofolio,
                    'tgl_dibuat' => $portofolio->tgl_dibuat,
                    'deskripsi_portofolio' => $portofolio->deskripsi_portofolio,
                    'jumlah_karya' => $portofolio->jumlah_karya,
                ],
                'nama_seniman' => $portofolio->seniman ? $portofolio->seniman->nama_seniman : null,
                'nama_kelompok' => $portofolio->kelompok ? $portofolio->kelompok->nama_kelompok : null,
            ];

            return response()->json([
                'data' => $data,
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

    public function filterByIndividu(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'individu_id' => 'required|exists:registrasi_individus,id',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $senimanIds = Seniman::whereHas('registrasiIndividu', function ($query) use ($request) {
                $query->where('id', $request->individu_id);
            })->pluck('id');

            $query = Portofolio::whereIn('seniman_id', $senimanIds)
                                ->with(['seniman:id,nama_seniman']);

            $portfolios = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $portfolios->items(),
                'current_page' => $portfolios->currentPage(),
                'last_page' => $portfolios->lastPage(),
                'per_page' => $portfolios->perPage(),
                'total' => $portfolios->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function filterByKelompok(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'kelompok_id' => 'required|exists:registrasi_kelompoks,id',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $query = Portofolio::where('kelompok_id', $request->kelompok_id)
                                ->with(['kelompok:id,nama_kelompok']);

            $portfolios = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $portfolios->items(),
                'current_page' => $portfolios->currentPage(),
                'last_page' => $portfolios->lastPage(),
                'per_page' => $portfolios->perPage(),
                'total' => $portfolios->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }






}
