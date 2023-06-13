<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\JadwalUmum;
use App\Models\JadwalHarian;
use App\Models\Kelas;
use App\Models\Instruktur;

class JadwalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwalUmum = JadwalUmum::with(['KelasForeignKey', 'InstrukturForeignKey'])->get();

        if(count($jadwalUmum) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $jadwalUmum
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'jam' => 'required|string',
            // 'slot_kelas' => 'required',
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $checkKelas = Kelas::where('id_kelas', $request->id_kelas)->first();
        if (!$checkKelas) {
            return response([
                'message' => 'No data Kelas',
            ], 400);
        }

        $checkInstruktur = Instruktur::where('id_instruktur', $request->id_instruktur)->first();
        if (!$checkInstruktur) {
            return response([
                'message' => 'No data Instruktur',
            ], 400);
        }

        $checkData = DB::SELECT("SELECT * from jadwal_umum where id_instruktur = '$request->id_instruktur' and tanggal = '$request->tanggal' and jam = '$request->jam'");
        if($checkData){
            return response([
                'message' => 'This schedule has been created',
            ], 400); //Return message data Jadwal baru dalam bentuk JSON
        }

        $checkTime = DB::SELECT("SELECT * from jadwal_umum where tanggal = '$request->tgl_jadwal' and jam = '$request->jam'");
        if($checkTime){
            return response([
                'message' => 'This schedule already has an instructor',
            ], 400); //Return message data Jadwal baru dalam bentuk JSON
        }

        $storeData['slot_kelas'] = 10;
        
        $jadwalUmum = JadwalUmum::create($storeData);
        return response([
            'message' => 'Add Jadwal Umum Success',
            'data' => $jadwalUmum,
        ], 200);
    }

    // public function showJadwalUmumInstruktur(){
    //     $data = DB::SELECT("SELECT * from jadwal_umum join instruktur using(id_instruktur)");

    //     if(count($data) > 0){
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $data
    //         ], 200);
    //     } //Return data semua data dalam bentuk JSON

    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ], 400); //Return message data jadwal kosong
    // }

    // public function showJadwalUmumKelas(){
    //     $data = DB::SELECT("SELECT * from jadwal_umum join kelas using(id_kelas)");

    //     if(count($data) > 0){
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $data
    //         ], 200);
    //     } //Return data semua data dalam bentuk JSON

    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ], 400); //Return message data kelas kosong
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_jadwal_umum)
    {
        $jadwalUmum = JadwalUmum::with(['KelasForeignKey', 'InstrukturForeignKey'])->where('id_jadwal_umum', $id_jadwal_umum)->first();

        if(!is_null($jadwalUmum)){
            return response([
                'message' => 'Retrieve data success',
                'data' => $jadwalUmum
            ], 200);
        }

        return response([
            'message' => 'No data',
            'data' => null,
        ], 400);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_jadwal_umum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_jadwal_umum)
    {
        $jadwalUmum = JadwalUmum::find($id_jadwal_umum);

        if(is_null($jadwalUmum)){
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'jam' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $checkKelas = Kelas::where('id_kelas', $request->id_kelas)->first();
        if (!$checkKelas) {
            return response([
                'message' => 'No data Kelas',
            ], 400);
        }

        $checkInstruktur = Instruktur::where('id_instruktur', $request->id_instruktur)->first();
        if (!$checkInstruktur) {
            return response([
                'message' => 'No data Instruktur',
            ], 400);
        }

        $checkData = DB::SELECT("SELECT * from jadwal_umum where id_instruktur = '$request->id_instruktur' and tanggal = '$request->tanggal' and jam = '$request->jam'");
        if($checkData){
            return response([
                'message' => 'This schedule has been created',
            ], 400); //Return message data Jadwal baru dalam bentuk JSON
        }

        $checkTime = DB::SELECT("SELECT * from jadwal_umum where tanggal = '$request->tgl_jadwal' and jam = '$request->jam'");
        if($checkTime){
            return response([
                'message' => 'This schedule already has an instructor',
            ], 400); //Return message data Jadwal baru dalam bentuk JSON
        }

        $jadwalUmum->id_instruktur = $updateData['id_instruktur'];
        $jadwalUmum->id_kelas = $updateData['id_kelas'];
        $jadwalUmum->tanggal = $updateData['tanggal'];
        $jadwalUmum->hari = $updateData['hari'];
        $jadwalUmum->jam = $updateData['jam'];
        
        if($jadwalUmum->save()){
            return response([
                'message' => 'Update jadwal success',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Update jadwal failed',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_jadwal_umum)
    {
        $jadwalUmum = JadwalUmum::find($id_jadwal_umum);

        if(is_null($jadwalUmum)){
            return response([
                'message' => 'No data',
                'data' => null,
            ], 400);
        }

        if($jadwalUmum->delete()){
            return response([
                'message' => 'Delete Jadwal Success',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Delete Jadwal Failed',
            'data' => null,
        ], 400);
    }
}
