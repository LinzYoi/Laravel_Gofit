<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PresensiBookingGym;
use App\Models\PresensiBookingKelas;

class LaporanController extends Controller
{
   public function laporanAktivitasGymBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;

        if (PresensiBookingGym::count() == 0) {
            return response([
                'message' => 'No Data',
            ]);
        }

        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }

        $tanggalCetak = [date('Y-m-d')];

        $aktivitasGymBulanan = PresensiBookingGym::where('tanggal_yang_dibooking', '<', $tanggalCetak[0])
            ->whereNotNull('waktu_presensi')
            ->whereMonth('tanggal_yang_dibooking', $bulan)
            ->get()
            ->groupBy(function ($item) {
                $carbonDate = Carbon::createFromFormat('Y-m-d', $item->tanggal_yang_dibooking);
                return $carbonDate->format('Y-m-d');
            });

        $responseData = [];

        foreach ($aktivitasGymBulanan as $tanggal => $grup) {
            $count = $grup->count();
            $responseData[] = [
                'tanggal' => $tanggal,
                'count' => $count,
            ];
        }

        return response([
            'data' => [
                'data_laporan' => $responseData,
                'tanggal_cetak' => $tanggalCetak[0]
            ]
        ]);
    }



    public function laporanAktivitasKelasBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;

        if (PresensiBookingKelas::count() == 0) {
            return response([
                'message' => 'No Data',                
            ]);
        }

        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }
        //* Tanggal Cetak
        $tanggalCetak = Carbon::now();
        $aktivitasKelasBulanan = DB::select('
            SELECT k.nama_kelas AS kelas, i.nama_instruktur AS instruktur, COUNT(bk.id_booking_kelas) AS jumlah_peserta, 
                COUNT(CASE WHEN jh.status = "diliburkan" THEN 1 ELSE NULL END) AS jumlah_libur
            FROM presensi_booking_kelas AS bk
            JOIN jadwal_harian AS jh ON bk.id_jadwal_harian = jh.id_jadwal_harian
            JOIN jadwal_umum AS ju ON jh.id_jadwal_umum = ju.id_jadwal_umum
            JOIN instruktur AS i ON ju.id_instruktur = i.id_instruktur
            JOIN kelas AS k ON ju.id_kelas = k.id_kelas
            WHERE MONTH(jh.tanggal) = ?
            GROUP BY k.nama_kelas, i.nama_instruktur
        ', [$bulan]);

        //akumulasi terlambat direset tiap bulan jam mulai tiap bulan - jam selesai bulan         
        return response([
            'data' => $aktivitasKelasBulanan,
            'tanggal_cetak' => $tanggalCetak,
        ]);        
    }

    public function laporanKinerjaInstrukturBulanan(Request $request) {
        $bulan = Carbon::now()->month;
        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }

        $tanggalCetak = Carbon::now();
        $kinerjaInstrukturBulanan = DB::select('
        SELECT i.nama_instruktur,
            SUM(CASE WHEN pi.id_presensi_instruktur IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_hadir,
            SUM(CASE WHEN iz.id_perizinan IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_izin,
            IFNULL(i.akumulasi_terlambat, 0) AS akumulasi_terlambat
        FROM instruktur AS i
        LEFT JOIN presensi_instruktur AS pi ON i.id_instruktur = pi.id_instruktur AND MONTH(pi.created_at) = ?
        LEFT JOIN perizinan_instruktur AS iz ON i.id_instruktur = iz.id_instruktur AND MONTH(iz.created_at) = ?
        GROUP BY i.nama_instruktur, i.akumulasi_terlambat   
    ', [$bulan, $bulan]);   
        return response([
            'data' => $kinerjaInstrukturBulanan,
            'tanggal_cetak' => $tanggalCetak,
        ]);
    }
    public function laporanPendapatanPerBulanDalamTahunTertentu(Request $request){        
        $year = Carbon::now()->year;
        $tanggalCetak = Carbon::now();
        if ($request->has('year') && !empty($request->year)) {
            $year = $request->year;
        }
        //*Group Pendapatannya perbulan
        //*Group Tampilan Pertahun -> Request->Year
        //*Group 
        $pendapatan = DB::select("
        SELECT
    bulan.nama_bulan,
    COALESCE(SUM(jumlah_pembayaran_aktivasi), 0) AS total_pendapatan_aktivasi,
    COALESCE(SUM(pendapatan_reguler + pendapatan_paket), 0) AS total_pendapatan_deposit,
    COALESCE(SUM(jumlah_pembayaran_aktivasi + pendapatan_reguler + pendapatan_paket), 0) AS total_pendapatan
FROM (
    SELECT 1 AS bulan_id, 'January' AS nama_bulan UNION ALL
    SELECT 2 AS bulan_id, 'February' AS nama_bulan UNION ALL
    SELECT 3 AS bulan_id, 'March' AS nama_bulan UNION ALL
    SELECT 4 AS bulan_id, 'April' AS nama_bulan UNION ALL
    SELECT 5 AS bulan_id, 'May' AS nama_bulan UNION ALL
    SELECT 6 AS bulan_id, 'June' AS nama_bulan UNION ALL
    SELECT 7 AS bulan_id, 'July' AS nama_bulan UNION ALL
    SELECT 8 AS bulan_id, 'August' AS nama_bulan UNION ALL
    SELECT 9 AS bulan_id, 'September' AS nama_bulan UNION ALL
    SELECT 10 AS bulan_id, 'October' AS nama_bulan UNION ALL
    SELECT 11 AS bulan_id, 'November' AS nama_bulan UNION ALL
    SELECT 12 AS bulan_id, 'December' AS nama_bulan
) AS bulan
LEFT JOIN (
    SELECT
        MONTH(ta.tanggal_aktivasi) AS bulan_id,
        ta.jumlah_pembayaran_aktivasi,
        0 AS pendapatan_reguler,
        0 AS pendapatan_paket
    FROM transaksi_aktivasi AS ta
    WHERE YEAR(ta.tanggal_aktivasi) = $year
    UNION ALL
    SELECT
        MONTH(tdu.tanggal_deposit_uang) AS bulan_id,
        0 AS jumlah_pembayaran_aktivasi,
        tdu.jumlah_pembayaran_deposit_uang AS pendapatan_reguler,
        0 AS pendapatan_paket
    FROM transaksi_deposit_uang AS tdu
    WHERE YEAR(tdu.tanggal_deposit_uang) = $year
    UNION ALL
    SELECT
        MONTH(tdk.tanggal_deposit_kelas) AS bulan_id,
        0 AS jumlah_pembayaran_aktivasi,
        0 AS pendapatan_reguler,
        tdk.jumlah_pembayaran_deposit_kelas AS pendapatan_paket
    FROM transaksi_deposit_kelas AS tdk
    WHERE YEAR(tdk.tanggal_deposit_kelas) = $year
) AS transaksi ON bulan.bulan_id = transaksi.bulan_id
GROUP BY bulan.bulan_id, bulan.nama_bulan
ORDER BY bulan.bulan_id

    ");

        return response([
            'data' => $pendapatan,
            'tanggal_cetak' => $tanggalCetak,
        ]);
    }
}
