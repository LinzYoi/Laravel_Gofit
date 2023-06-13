<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CobaCobaController extends Controller
{
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_member' => 'required',
            // making id_insturktur, hari_kelas, and jam_kelas unique in the same time
            'id_jadwal_harian' => 'required',
            'jenis_pembayaran' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $member = Member::where('id', $request->id_member)->first();
        $jadwalHarian = JadwalHarian::where('id', '=', $storeData['id_jadwal_harian'])->first();

        // cek member aktif
        if ($member->status == 'Inactive') {
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        // cek jadwal harian
        if ($jadwalHarian->status == 'Inactive') {
            return response([
                'message' => 'Jadwal Harian Tidak Aktif',
            ], 400);
        }
        if(is_null($jadwalHarian)){
            return response([
                'message' => 'Jadwal Harian Tidak Ditemukan',
            ], 400);
        }

        $sisaDeposit = DepositKelas::where([
            'id_member' => $member['id'],
            'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
        ])->first();

        
        $cekPenuh = PresensiBookingKelas::where([
            'id_jadwal_harian' => $request->id_jadwal_harian,
        ])->count();
        if ($cekPenuh == 10) {
            return response([
                'message' => 'Kelas Penuh',
            ], 400);
        }

        // Cek Tarif jika deposit kelas
        if ($storeData['jenis_pembayaran'] == "Deposit Kelas") {
            if (is_null($sisaDeposit)) {
                return response([
                    'message' => 'Deposit Kelas kosong',
                ], 404);
            }
            if ($sisaDeposit['sisa_deposit'] <= 0) {
                return response([
                    'message' => 'Sisa Deposit Tidak Mencukupi',
                ], 404);
            }
            
            // Cek Tarif jika bukan deposit uang
        } else {
            if ($member['deposit_uang'] < $jadwalHarian['FJadwalUmum']['Fkelas']['harga']) {
                return response([
                    'message' => 'Deposit Uang Tidak Mencukupi',
                ], 400);
            }
            
        }

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'presensi_booking_kelas', 'length' => 9, 'prefix' => $date]);
        $storeData['id'] = $id;
        $storeData['tanggal_booking'] = date('Y-m-d');
        $storeData['tanggal_yang_dibooking'] = $jadwalHarian['tanggal_jadwal_harian'];
        $presensiBookingkelas = PresensiBookingKelas::create($storeData);

        return response([
            'message' => 'Berhasil Menambahkan Data',
            'data' => $presensiBookingkelas,
        ], 200);
    }
}
