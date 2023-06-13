<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;
use App\Models\Instruktur;
use App\Models\Member;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(is_null($request->username) || is_null($request->password)) {
            return response(['message' => 'Username / Password Empty'], 400);
        } 

        $pegawai = null;
        $instruktur = null;
        $member = null;

        // make token with random string
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if(Pegawai::where('nama_pegawai', '=', $loginData['username'])->first() ) {
            $loginPegawai = Pegawai::where('nama_pegawai', '=', $loginData['username'])->first();

            if(Hash::check($loginData['password'], $loginPegawai['password'])) {
                $pegawai = Pegawai::where('nama_pegawai', $loginData['username'])->first();
            }else{
                return response([
                    'message' => 'username or password false',
                    'data' => $pegawai
                ], 400);
            }
            $token = $pegawai->createToken('Authentication Toke')->accessToken;
            return response([
                'message' => 'Login pegawai success',
                'data' => $pegawai,
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        }else if(Instruktur::where('nama_instruktur', '=', $loginData['username'])->first()) {
            $loginInstruktur = Instruktur::where('nama_instruktur', '=', $loginData['username'])->first();

            if(Hash::check($loginData['password'], $loginInstruktur['password'])) {
                $instruktur = Instruktur::where('nama_instruktur', $loginData['username'])->first();
            }else{
                return response([
                    'message' => 'username or password false',
                    'data' => $instruktur
                ], 400);
            }
            $token = bcrypt($randomString);
            return response([
                'message' => 'Login instruktur success',
                'data' => $instruktur,
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        }else{
            $loginMember = Member::where('id_member','=',$loginData['username'])->first();

            if(Hash::check($loginData['password'], $loginMember['password'])) {
                $Member = Member::where('id_member', $loginData['username'])->first();
            }else{
                return response([
                    'message' => 'username or password false',                                     
                ], 400);
            }
            $token = bcrypt($randomString);
            return response([
                'message' => 'Login member success',
                'data' => $Member,
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }

    public function loginPegawai(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(is_null($request->username) || is_null($request->password)) {
            return response(['message' => 'Username / Password Empty'], 400);
        }         
        
        $loginPegawai = Pegawai::where('nama_pegawai', '=', $loginData['username'])->first();

        if ($loginPegawai) {
            if (Hash::check($loginData['password'], $loginPegawai['password'])) {
                 $Pegawai = Pegawai::where('nama_pegawai', $loginData['username'])->first();
            } else {
                return response([
                    'message' => 'Password salah',
                ], 400);
            }
        } else {
            return response([
                'message' => 'Username tidak ditemukan',
            ], 400);
        }

        $token = $Pegawai->createToken('Authentication Token')->accessToken;
        return response([
            'message' => 'Login pegawai success',
            'data' => $Pegawai,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }

    public function loginInstruktur(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(is_null($request->username) || is_null($request->password)) {
            return response(['message' => 'Username / Password Empty'], 400);
        }         
        
        $loginInstruktur = Instruktur::where('nama_instruktur', '=', $loginData['username'])->first();

        if ($loginInstruktur) {
            if (Hash::check($loginData['password'], $loginInstruktur['password'])) {
                 $Instruktur = Instruktur::where('nama_instruktur', $loginData['username'])->first();
            } else {
                return response([
                    'message' => 'Password salah',
                ], 400);
            }
        } else {
            return response([
                'message' => 'Username tidak ditemukan',
            ], 400);
        }

        $token = $Instruktur->createToken('Authentication Token')->accessToken;
        return response([
            'message' => 'Login instruktur success',
            'data' => $Instruktur,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }

    public function loginMember(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(is_null($request->username) || is_null($request->password)) {
            return response(['message' => 'Username / Password Empty'], 400);
        }         
        
        $loginMember = Member::where('id_member', '=', $loginData['username'])->first();

        if ($loginMember) {
            if (Hash::check($loginData['password'], $loginMember['password'])) {
                 $Member = Member::where('id_member', $loginData['username'])->first();
            } else {
                return response([
                    'message' => 'Password salah',
                ], 400);
            }
        } else {
            return response([
                'message' => 'Username tidak ditemukan',
            ], 400);
        }

        $token = $Member->createToken('Authentication Token')->accessToken;
        return response([
            'message' => 'Login member success',
            'data' => $Member,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }
        
}
