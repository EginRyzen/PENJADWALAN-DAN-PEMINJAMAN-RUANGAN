<?php

namespace App\Http\Controllers;

use App\Exports\BuildingTemplateExport;
use App\Exports\GenericTemplateExport;
use App\Imports\MultiSheetBuildingImport;
use App\Imports\ProgramStudiImport;
use App\Imports\KelasImport;
use App\Imports\MataKuliahImport;
use App\Imports\MahasiswaImport;
use App\Imports\DosenImport;
use App\Imports\HariLiburImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExcelImportController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new BuildingTemplateExport, 'template_import_gedung.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            DB::beginTransaction();
            
            Excel::import(new MultiSheetBuildingImport, $request->file('file'));
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimport'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }

    // Master Data Program Studi
    public function downloadTemplateProgramStudi()
    {
        return Excel::download(new GenericTemplateExport('Program Studi', [
            ['kode', 'nama', 'fakultas', 'jenjang', 'status'],
            ['INF', 'Informatika', 'Kampus 5 PSDKU', 'S1', 'aktif'],
        ]), 'template_program_studi.xlsx');
    }

    public function importProgramStudi(Request $request)
    {
        return $this->processImport(new ProgramStudiImport(), $request);
    }

    // Master Data Kelas
    public function downloadTemplateKelas()
    {
        return Excel::download(new GenericTemplateExport('Kelas', [
            ['nama_kelas', 'kode_prodi', 'nama_periode'],
            ['A', 'INF', 'Ganjil 2025/2026'],
        ]), 'template_kelas.xlsx');
    }

    public function importKelas(Request $request)
    {
        return $this->processImport(new KelasImport(), $request);
    }

    // Master Data Mata Kuliah
    public function downloadTemplateMataKuliah()
    {
        return Excel::download(new GenericTemplateExport('Mata Kuliah', [
            ['kode', 'nama', 'sks', 'semester', 'sks_ujian', 'kode_prodi'],
            ['INF101', 'Pemrograman Dasar', 3, 1, 3, 'INF'],
        ]), 'template_mata_kuliah.xlsx');
    }

    public function importMataKuliah(Request $request)
    {
        return $this->processImport(new MataKuliahImport(), $request);
    }

    // Master Data Mahasiswa
    public function downloadTemplateMahasiswa()
    {
        return Excel::download(new GenericTemplateExport('Mahasiswa', [
            ['nim', 'nama', 'kode_prodi', 'nama_kelas', 'semester', 'nama_periode', 'status'],
            ['2025001', 'Budi Santoso', 'INF', 'A', 1, 'Ganjil 2025/2026', 'Aktif'],
        ]), 'template_mahasiswa.xlsx');
    }

    public function importMahasiswa(Request $request)
    {
        return $this->processImport(new MahasiswaImport(), $request);
    }

    // Master Data Dosen
    public function downloadTemplateDosen()
    {
        return Excel::download(new GenericTemplateExport('Dosen', [
            ['nidn', 'nip', 'nama', 'kode_prodi', 'jabatan', 'status'],
            ['0001018501', '198501012010121001', 'Dr. Ahmad', 'INF', 'Lektor', 'Aktif'],
        ]), 'template_dosen.xlsx');
    }

    public function importDosen(Request $request)
    {
        return $this->processImport(new DosenImport(), $request);
    }

    // Master Data Hari Libur
    public function downloadTemplateHariLibur()
    {
        return Excel::download(new GenericTemplateExport('Hari Libur', [
            ['tanggal', 'keterangan', 'tipe', 'nama_periode'],
            ['2025-08-17', 'Hari Kemerdekaan', 'nasional', 'Ganjil 2025/2026'],
        ]), 'template_hari_libur.xlsx');
    }

    public function importHariLibur(Request $request)
    {
        return $this->processImport(new HariLiburImport(), $request);
    }

    private function processImport($importClass, Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            DB::beginTransaction();
            Excel::import($importClass, $request->file('file'));
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimport'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }
}
