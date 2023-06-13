<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('loginPegawai', 'App\Http\Controllers\Api\AuthController@loginPegawai');
Route::post('loginInstruktur', 'App\Http\Controllers\Api\AuthController@loginInstruktur');
Route::post('loginMember', 'App\Http\Controllers\Api\AuthController@loginMember');
Route::post('login', 'App\Http\Controllers\Api\AuthController@login');

Route::group(['middleware' => 'auth:pegawaiPassport'], function(){
    Route::get('instruktur', 'App\Http\Controllers\Api\InstrukturController@index');
    Route::post('instruktur', 'App\Http\Controllers\Api\InstrukturController@store');
    Route::get('instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@show');
    Route::put('instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@update');
    Route::delete('instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@destroy');    
    
    Route::get('jadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@index');
    Route::post('jadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@store');
    // Route::get('jadwalUmumInstruktur', 'App\Http\Controllers\Api\JadwalUmumController@showJadwalUmumInstruktur');
    // Route::get('jadwalUmumKelas', 'App\Http\Controllers\Api\JadwalUmumController@showJadwalUmumKelas');
    Route::get('jadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@show');
    Route::put('jadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@update');
    Route::delete('jadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@destroy');
    
    Route::post('jadwalHarian', 'App\Http\Controllers\Api\JadwalHarianController@store');
    Route::delete('jadwalHarian', 'App\Http\Controllers\Api\JadwalHarianController@destroyAllData');

    Route::get('perizinanInstruktur', 'App\Http\Controllers\Api\PerizinanInstrukturController@index');    
    Route::get('perizinanInstrukturNotConfirmed', 'App\Http\Controllers\Api\PerizinanInstrukturController@indexPerizinanNotComrfirmed');    
    Route::put('perizinanInstruktur/{id}', 'App\Http\Controllers\Api\PerizinanInstrukturController@konfirmasiPerizinan');        

    Route::get('kelas', 'App\Http\Controllers\Api\KelasController@index');
    Route::get('promo', 'App\Http\Controllers\Api\PromoController@index');
    
    Route::get('member', 'App\Http\Controllers\Api\MemberController@index');
    // Route::get('memberDeactive', 'App\Http\Controllers\Api\MemberController@indexDeactiveMember');
    Route::post('member', 'App\Http\Controllers\Api\MemberController@store');
    Route::get('member/{id}', 'App\Http\Controllers\Api\MemberController@show');
    Route::put('member/{id}', 'App\Http\Controllers\Api\MemberController@update');
    Route::delete('member/{id}', 'App\Http\Controllers\Api\MemberController@destroy');
    Route::patch('member/{id}', 'App\Http\Controllers\Api\MemberController@resetPassword');
    Route::put('memberDeactive/{id}', 'App\Http\Controllers\Api\MemberController@deactiveMember');
    
    Route::get('transaksiAktivasi', 'App\Http\Controllers\Api\TransaksiAktivasiController@index');
    Route::post('transaksiAktivasi', 'App\Http\Controllers\Api\TransaksiAktivasiController@store');
    Route::get('transaksiAktivasi/{id}', 'App\Http\Controllers\Api\TransaksiAktivasiController@show');
    Route::delete('transaksiAktivasi/{id}', 'App\Http\Controllers\Api\TransaksiAktivasiController@destroy');
    
    Route::get('transaksiDepositUang', 'App\Http\Controllers\Api\TransaksiDepositUangController@index');
    Route::post('transaksiDepositUang', 'App\Http\Controllers\Api\TransaksiDepositUangController@store');
    Route::get('transaksiDepositUang/{id}', 'App\Http\Controllers\Api\TransaksiDepositUangController@show');
    Route::delete('transaksiDepositUang/{id}', 'App\Http\Controllers\Api\TransaksiDepositUangController@destroy');
    
    Route::get('transaksiDepositKelas', 'App\Http\Controllers\Api\TransaksiDepositKelasController@index');
    Route::get('transaksiDepositKelasKadaluarsa', 'App\Http\Controllers\Api\TransaksiDepositKelasController@indexKadaluarsaDepositKelas');
    Route::post('transaksiDepositKelas', 'App\Http\Controllers\Api\TransaksiDepositKelasController@store');
    Route::get('transaksiDepositKelas/{id}', 'App\Http\Controllers\Api\TransaksiDepositKelasController@show');
    Route::delete('transaksiDepositKelas/{id}', 'App\Http\Controllers\Api\TransaksiDepositKelasController@destroy');
    Route::put('transaksiDepositKelasReset/{id}', 'App\Http\Controllers\Api\TransaksiDepositKelasController@resetDepositKelas');

    Route::get('presensiBookingGym', 'App\Http\Controllers\Api\PresensiController@indexPresensiBookingGym');
    Route::put('presensiBookingGym/{id}', 'App\Http\Controllers\Api\PresensiController@updatePresensiBookingGym');

    Route::get('presensiBookingKelas', 'App\Http\Controllers\Api\PresensiController@indexPresensiBookingKelas');
    
    Route::get('pegawai', 'App\Http\Controllers\Api\PegawaiController@index');
    Route::get('pegawai/{id}', 'App\Http\Controllers\Api\PegawaiController@show');

    Route::get('laporanAktivitasGymBulanan', 'App\Http\Controllers\Api\LaporanController@laporanAktivitasGymBulanan');
    Route::get('laporanAktivitasKelasBulanan', 'App\Http\Controllers\Api\LaporanController@laporanAktivitasKelasBulanan');
    Route::get('laporanKinerjaInstrukturBulanan', 'App\Http\Controllers\Api\LaporanController@laporanKinerjaInstrukturBulanan');
    Route::get('laporanPendapatanPerBulanDalamTahunTertentu', 'App\Http\Controllers\Api\LaporanController@laporanPendapatanPerBulanDalamTahunTertentu');
});

