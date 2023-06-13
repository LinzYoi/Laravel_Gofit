<?php

namespace App\Http\Controllers\Api;

use App\Models\PerizinanInstruktur;
use App\Models\Instruktur;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use App\Models\Kelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerizinanInstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perizinanInstruktur = PerizinanInstruktur::with('InstrukturForeignKey', 'JadwalHarianForeignKey.JadwalUmumForeignKey.KelasForeignKey')->get();

        if (count($perizinanInstruktur) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $perizinanInstruktur
            ], 200);
        } else {
            return response([
                'message' => 'No data',
                'data' => null
            ], 400);
        }
    }

    public function indexPerizinanInstrukturByIdInstruktur($id_instruktur) 
    {
        $perizinanInstruktur = PerizinanInstruktur::with('InstrukturForeignKey', 'JadwalHarianForeignKey.JadwalUmumForeignKey.KelasForeignKey')
            ->where('id_instruktur', $id_instruktur)
            ->get();

        if (count($perizinanInstruktur) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $perizinanInstruktur
            ], 200);
        } else {
            return response([
                'message' => 'No data',
                'data' => null
            ], 400);
        }        
    }

    public function indexPerizinanNotComrfirmed()
    {
        $perizinanInstruktur = PerizinanInstruktur::with('InstrukturForeignKey', 'JadwalHarianForeignKey')
            ->whereNull('status_perizinan')
            ->get();

        if (count($perizinanInstruktur) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $perizinanInstruktur
            ], 200);
        } else {
            return response([
                'message' => 'No data',
                'data' => null
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $storeData = $request->all(); //Ambil semua inputan
        $storeData['id_jadwal_harian'] = intval($storeData['id_jadwal_harian']);
        
        $validate = Validator::make($storeData, [
            'id_jadwal_harian' => 'required',
            'id_instruktur' => 'required',
            'tanggal_perizinan' => 'required',
            // 'tanggal_pembuatan_perizinan' => 'required',
            // 'status_perizinan' => 'required',
            'keterangan_perizinan' => 'required',
            // 'tanggal_konfirmasi_perizinan' => 'required',                                    
        ]);

        if($validate->fails()) {
            return response(['message' => $validate->errors()],400);            
        }

        $date = $storeData['tanggal_perizinan'];
        if($date > now()->format('Y-m-d')){
            $storeData['tanggal_pembuatan_perizinan'] = now()->format('Y-m-d');
            $perizinanInstruktur = PerizinanInstruktur::create($storeData);
            return response([
                'message' => 'Add Perizinan Instruktur Sukses',
                'data' => $perizinanInstruktur,
            ], 200);
        }

        return response([
            'message' => 'Tanggal Pembuatan Izin maksimal H-1',
            'data' => null
        ], 400);
    }

    public function konfirmasiPerizinan($id_perizinan){
        $perizinanInstruktur = PerizinanInstruktur::find($id_perizinan);
        $today = date('Y-m-d');

        if(is_null($perizinanInstruktur)){
            return response([
                'message' => 'Perizinan Instruktur Not Found',
                'data' => null
            ], 404);
        }

        if($perizinanInstruktur->status_perizinan != null){
            return response([
                'message' => 'Izin sudah dikonfirmasi',
                'data' => null
            ], 404);
        }
        
        $perizinanInstruktur->status_perizinan = 'Dikonfirmasi';
        $perizinanInstruktur->tanggal_konfirmasi_perizinan = $today;
                
        $perizinanInstruktur->save();
        return response([
            'message' => 'Konfirmasi Izin Success',
            'data' => $perizinanInstruktur
        ], 200);        


        return response([
            'message' => 'Konfirmasi Izin Failed',
            'data' => null
        ], 400);
    }
}
