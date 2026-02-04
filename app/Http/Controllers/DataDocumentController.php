<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\DataDocument;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DataDocumentController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');

            $document = DataDocument::create([
                'file_path' => $path,
                'file_name' => $fileName,
                'file_type' => $file->getClientOriginalExtension(),
                'checksum'  => hash_file('sha256', $file->getRealPath()),
            ]);

            return $this->successResponse($document, 'Gambar berhasil diunggah', 201, 'Created');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataDocument $dataDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataDocument $dataDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataDocument $dataDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataDocument $dataDocument)
    {
        //
    }
}
