<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UnitAbsensi;

use Exception;

class UserController extends Controller
{
    public static $token = "administrator.user";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'user',
            [
                'employees' => DB::table('bangil_kepegawaian.ms_user')->get(),
                'groups'    => UserGroup::where('status', 1)->get(),
                'locations' => UnitAbsensi::where('status', 1)->get(),
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $input      = $request->all();
        $username   = empty($input['username']) ? '' : $input['username'];
        $nama       = empty($input['nama'])   ? '' : $input['nama'];

        $aColumns   = array(
            'k.nama',
            'k.username',
            'ua.nama',
            'ug.nama',
        );

        $iTotalRecords  = DB::table('users as u')
            ->selectRaw('COUNT(*) AS total')
            ->join('bangil_kepegawaian.ms_user as k', 'k.id', '=', 'u.id_pegawai')
            ->leftJoin('unit_absensi as ua', 'ua.id', '=', 'u.id_unit_absensi')
            ->leftJoin('user_groups as ug', 'ug.id', '=', 'u.id_group')
            ->where('k.nama', 'LIKE', '%' . $nama . '%')
            ->where('k.username',   'LIKE', '%' . $username . '%')
            ->first();

        $iDisplayLength = intval($input['iDisplayLength']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords->total : $iDisplayLength;
        $iDisplayStart  = intval($input['iDisplayStart']);
        $sEcho          = intval($input['sEcho']);
        $iSortCol_0     = $input['iSortCol_0'];

        $sortCol = 'u.name';
        $sortBy  = 'ASC';
        if (isset($iSortCol_0)) {
            $sortCol = $aColumns[$input['iSortCol_0']];
            $sortBy  = $input['sSortDir_0'];
        }

        $users  = DB::table('users as u')
            ->selectRaw('u.id data, k.nama, k.username, ua.nama lokasi, ug.nama ugroup')
            ->join('bangil_kepegawaian.ms_user as k', 'k.id', '=', 'u.id_pegawai')
            ->leftJoin('unit_absensi as ua', 'ua.id', '=', 'u.id_unit_absensi')
            ->leftJoin('user_groups as ug', 'ug.id', '=', 'u.id_group')
            ->where('k.nama', 'LIKE', '%' . $nama . '%')
            ->where('k.username',   'LIKE', '%' . $username . '%')
            ->take($iDisplayLength)->skip($iDisplayStart)
            ->orderBy($sortCol, $sortBy)
            ->get();

        return Response::json([
            "recordsTotal"      => $iTotalRecords->total,
            "recordsFiltered"   => $iTotalRecords->total,
            "data"              => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'id_pegawai'        => 'required|string|max:255|unique:users',
            'id_unit_absensi'   => 'required|numeric',
            'id_group'          => 'required|numeric',
        ];

        $messages = [
            'required'  => 'Tidak boleh kosong',
            'string'    => 'Format string',
            'numeric'   => 'Format angka',
            'min'       => 'Input minimal :min karakter',
            'max'       => 'Input maksimal :max karakter',
            'email'     => 'Format email',
            'unique'    => 'Data sudah terdaftar',
            'id_pegawai.unique'    => 'Data pegawai sudah terdaftar'
        ];

        //if edit
        if (!empty($input['id'])) {
            $rules['id_pegawai'] = 'required|string|max:255|unique:users,id,' . $input['id'];
        }

        $validator  = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Response::json([
                'error'    => 1,
                'message'  => $validator->errors(),
                'code'     => 'validation'
            ]);
        }

        try {
            $pegawai = DB::table('bangil_kepegawaian.ms_user')->where('id', $input['id_pegawai'])->first();
            if ($pegawai === null && empty($pegawai)) {
                throw new \Exception('sistem error : data pegawai tidak ditemukan');
            }

            $user = User::firstOrNew(['id' => $input['id']]);
            $user->id_pegawai   = $pegawai->id;
            $user->name         = $pegawai->nama;
            $user->email        = $pegawai->email;
            $user->id_group     = $input['id_group'];
            $user->id_unit_absensi = $input['id_unit_absensi'];
            if (empty($input['id'])) {
                $user->created_by = Auth::user()->id_pegawai;
            } else {
                $user->edited_by  = Auth::user()->id_pegawai;
            }
            $user->save();

            return Response::json([
                'error'     => 0,
                'message'   => 'Data berhasil disimpan',
                'code'      => ''
            ]);
        } catch (\Exception $ex) {
            return Response::json([
                'error'    => 1,
                'message'  => $ex->getMessage(),
                'line'     => $ex->getLine(),
                'code'     => 'other'
            ]);
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
        try {
            $id = empty($id) ? 0 : $id;

            $user = User::where('id', $id)->first();
            if ($user === null && empty($user))
                throw new Exception("Data not found");

            return Response::json([
                'error' => 0,
                'data'  => [
                    'id'                => $user->id,
                    'id_pegawai'        => $user->id_pegawai,
                    'id_group'          => $user->id_group,
                    'id_unit_absensi'   => $user->id_unit_absensi,
                ]
            ]);
        } catch (\Exception $ex) {
            return Response::json([
                'error'    => 1,
                'message'  => $ex->getMessage(),
                'line'     => $ex->getLine(),
                'code'     => 'other'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $id   = empty($id) ? 0 : $id;
            $user = User::where('id', $id)->first();
            if ($user === null && empty($user))
                throw new Exception("Data not found");

            $user->delete();
            return Response::json([
                'error'     => 0,
                'message'   => 'Data berhasil dihapus',
                'code'      => ''
            ]);
        } catch (\Exception $ex) {
            return Response::json([
                'error'    => 1,
                'message'  => $ex->getMessage(),
                'line'     => $ex->getLine(),
                'code'     => 'other'
            ]);
        }
    }
}
