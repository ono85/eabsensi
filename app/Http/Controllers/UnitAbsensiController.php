<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\UnitAbsensi;

class UnitAbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('unit_absensi');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $input = $request->all();

        $aColumns   = array(
            'nama',
            'latitude',
            'longitude',
            'radius',
        );

        $iTotalRecords  = UnitAbsensi::select(DB::raw('COUNT(id) as total'))->first();

        $iDisplayLength = intval($input['iDisplayLength']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords->total : $iDisplayLength;
        $iDisplayStart  = intval($input['iDisplayStart']);
        $sEcho          = intval($input['sEcho']);
        $iSortCol_0     = $input['iSortCol_0'];

        $sortCol = 'nama';
        $sortBy  = 'ASC';
        if (isset($iSortCol_0)) {
            $sortCol = $aColumns[$input['iSortCol_0']];
            $sortBy  = $input['sSortDir_0'];
        }

        $item  = UnitAbsensi::take($iDisplayLength)->skip($iDisplayStart)
            ->orderBy($sortCol, $sortBy)
            ->get();

        $rec = [];
        foreach ($item as $row) {
            $rec[] = [
                'data'      => $row->id,
                'nama'      => $row->nama,
                'latitude'  => $row->latitude,
                'longitude' => $row->longitude,
                'radius'    => $row->radius,
                'status'    => $row->status,
            ];
        }

        return Response::json([
            "recordsTotal"      => $iTotalRecords->total,
            "recordsFiltered"   => $iTotalRecords->total,
            "data"              => $rec,
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
            'nama'      => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius'    => 'required||integer|min:1|max:50',
        ];

        $validator  = Validator::make($input, $rules);
        if ($validator->fails()) {
            return Response::json([
                'error'    => 1,
                'message'  => $validator->errors(),
                'code'     => 'validation'
            ]);
        }

        try {
            $ua = UnitAbsensi::firstOrNew(['id' => $input['id']]);
            $ua->nama       = $input['nama'];
            $ua->latitude   = $input['latitude'];
            $ua->longitude  = $input['longitude'];
            $ua->radius     = $input['radius'];
            if (empty($input['id']))
                $ua->created_by = Auth::user()->id_pegawai;
            else
                $ua->edited_by  = Auth::user()->id_pegawai;
            $ua->save();

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
            $ua = UnitAbsensi::find($id, ['id', 'nama', 'radius', 'latitude', 'longitude']);
            return Response::json([
                'data'      => $ua,
                'error'     => 0,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function set_status($id)
    {
        try {
            $ua = UnitAbsensi::find($id);
            $ua->status = ($ua->status == 1) ? '0' : '1';
            $ua->save();
            return Response::json([
                'error'     => 0,
                'message'   => 'Data berhasil diubah',
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

    public function setLokasi()
    {
        //RSUD BANGIL '-7.6051938, 112.8182255'
        return  '-7.6051938, 112.8182255';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function map()
    {
        return view('unit_absensi_map', [
            'position' => self::setLokasi(),
            'radius' => 130
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function map_data()
    {
        try {
            $data = UnitAbsensi::where('status', 1)->get();
            return Response::json([
                'error'   => 0,
                'data'    => $data
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
