<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\TransaksiAktivasi;
use App\Models\Pegawai;
use App\Models\Member;

class TransaksiAktivasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksiAktivasi = TransaksiAktivasi::with(['PegawaiForeignKey', 'MemberForeignKey'])->get();

        if(count($transaksiAktivasi) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiAktivasi
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
            'id_pegawai' => 'required',
            'id_member' => 'required',                        
            // 'jumlah_pembayaran_aktivasi' => 'required|numeric|min:3000000',            
            'jenis_pembayaran_aktivasi' => 'required',            
        ]);
        
        $checkPegawai = Pegawai::where('id_pegawai', $request->id_pegawai)->first();
        if (!$checkPegawai) {
            return response([
                'message' => 'No data Pegawai',
            ], 400);
        }

        $member = Member::where('id_member', $request->id_member)->first();        
        if ($member->status_member == 'Aktif') {
            return response([
                'message' => 'Members are active',
            ], 400);
        }


        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
        $id = IdGenerator::generate(['table' => 'transaksi_aktivasi', 'length' => 9, 'prefix' => date('y.m.'), 'field'=>'id_aktivasi']);        
        $storeData['id_aktivasi'] = $id;

        $storeData['tanggal_aktivasi'] = date('Y-m-d H:i:s', strtotime('+0 days'));
        $storeData['tanggal_kadaluarsa'] = date('Y-m-d', strtotime('+1 years -1 days'));
        $storeData['jumlah_pembayaran_aktivasi'] = 3000000;
        $member->status_member = 'Aktif';
        $member->masa_berlaku_member = $storeData['tanggal_kadaluarsa'];
        $member->save();

        $transaksiAktivasi = TransaksiAktivasi::create($storeData);

        return response([
            'message' => 'Add Transaksi Aktivasi Success',
            'data' => $transaksiAktivasi,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_aktivasi)
    {
        $transaksiAktivasi = TransaksiAktivasi::with(['PegawaiForeignKey', 'MemberForeignKey'])->where('id_aktivasi', $id_aktivasi)->first();

        if(!is_null($transaksiAktivasi)){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiAktivasi
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
    public function edit($id)
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
    public function update(Request $request, $id_aktivasi)
    {
        // gada update su awokowkowkowk

        // $transaksiAktivasi = TransaksiAktivasi::find($id_aktivasi);

        // if(is_null($transaksiAktivasi)){
        //     return response([
        //         'message' => 'Data Tidak ada',
        //         'data' => null,
        //     ], 400);
        // }

        // $updateData = $request->all();
        // $validate = Validator::make($updateData, [            
        //     'id_pegawai' => 'required',
        //     'id_member' => 'required',
        //     'tanggal_aktivasi' => 'required|date',
        //     'tanggal_kadaluarsa' => 'required|date',
        //     'jumlah_pembayaran_aktivasi' => 'required',            
        //     'jenis_pembayaran_aktivasi' => 'required',            
        // ]);

        // if ($validate->fails()) {
        //     return response(['message' => $validate->errors()], 400);
        // }

        // $checkPegawai = Pegawai::where('id_pegawai', $request->id_pegawai)->first();
        // if (!$checkPegawai) {
        //     return response([
        //         'message' => 'No data Pegawai',
        //     ], 400);
        // }

        // $checkMember = Member::where('id_member', $request->id_member)->first();
        // if (!$checkMember) {
        //     return response([
        //         'message' => 'No data Member',
        //     ], 400);
        // }

        // $transaksiAktivasi->id_pegawai = $updateData['id_pegawai'];
        // $transaksiAktivasi->id_member = $updateData['id_member'];
        // $transaksiAktivasi->tanggal_aktivasi = $updateData['tanggal_aktivasi'];
        // $transaksiAktivasi->tanggal_kadaluarsa = $updateData['tanggal_kadaluarsa'];
        // $transaksiAktivasi->jumlah_pembayaran_aktivasi = $updateData['jumlah_pembayaran_aktivasi'];
        // $transaksiAktivasi->jenis_pembayaran_aktivasi = $updateData['jenis_pembayaran_aktivasi'];
        // // $jadwalUmum->id_instruktur = $updateData['id_instruktur'];
        
        // if($jadwalUmum->save()){
        //     return response([
        //         'message' => 'Update jadwal success',
        //         'data' => $jadwalUmum,
        //     ], 200);
        // }

        // return response([
        //     'message' => 'Update jadwal failed',
        //     'data' => null,
        // ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_aktivasi)
    {
        $transaksiAktivasi = TransaksiAktivasi::find($id_aktivasi);

        if(is_null($transaksiAktivasi)){
            return response([
                'message' => 'No data',
                'data' => null,
            ], 400);
        }

        if($transaksiAktivasi->delete()){
            return response([
                'message' => 'Delete Transaksi Aktivasi Success',
                'data' => $transaksiAktivasi,
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Aktivasi Failed',
            'data' => null,
        ], 400);
    }
}
