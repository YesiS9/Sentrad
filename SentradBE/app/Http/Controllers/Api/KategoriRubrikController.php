<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\KategoriRubrik;
use App\Models\Kategori;
use App\Models\KategoriSeni;
use App\Models\Rubrik;
use Illuminate\Support\Facades\Log;

class KategoriRubrikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // You can implement the index method if you need to list all kategori-rubrik entries
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($request->all(), [
                'kategori_id' => 'required|exists:kategori_senis,nama_kategori',
                'rubrik_id' => 'required|exists:rubriks,id',
            ]);

            if ($validate->fails()) {
                Log::error('validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->where('nama_kategori', $storeData['nama_kategori'])->first();

            if(!$kategoriSeni){
                Log::error('The selected Kategori Seni name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected Kategori Seni name is invalid.',
                ], 400);
            }

            $storeData['kategori_id'] = $kategoriSeni->id;

            $kategoriRubrik = KategoriRubrik::create($storeData);
            
            Log::info('Data Kategori Rubrik Berhasil Ditambahkan');
            return response()->json([
                'data' => $kategoriRubrik,
                'status' => 'success',
                'message' => 'Data Kategori Rubrik Berhasil Ditambahkan',
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // You can implement the show method if you need to display a specific kategori-rubrik entry
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // You can implement the update method if you need to update a specific kategori-rubrik entry
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // You can implement the destroy method if you need to delete a specific kategori-rubrik entry
    }
}
