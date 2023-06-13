<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\DepositKelasMember;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member = Member::all();
        
        if(count($member) > 0){
            return response([
                'message' => 'Retrieve data success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'No data',
            'data' => null
        ], 404);
    }

    // public function indexDeactiveMember()
    // {
    //     $today = date('Y-m-d'); // Mendapatkan tanggal hari ini
    //     $members = Member::where('masa_berlaku_member', $today)->get();
        
    //     if ($members->count() > 0) {
    //         return response([
    //             'message' => 'Retrieve data success',
    //             'data' => $members
    //         ], 200);
    //     }

    //     return response([
    //         'message' => 'No data',
    //         'data' => null
    //     ], 404);
    // }

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
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'tanggal_lahir_member' => 'required|date',
            'no_telepon_member' => 'required',
            'jenis_kelamin_member' => 'required',                        
            'email' => 'required|email',            
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
	    $id = IdGenerator::generate(['table' => 'member', 'length' => 8, 'prefix' => date('y.m.'), 'field'=>'id_member']);        
        $storeData['id_member'] = $id;

        $storeData['status_member'] = 'Tidak Aktif';
        $storeData['sisa_deposit_uang_member'] = 0;        
        $storeData['password'] = bcrypt($storeData['tanggal_lahir_member']);

        $member = Member::create($storeData);

        return response([
            'message' => 'Add Member Success',
            'data' => $member,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_member)
    {
        $member = Member::find($id_member);
        
        $depositKelasMember = DepositKelasMember::where([
            'id_member' => $member->id_member,                
        ])->first();

        if (!is_null($member)) {
            return response([
                'message' => 'Retrieve data success',
                'data' => [
                    'member' => $member,
                    'deposit_kelas_member' => $depositKelasMember,
                ]
            ], 200);

            return response([
                'message' => 'Member not found',
                'data' => null
            ], 404);
        }
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
    public function update(Request $request, $id_member)
    {
        $member = Member::find($id_member);    
        if(is_null($member)){
            return response([
                'message' => 'Member not found',
                'data' => null
            ], 404);
        }

        $update = $request->all();

        $validate = Validator::make($update, [
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'tanggal_lahir_member' => 'required|date',
            'no_telepon_member' => 'required',
            'jenis_kelamin_member' => 'required',            
            'sisa_deposit_uang_member' => 'required',
            'email' => 'required|email',
            // 'password' => 'required|string',
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $member->nama_member = $update['nama_member'];
        $member->alamat_member = $update['alamat_member'];
        $member->tanggal_lahir_member = $update['tanggal_lahir_member'];
        $member->no_telepon_member = $update['no_telepon_member'];
        $member->jenis_kelamin_member = $update['jenis_kelamin_member'];        
        $member->sisa_deposit_uang_member = $update['sisa_deposit_uang_member'];
        $member->email = $update['email'];
        // $member->password = bcrypt($updateData['password']);

        if($member->save()){
            return response([
                'message' => 'Update Member Success',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Update Member Failed',
            'data' => null, 
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_member)
    {
        $member = Member::find($id_member);

        if(is_null($member)){
            return response([
                'message' => 'Member not found',
                'data' => null
            ], 404);
        }

        if($member->delete()){
            return response([
                'message' => 'Delete Member Success',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Delete Member Failed',
            'data' => null,
        ], 400);
    }

    public function resetPassword($id_member){
        $member = Member::find($id_member);

        if (is_null($member)) {
            return response([
                'message' => 'Member not found',
                'data' => null
            ], 404);
        }
     
        $member->password = bcrypt($member->tanggal_lahir_member);
        if($member->save()){
            return response([
                'message' => 'Reset Password Member Success',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Reset Password Member Failed',
            'data' => null,
        ], 400);
    }

    public function deactiveMember($id_member){
        $member = Member::find($id_member);
        $today = date('Y-m-d');

        if(is_null($member)){
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ], 404);
        }

        if($member->status_member == 'Tidak Aktif'){
            return response([
                'message' => 'Member Already Deactive',
                'data' => null
            ], 404);
        }

        if ($member->status_member == 'Aktif') {
            if ($member->masa_berlaku_member != $today) {
                return response([
                    'message' => 'Member Belum Saatnya di Deactive',
                    'data' => null
                ], 404);
            }
            $member->status_member = 'Tidak Aktif';
            $member->masa_berlaku_member = null;
            $member->save();
            return response([
                'message' => 'Deactivate Member Success',
                'data' => $member
            ], 200);
        }


        return response([
            'message' => 'Deactive Member Failed',
            'data' => null
        ], 400);
    }

    public function getDepositKelasMember($id_member){
        $depositKelasMember = DepositKelasMember::with('MemberForeignKey', 'KelasForeignKey')
            ->where('id_member', $id_member)            
            ->get();

        if ($depositKelasMember->isEmpty()) {
            return response([
                'message' => 'Deposit Kelas Member Not Found',
                'data' => null
            ], 404);
        }

        return response([
            'message' => 'Retrieve Deposit Kelas Member Success',
            'data' => $depositKelasMember
        ], 200);

    }
}