Route::group(['middleware' => 'auth:instrukturPassport'], function(){    
    Route::get('instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@show');
            
    Route::get('perizinanInstruktur/{id}', 'App\Http\Controllers\Api\PerizinanInstrukturController@indexPerizinanInstrukturByIdInstruktur');
    Route::post('perizinanInstruktur', 'App\Http\Controllers\Api\PerizinanInstrukturController@store');

    Route::get('jadwalHarian/{id}', 'App\Http\Controllers\Api\JadwalHarianController@indexJadwalHarianByIdInstruktur');
});

Route::group(['middleware' => 'auth:memberPassport'], function(){
    Route::get('member/{id}', 'App\Http\Controllers\Api\MemberController@show');
    
    Route::get('presensiBookingGym/{id}', 'App\Http\Controllers\Api\PresensiController@indexPresensiBookingGymByIdMember');
    Route::post('presensiBookingGym', 'App\Http\Controllers\Api\PresensiController@storePresensiBookingGym');
    Route::delete('presensiBookingGym/{id}', 'App\Http\Controllers\Api\PresensiController@destroyPresensiBookingGym');
    
    Route::get('presensiBookingKelas/{id}', 'App\Http\Controllers\Api\PresensiController@indexPresensiBookingKelasByIdMember');
    Route::post('presensiBookingKelas', 'App\Http\Controllers\Api\PresensiController@storePresensiBookingKelas');
    Route::delete('presensiBookingKelas/{id}', 'App\Http\Controllers\Api\PresensiController@destroyPresensiBookingKelas');
    
    Route::get('getDepositKelasMember/{id}', 'App\Http\Controllers\Api\MemberController@getDepositKelasMember');    
});


Route::get('jadwalHarian', 'App\Http\Controllers\Api\JadwalHarianController@index');


// Route::get('member/{id}', 'App\Http\Controllers\Api\MemberController@show');




