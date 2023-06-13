<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\Kelas;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use App\Models\PresensiBookingGym;
use App\Models\PresensiBookingKelas;
use App\Models\DepositKelasMember;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPresensiBookingGym()
    {
        $presensiBookingGym = PresensiBookingGym::with(['MemberForeignKey'])->get();

        if(count($presensiBookingGym) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $presensiBookingGym
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);
    }

    public function indexPresensiBookingGymByIdMember($id_member)
    {
        $presensiBookingGym = PresensiBookingGym::with(['MemberForeignKey'])->where('id_member', $id_member)->get();

        if(count($presensiBookingGym) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $presensiBookingGym
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);
    }

    public function indexPresensiBookingKelas()
    {
        $presensiBookingKelas = PresensiBookingKelas::with(['MemberForeignKey', 'JadwalHarianForeignKey', 'JadwalHarianForeignKey.InstrukturForeignKey', 'JadwalHarianForeignKey.JadwalUmumForeignKey.KelasForeignKey'])->get();

        $memberIds = $presensiBookingKelas->pluck('id_member');
        $jadwalHarianIds = $presensiBookingKelas->pluck('id_jadwal_harian');

        $members = Member::whereIn('id_member', $memberIds)->get();
        $jadwalHarians = JadwalHarian::whereIn('id_jadwal_harian', $jadwalHarianIds)->get();

        $depositKelasMembers = [];
        foreach ($presensiBookingKelas as $presensi) {
            $member = $members->firstWhere('id_member', $presensi->id_member);
            $jadwalHarian = $jadwalHarians->firstWhere('id_jadwal_harian', $presensi->id_jadwal_harian);
            $depositKelasMember = DepositKelasMember::where([
                'id_member' => $member->id_member,
                'id_kelas' => $jadwalHarian->JadwalUmumForeignKey->KelasForeignKey->id_kelas,
            ])->first();
            $depositKelasMembers[] = $depositKelasMember;
        }

        if (count($presensiBookingKelas) > 0) {
            return response([
                'message' => 'Retrieve data success',
                'data' => [
                    'presensi_booking_kelas' => $presensiBookingKelas,
                    'deposit_kelas_member' => $depositKelasMembers,
                ]
                
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);

    }
        
    public function indexPresensiBookingKelasByIdMember($id_member) {
        $presensiBookingKelas = PresensiBookingKelas::with(['MemberForeignKey', 'JadwalHarianForeignKey', 'JadwalHarianForeignKey.InstrukturForeignKey', 'JadwalHarianForeignKey.JadwalUmumForeignKey.KelasForeignKey'])->where('id_member', $id_member)->get();

        $memberIds = $presensiBookingKelas->pluck('id_member');
        $jadwalHarianIds = $presensiBookingKelas->pluck('id_jadwal_harian');

        $members = Member::whereIn('id_member', $memberIds)->get();
        $jadwalHarians = JadwalHarian::whereIn('id_jadwal_harian', $jadwalHarianIds)->get();

        $depositKelasMembers = [];
        foreach ($presensiBookingKelas as $presensi) {
            $member = $members->firstWhere('id_member', $presensi->id_member);
            $jadwalHarian = $jadwalHarians->firstWhere('id_jadwal_harian', $presensi->id_jadwal_harian);
            $depositKelasMember = DepositKelasMember::where([
                'id_member' => $member->id_member,
                'id_kelas' => $jadwalHarian->JadwalUmumForeignKey->KelasForeignKey->id_kelas,
            ])->first();
            $depositKelasMembers[] = $depositKelasMember;
        }

        if (count($presensiBookingKelas) > 0) {
            return response([
                'message' => 'Retrieve data success',
                'data' => [
                    'presensi_booking_kelas' => $presensiBookingKelas,
                    'deposit_kelas_member' => $depositKelasMembers,
                ]
                
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
    public function storePresensiBookingGym(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [       
            'id_booking_gym' => 'string',                 
            'id_member' => 'required',            
            'tanggal_yang_dibooking' => 'required|date',
            'slot_waktu' => 'required',
        ]);

        $member = Member::where('id_member', $request->id_member)->first();        
        if ($member->status_member == 'Tidak Aktif') {
            return response([
                'message' => 'Members are not active',
            ], 400);
        }

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
	    $id = IdGenerator::generate(['table' => 'presensi_booking_gym', 'length' => 9, 'prefix' => date('y.m.'), 'field'=>'id_booking_gym']);                
        $storeData['id_booking_gym'] = $id;        
        $storeData['tanggal_booking'] = date('Y-m-d');

        $cekMemberGymIsBooking = PresensiBookingGym::where('tanggal_yang_dibooking', $request->tanggal_yang_dibooking)->where('slot_waktu', $request->slot_waktu)->where('id_member', $request->id_member)->count();

        if ($cekMemberGymIsBooking >= 1) {
            return response([
                'message' => 'Anda sudah booking gym pada slot waktu tersebut',
            ], 400);
        }

        $cekKuota = PresensiBookingGym::where('tanggal_yang_dibooking', $request->tanggal_yang_dibooking)->where('slot_waktu', $request->slot_waktu)->count();        

        if ($cekKuota >= 10) {
            return response([
                'message' => 'Kuota Gym sudah penuh',
            ], 400);
        }
        $presensiBookingGym = PresensiBookingGym::create($storeData);         

        return response([
            'message' => 'Add Presensi Booking Gym Success',
            'data' => $presensiBookingGym,
        ], 200);
    }

    public function storePresensiBookingKelas(Request $request)
    {
        $storeData = $request->all();
        $storeData['id_jadwal_harian'] = intval($storeData['id_jadwal_harian']);

        $validate = Validator::make($storeData, [       
            'id_booking_kelas' => 'string',                 
            'id_member' => 'required',            
            'id_jadwal_harian' => 'required',
            'jenis_pembayaran' => 'required',            
        ]);

        $member = Member::where('id_member', $request->id_member)->first();        
        if ($member->status_member == 'Tidak Aktif') {
            return response([
                'message' => 'Members are not active',
            ], 400);
        }
        
        $jadwalHarian = JadwalHarian::where('id_jadwal_harian', $storeData['id_jadwal_harian'])->first();
        if (is_null($jadwalHarian)) {
            return response([
                'message' => 'Jadwal Harian not found',
            ], 400);
        }
        
        if ($jadwalHarian->status == 'Tidak Aktif') {
            return response([
                'message' => 'Jadwal Harian not active',
            ], 400);
        }
        
        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $depositKelasMember = DepositKelasMember::where([
            'id_member' => $member->id_member,
            'id_kelas' => $jadwalHarian->JadwalUmumForeignKey->KelasForeignKey->id_kelas,
        ])->first();

        // kode ini sama kek diatas wkwk
        // $depositKelasMember = DepositKelasMember::where([
        //     'id_member' => $member['id_member'],
        //     'id_kelas' => $jadwalHarian['JadwalUmumForeignKey']['KelasForeignKey']['id_kelas'],
        // ])->first();

        $cekMemberKelasIsBooking = PresensiBookingKelas::where('id_jadwal_harian', $request->id_jadwal_harian)->where('id_member', $request->id_member)->count();
        if ($cekMemberKelasIsBooking >= 1) {
            return response([
                'message' => 'Anda sudah booking kelas di tanggal tersebut',
            ], 400);
        }

        // member tidak bisa booking tanggal sebelum hari ini
        $jadwalHarianTanggal = Carbon::parse($jadwalHarian->tanggal)->startOfDay();
        $currentDate = Carbon::now()->startOfDay();

        if ($jadwalHarianTanggal->lt($currentDate)) {
            return response()->json([
                'message' => 'Anda tidak bisa booking kelas pada tanggal sebelum hari ini'
            ], 400);
        }

        $cekKuota = PresensiBookingKelas::where('id_jadwal_harian', $request->id_jadwal_harian)->count();
        if ($cekKuota >= 10) {
            return response([
                'message' => 'Kuota Kelas sudah penuh',
            ], 400);
        }

        // memakai deposit kelas atau deposit uang
        if ($storeData['jenis_pembayaran']  == "Deposit Kelas") {
            if (is_null($depositKelasMember)) {
                return response([
                    'message' => 'Deposit Kelas anda tidak ada',
                ], 400);
            }
            if ($depositKelasMember->sisa_deposit <= 0) {
                return response([
                    'message' => 'Deposit Kelas anda tidak mencukupi',
                ], 400);
            }
        } else {
            if ($member->sisa_deposit_uang_member < $jadwalHarian->JadwalUmumForeignKey->KelasForeignKey->harga_kelas) {
                return response([
                    'message' => 'Deposit Uang anda tidak mencukupi',
                ], 400);
            }
            $storeData['tarif'] = $jadwalHarian->JadwalUmumForeignKey->KelasForeignKey->harga_kelas;
        }

        $id = IdGenerator::generate(['table' => 'presensi_booking_kelas', 'length' => 9, 'prefix' => date('y.m.'), 'field'=>'id_booking_kelas']);                
        $storeData['id_booking_kelas'] = $id;        
        $storeData['tanggal_booking'] = date('Y-m-d');
        $storeData['tanggal_yang_dibooking'] = $jadwalHarian->tanggal;

        $presensiBookingKelas = PresensiBookingKelas::create($storeData);         

        return response([
            'message' => 'Add Presensi Booking Kelas Success',
            'data' => $presensiBookingKelas,
        ], 200);
    }

    public function updatePresensiBookingGym($id_booking_gym){
        $presensiBookingGym = PresensiBookingGym::find($id_booking_gym);        

        if(is_null($presensiBookingGym)){
            return response([
                'message' => 'Presensi Booking Gym Not Found',
                'data' => null
            ], 404);
        }

        if ($presensiBookingGym->waktu_presensi != null) {
            return response([
                'message' => 'Presensi Booking Gym Already Confirmed',
                'data' => null
            ], 400);
        }

        //  buat tidak bisa presensi jika belum tanggal_yang_dibooking
        if ($presensiBookingGym->tanggal_yang_dibooking != date('Y-m-d')) {
            return response([
                'message' => 'Presensi Booking Gym Not Allowed',
                'data' => null
            ], 400);
        }
        
        $presensiBookingGym->waktu_presensi = date('Y-m-d H:i:s');
        
        $presensiBookingGym->save();
        return response([
            'message' => 'Update Presensi Booking Gym Success',
            'data' => $presensiBookingGym,
        ], 200);

        return response([
            'message' => 'Update Presensi Booking Gym Failed',
            'data' => null
        ], 400);
    }

    // public function updatePresensiBookingKelas($id_booking_kelas){
    //     $presensiBookingKelas = PresensiBookingKelas::find($id_booking_kelas);        

    //     if(is_null($presensiBookingKelas)){
    //         return response([
    //             'message' => 'Presensi Booking Kelas Not Found',
    //             'data' => null
    //         ], 404);
    //     }

    //     if ($presensiBookingGym->waktu_presensi != null) {
    //         return response([
    //             'message' => 'Presensi Booking Kelas Already Confirmed',
    //             'data' => null
    //         ], 400);
    //     }
        
    //     $presensiBookingGym->waktu_presensi = date('Y-m-d H:i:s');

    //     // buat agar sisa_deposit_uang_member berkurang
        
    //     // $member = Member::where('id_member', $presensiBookingGym->id_member)->first();
    //     // $member->sisa_deposit_uang_member = $member->sisa_deposit_uang_member - 5000;
    //     // $member->save();
        
    //     $presensiBookingGym->save();
    //     return response([
    //         'message' => 'Update Presensi Booking Gym Success',
    //         'data' => $presensiBookingGym,
    //     ], 200);

    //     return response([
    //         'message' => 'Update Presensi Booking Gym Failed',
    //         'data' => null
    //     ], 400);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroyPresensiBookingGym($id_booking_gym)
    {
        $presensiBookingGym = PresensiBookingGym::where('id_booking_gym', $id_booking_gym)->first();

        if(is_null($presensiBookingGym)){
            return response([
                'message' => 'Presensi Booking Gym Not Found',
                'data' => null
            ], 404);
        }

        if($presensiBookingGym->delete()){
            return response([
                'message' => 'Delete Presensi Booking Gym Success',
                'data' => $presensiBookingGym,
            ], 200);
        }
        
        return response([
            'message' => 'Delete Presensi Booking Gym Failed',
            'data' => null,
        ], 400);        
    }

    public function destroyPresensiBookingKelas($id_booking_kelas)
    {
        $presensiBookingKelas = PresensiBookingKelas::where('id_booking_kelas', $id_booking_kelas)->first();

        if(is_null($presensiBookingKelas)){
            return response([
                'message' => 'Booking Kelas Not Found',
                'data' => null
            ], 404);
        }

        if(date('Y-m-d') >= $presensiBookingKelas->tanggal_yang_dibooking){
            return response([
                'message' => 'Cancel Booking Kelas Maks H-1',
                'data' => null
            ], 400);
        }

        if($presensiBookingKelas->delete()){
            return response([
                'message' => 'Cancel Booking Kelas Success',
                'data' => $presensiBookingKelas,
            ], 200);
        }
        
        return response([
            'message' => 'Cancel Booking Kelas Failed',
            'data' => null,
        ], 400);        
    }
}
