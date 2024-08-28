<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\KategoriSeni;
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
                    'message' => 'Seniman not found for the user',
                ], 404);
            }

            $senimanId = $user->seniman->id;

            $portfolios = Portofolio::where('seniman_id', $senimanId)
                ->select('id', 'judul_portofolio','jumlah_karya', 'created_at')
                ->paginate(10);

            if ($portfolios->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $portfolios->items(),
                    'current_page' => $portfolios->currentPage(),
                    'last_page' => $portfolios->lastPage(),
                    'per_page' => $portfolios->perPage(),
                    'total' => $portfolios->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Portofolio tidak ditemukan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage(),
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
                'judul_portofolio' => 'required|string|max:100|unique:portofolios,judul_portofolio',
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

        // Validasi input
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

        // Ambil kategori berdasarkan nama kategori dari request
        $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();

        // Set seniman_id dari request atau gunakan yang lama jika tidak ada
        $senimanId = $request->seniman_id ?? $portofolio->seniman_id;

        // Hitung jumlah karya berdasarkan seniman_id
        $jumlahKarya = DB::table('karyas')->where('portofolio_id', $id)->count();

        // Update data portofolio
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

    public function filterByRegistrasi(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'registrasi_individu' => 'nullable|exists:registrasi_individu,id',
                'registrasi_kelompok' => 'nullable|exists:registrasi_kelompok,id',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $query = Portofolio::query();

            if ($request->registrasi_individu) {
                $query->where('registrasi_individu_id', $request->registrasi_individu);
            }

            if ($request->registrasi_kelompok) {
                $query->where('registrasi_kelompok_id', $request->registrasi_kelompok);
            }

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
