<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\TransaksiDepositKelas;
use App\Models\DepositKelasMember;
use App\Models\Pegawai;
use App\Models\Member;
use App\Models\Kelas;
use App\Models\Promo;

class TransaksiDepositKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksiDepositKelas = TransaksiDepositKelas::with(['PegawaiForeignKey','MemberForeignKey', 'KelasForeignKey', 'PromoForeignKey'])->get();

        if(count($transaksiDepositKelas) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiDepositKelas
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);
    }

    public function indexKadaluarsaDepositKelas()
    {
        $today = date('Y-m-d'); // Mendapatkan tanggal hari ini
        $transaksiDepositKelas = TransaksiDepositKelas::with(['PegawaiForeignKey', 'MemberForeignKey', 'KelasForeignKey', 'PromoForeignKey'])
            ->where('tanggal_kadaluarsa_deposit_kelas', $today)
            ->get();

        if ($transaksiDepositKelas->count() > 0) {
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiDepositKelas
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
        $storeDataForTransaksiDepositKelas = $request->all();

        $validate = Validator::make($storeDataForTransaksiDepositKelas, [    
            'id_pegawai' => 'required',
            'id_member' => 'required',
            // 'id_promo' => 'required',
            'id_kelas' => 'required',
            // 'jumlah_pembayaran_deposit_kelas' => 'required|numeric',
            'jumlah_deposit_kelas' => 'required|numeric',
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
        
        $kelas = Kelas::where('id_kelas', $request->id_kelas)->first();        
        if (!$kelas) {
            return response([
                'message' => 'No data Kelas',
            ], 400);
        }
        
        // $promo = Promo::where('id_promo', $request->id_promo)->first();
        // if (!$promo) {
        //     return response([
        //         'message' => 'No data Promo',
        //     ], 400);
        // }

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }        
        
        
        $id = IdGenerator::generate(['table' => 'transaksi_deposit_kelas', 'length' => 9, 'prefix' => date('y.m.'), 'field'=>'id_deposit_kelas']);        
        $storeDataForTransaksiDepositKelas['id_deposit_kelas'] = $id;
        
        $storeDataForTransaksiDepositKelas['tanggal_deposit_kelas'] = date('Y-m-d H:i:s', strtotime('+0 days'));                   
        $storeDataForTransaksiDepositKelas['total_deposit_kelas'] = $storeDataForTransaksiDepositKelas['jumlah_deposit_kelas'];
        $storeDataForTransaksiDepositKelas['id_promo'] = 1;
        
        if (!empty($storeDataForTransaksiDepositKelas['id_promo'])) {
            if ($storeDataForTransaksiDepositKelas['total_deposit_kelas'] == 10) {
                $storeDataForTransaksiDepositKelas['bonus_deposit_kelas'] = 3;
                $storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas'] = 10 * $kelas['harga_kelas'];
                $storeDataForTransaksiDepositKelas['tanggal_kadaluarsa_deposit_kelas'] = date('Y-m-d', strtotime('+2 month -1 days'));                           
            } else if ($storeDataForTransaksiDepositKelas['total_deposit_kelas'] == 5) {
                $storeDataForTransaksiDepositKelas['bonus_deposit_kelas'] = 1;
                $storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas'] = 5 * $kelas['harga_kelas'];
                $storeDataForTransaksiDepositKelas['tanggal_kadaluarsa_deposit_kelas'] = date('Y-m-d', strtotime('+1 month -1 days'));                           
            } else {
                $storeDataForTransaksiDepositKelas['bonus_deposit_kelas'] = 0;
            }
        }
        
        $storeDataForTransaksiDepositKelas['total_deposit_kelas'] = $storeDataForTransaksiDepositKelas['jumlah_deposit_kelas'] + $storeDataForTransaksiDepositKelas['bonus_deposit_kelas'];
        $totalPembayaranDepositKelas = $storeDataForTransaksiDepositKelas['jumlah_deposit_kelas'] * $kelas['harga_kelas'];

        if ($storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas'] < $totalPembayaranDepositKelas) {
            return response([
                'message' => 'Jumlah pembayaran deposit kelas kurang Rp. ' . ($totalPembayaranDepositKelas - $storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas']),
            ], 400);
        }

        $kembalianDepositKelas = $storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas'] - $totalPembayaranDepositKelas;
        $storeDataForTransaksiDepositKelas['jumlah_pembayaran_deposit_kelas'] = $storeDataForTransaksiDepositKelas['jumlah_deposit_kelas'] * $kelas['harga_kelas'];                
        $transaksiDepositKelas = TransaksiDepositKelas::create($storeDataForTransaksiDepositKelas);
        
        $storeDataDepositKelasMember = [            
            'id_member' => $storeDataForTransaksiDepositKelas['id_member'],
            'id_kelas' => $storeDataForTransaksiDepositKelas['id_kelas'],
            'tanggal_kadaluarsa' => $storeDataForTransaksiDepositKelas['tanggal_kadaluarsa_deposit_kelas'],
            'sisa_deposit' => $storeDataForTransaksiDepositKelas['total_deposit_kelas']
        ];
        
        $depositKelasMember = DepositKelasMember::create($storeDataDepositKelasMember);
        return response([
            'message' => 'Add Transaksi Deposit Kelas Success',
            'data' => [
                'transaksi_deposit_kelas' => $transaksiDepositKelas,
                'deposit_kelas_member' => $storeDataDepositKelasMember,
            ],
            'kembalian' => $kembalianDepositKelas,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_deposit_kelas)
    {
        $transaksiDepositKelas = TransaksiDepositKelas::with(['PegawaiForeignKey', 'MemberForeignKey', 'KelasForeignKey', 'PromoForeignKey'])->where('id_deposit_kelas', $id_deposit_kelas)->first();

        if(!is_null($transaksiDepositKelas)){
            return response([
                'message' => 'Retrieve data success',
                'data' => $transaksiDepositKelas
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
    public function destroy($id_deposit_kelas)
    {
        $transaksiDepositKelas = TransaksiDepositKelas::find($id_deposit_kelas);

        if(is_null($transaksiDepositKelas)){
            return response([
                'message' => 'No data',
                'data' => null,
            ], 400);
        }

        if($transaksiDepositKelas->delete()){
            return response([
                'message' => 'Delete Transaksi Deposit Kelas Success',
                'data' => $transaksiDepositKelas,
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Deposit Kelas Failed',
            'data' => null,
        ], 400);
    }

    public function resetDepositKelas($id_deposit_kelas){
        $transaksiDepositKelas = TransaksiDepositKelas::find($id_deposit_kelas);
        $today = date('Y-m-d');

        if(is_null($transaksiDepositKelas)){
            return response([
                'message' => 'Transaksi Deposit Kelas Not Found',
                'data' => null
            ], 404);
        }
        
        if ($transaksiDepositKelas->tanggal_kadaluarsa_deposit_kelas != $today) {
            return response([
                'message' => 'Deposit Kelas Belum Bisa di Reset',
                'data' => null
            ], 404);
        }
        $transaksiDepositKelas->jumlah_deposit_kelas = 0;
        $transaksiDepositKelas->bonus_deposit_kelas = 0;
        $transaksiDepositKelas->total_deposit_kelas = 0;
        $transaksiDepositKelas->tanggal_kadaluarsa_deposit_kelas = null;
        $transaksiDepositKelas->save();
        
        return response([
            'message' => 'Reset Deposit Kelas Success',
            'data' => $transaksiDepositKelas
        ], 200);        


        return response([
            'message' => 'Reset Deposit Kelas Failed',
            'data' => null
        ], 400);
    }
}