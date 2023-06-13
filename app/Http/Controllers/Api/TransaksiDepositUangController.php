<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\TransaksiDepositUang;
use App\Models\Pegawai;
use App\Models\Member;
use App\Models\Promo;

class TransaksiDepositUangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksiDepositUang = TransaksiDepositUang::with(['PegawaiForeignKey', 'MemberForeignKey', 'PromoForeignKey'])->get();

        if(count($transaksiDepositUang) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiDepositUang
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
            // 'id_promo' => 'required',
            'id_member' => 'required',                        
            'jumlah_pembayaran_deposit_uang' => 'numeric',                                                                 
        ]);
        
        
        
        $member = Member::where('id_member', $request->id_member)->first();        
        if (!$member) {
            return response([
                'message' => 'No data Member',
            ], 400);
        }

        if ($member->status_member == 'Tidak Aktif') {
            return response([
                'message' => 'Member is not active',
            ], 400);
        }

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
        $id = IdGenerator::generate(['table' => 'transaksi_deposit_uang', 'length' => 9, 'prefix' => date('y.m.'), 'field'=>'id_deposit_uang']);        
        $storeData['id_deposit_uang'] = $id;
        $storeData['id_promo'] = 2;
        $storeData['total_deposit_uang'] = 0;                
        $storeData['tanggal_deposit_uang'] = date('Y-m-d H:i:s', strtotime('+0 days'));

        $storeData['sisa_deposit_uang_member_sebelum'] = $member->sisa_deposit_uang_member;        
        
        if ($storeData['jumlah_pembayaran_deposit_uang'] >= 3000000) {
            $storeData['bonus_deposit_uang'] = 300000;
        }else {
            $storeData['bonus_deposit_uang'] = 0;
        }

        $storeData['total_deposit_uang'] = $storeData['total_deposit_uang'] + $member->sisa_deposit_uang_member + $storeData['jumlah_pembayaran_deposit_uang'] + $storeData['bonus_deposit_uang'];        
        
        $member->sisa_deposit_uang_member = $storeData['total_deposit_uang'];
        $member->save();

        $transaksiDepositUang = TransaksiDepositUang::create($storeData);

        return response([
            'message' => 'Add Transaksi Deposit Uang Success',
            'data' => $transaksiDepositUang,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_deposit_uang)
    {
        $transaksiDepositUang = TransaksiDepositUang::with(['PegawaiForeignKey', 'MemberForeignKey', 'PromoForeignKey'])->where('id_deposit_uang', $id_deposit_uang)->first();

        if(!is_null($transaksiDepositUang)){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiDepositUang
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_deposit_uang)
    {
        $transaksiDepositUang = TransaksiDepositUang::find($id_deposit_uang);

        if(is_null($transaksiDepositUang)){
            return response([
                'message' => 'No data',
                'data' => null,
            ], 400);
        }

        if($transaksiDepositUang->delete()){
            return response([
                'message' => 'Delete Transaksi Deposit Uang Success',
                'data' => $transaksiDepositUang,
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Deposit Uang Failed',
            'data' => null,
        ], 400);
    }
}