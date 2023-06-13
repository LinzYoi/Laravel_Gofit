<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Instruktur;
use App\Http\Resources\InstrukturResource;

class InstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur = Instruktur::all();

        if(count($instruktur) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'No data',
            'data' => null
        ], 404);
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
            'nama_instruktur' => 'required',
            'alamat_instruktur' => 'required',
            'tanggal_lahir_instruktur' => 'required|date',
            'no_telepon_instruktur' => 'required',
            'gaji_instruktur' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 400);
        }

        $storeData['password'] = bcrypt($storeData['password']);
        $storeData['akumulasi_terlambat'] = 0;

        $instruktur = Instruktur::create($storeData);
        
        return response([
            'message' => 'Instruktur Added',
            'data' => $instruktur
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id_instruktur
     * @return \Illuminate\Http\Response
     */
    public function show($id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);

        if(!is_null($instruktur)){
            return response([
                'message' => 'Retrieve get success',
                'data' => $instruktur
            ], 200);
        }
        return response([
            'message' => 'Instruktur not found',
            'data' => null
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id_instruktur
     * @return \Illuminate\Http\Response
     */
    public function edit($id_instruktur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_instruktur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur not found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_instruktur' => 'required',
            'alamat_instruktur' => 'required',
            'tanggal_lahir_instruktur' => 'required|date',
            'no_telepon_instruktur' => 'required',
            'gaji_instruktur' => 'required',
            'email' => 'required|email',
            'password' => 'string',
        ]);        

        if($validate->fails()){
            return response(['message' => $validate->errors()], 200);
        }

        $instruktur->nama_instruktur=$updateData['nama_instruktur'];
        $instruktur->alamat_instruktur=$updateData['alamat_instruktur'];
        $instruktur->tanggal_lahir_instruktur=$updateData['tanggal_lahir_instruktur'];
        $instruktur->no_telepon_instruktur=$updateData['no_telepon_instruktur'];
        $instruktur->gaji_instruktur=$updateData['gaji_instruktur'];
        $instruktur->akumulasi_terlambat=$updateData['akumulasi_terlambat'];
        $instruktur->email=$updateData['email'];
        $instruktur->password=$updateData['password'];

        if($instruktur->save()){
            return response([
                'message' => 'Update instruktur success',
                'data' => $instruktur
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id_instruktur
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur not found',
                'data' => null
            ], 404);
        }
        
        if($instruktur->delete()){
            return response([
                'message' => 'Delete instruktur success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Delete instruktur failed',
            'data' => null
        ], 400);
    }

    // belum jadi
    public function resetPassword(Request $request, $id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur not found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'password' => 'required|string',
        ]);        

        if($validate->fails()){
            return response(['message' => $validate->errors()], 200);
        }

        $instruktur->password=$updateData['password'];

        if($instruktur->save()){
            return response([
                'message' => 'Update instruktur success',
                'data' => $instruktur
            ], 200);
        }
    }
}
