<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use App\Models\Instruktur;

class JadwalHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwalHarian = JadwalHarian::with(['JadwalUmumForeignKey', 'JadwalUmumForeignKey.KelasForeignKey', 'InstrukturForeignKey'])->get();

        if(count($jadwalHarian) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $jadwalHarian
            ], 200);
        }

        return response([
            'message' => 'No Data',
            'data' => null,
        ], 400);
    }

    public function indexJadwalHarianByIdInstruktur($id_instruktur)
    {
        $jadwalHarian = JadwalHarian::with(['JadwalUmumForeignKey', 'JadwalUmumForeignKey.KelasForeignKey', 'InstrukturForeignKey'])
            ->where('id_instruktur', $id_instruktur)
            ->get();

        if (count($jadwalHarian) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwalHarian
            ], 200);
        }
        
        return response([
            'message' => 'No data',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $checkNull = JadwalHarian::all();
        if(count($checkNull) > 0){
            $jadwalHarian = JadwalHarian::latest()->take(30)->get();
            $dateNextWeek = Carbon::now()->addDays(7)->toDateString();
            foreach($jadwalHarian as $data){
                // $dateClassRunning = $data->date->addDays(7);
                foreach($jadwalHarian as $check)
                    if($check->tanggal == $dateNextWeek){
                        return response([
                            'message' => 'Jadwal Harian For Next Week Already Exist',
                            'data' => $data
                        ], 400);
                    }
                $tanggal = Carbon::createFromFormat('Y-m-d', $data->tanggal);
                $jadwalHarian = JadwalHarian::create([
                    'id_jadwal_umum' => $data->id_jadwal_umum,
                    'id_instruktur' => $data->id_instruktur,
                    'tanggal' => $tanggal->addDays(7),
                    'status' => 'Normal',
                    'slot_kelas' => 10,
                ]);
            }
        } else{
            $nextWeek = Carbon::now()->addDays(7);
            $loopDay = $nextWeek->startOfWeek();
            $jadwal_umum = JadwalUmum::all();

            foreach($jadwal_umum as $data){
                $dayString = ($loopDay->format('l'));
                if($data->hari == strtoupper(Str::substr($dayString, 0, 3))){
                    $jadwalHarian= JadwalHarian::create([
                        'id_jadwal_umum' => $data->id_jadwal_umum,
                        'id_instruktur' => $data->id_instruktur,
                        'tanggal' => $loopDay,
                        'status' => 'Normal',
                        'slot_kelas' => $data->slot_kelas,
                    ]);
                } else{
                    if($data->hari == 'MON'){
                        $nextWeek = Carbon::now()->addDays(7);
                        $loopDay = $nextWeek->startOfWeek();
                        $jadwalHarian = JadwalHarian::create([
                            'id_jadwal_umum' => $data->id_jadwal_umum,
                            'id_instruktur' => $data->id_instruktur,
                            'tanggal' => $loopDay,
                            'status' => 'Normal',
                            'slot_kelas' => $data->slot_kelas,
                        ]);
                    } else{
                        $loopDay->addDays(1);
                        $jadwalHarian = JadwalHarian::create([
                            'id_jadwal_umum' => $data->id_jadwal_umum,
                            'id_instruktur' => $data->id_instruktur,
                            'tanggal' => $loopDay,
                            'status' => 'Normal',
                            'slot_kelas' => $data->slot_kelas,
                        ]);
                    }                    
                }
            }
        }
        $jadwal_umum_baru = JadwalUmum::latest()->take(30)->get();

        return response([
            'message' => 'Add Jadwal Harian Success',
            'data' => $jadwal_umum_baru
        ], 200);
    }

    public function refactor(){
        $count = JadwalHarian::count();
        $jadwalHarian = null;

        if ($count > 0) {
            $jadwalHarian = JadwalHarian::orderBy('date_created', 'desc')->first();
        }

        $jadwal = Jadwal::get();
        $today = Carbon::today(); 

        if ($jadwalHarian !== null) {

            $jadwalHarianDate = Carbon::parse($jadwalHarian->date_created);
          
            $daysFromToday = $today->diffInDays($jadwalHarianDate);
           
            if ($daysFromToday <= 6) {
                return response([
                    'message' => 'Make Jadwal Harian Success',
                ], 400);
            }
        }

        $todayWhatDay = $today->dayOfWeekIso;

        foreach ($jadwal as $item) {
            $hari_ke = Carbon::parse($item->hari_kelas)->dayOfWeekIso;
            $tanggal = null;
            
               
            if ($todayWhatDay > $hari_ke) {
          
                $daysToAdd = 7 - ($todayWhatDay - $hari_ke);
                $tanggal = $today->copy()->addDays($daysToAdd);
            } else {
                $daysToAdd = $hari_ke - $todayWhatDay;
                $tanggal = $today->copy()->addDays($daysToAdd);
            }
            
            $jadwalHarian = new JadwalHarian();
            $jadwalHarian->id_jadwal_harian = $item->id;
            $jadwalHarian->id_instruktur = $item->id_instruktur;
            $jadwalHarian->keterangan = $item->nama_kelas;
            $jadwalHarian->tanggal_jadwal_harian = $tanggal;
            $jadwalHarian->jam_kelas = $item->sesi_kelas;
            $jadwalHarian->date_created = $today;
            $jadwalHarian->save();
        }

        return response([
            'message' => 'Data Successfully',
            'data' => $jadwalHarian,
            'today' => $today
        ], 200);
    }

    public function destroyAllData()
    {
        $jadwalHarian = JadwalHarian::all();
        foreach($jadwalHarian as $data){
            $data->delete();
        }
        return response([
            'message' => 'Delete All Jadwal Harian Success',
            'data' => $jadwalHarian
        ], 200);
    }
}
