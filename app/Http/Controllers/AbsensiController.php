<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use App\Models\Absensi;

class AbsensiController extends Controller
{
    const DIR_IMAGE_ABSENSI = 'public/img-absen/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('absensi', []);
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

    public function generate_name($ind)
    {
        return 'abs' . $ind . date('dmyhis') . substr(md5(mt_rand()), 0, 12);
    }

    public function check_base64_image($base64, $filename, $directory)
    {
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $file = $directory . $filename;
        Storage::put($file, base64_decode($base64), 'public');

        $image = Storage::path($file);
        $info  = getimagesize($image);
        if (!empty($info) && $info[0] > 0 && $info[1] > 0 && $info['mime']) {
            return true;
        } else {
            throw new \Exception('sistem error : foto invalid');
        }
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
            'latitude'    => 'required',
            'longitude'   => 'required',
        ];

        $validator  = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json([
                'error'    => 1,
                'message'  => $validator->errors(),
                'code'     => 'validation'
            ]);
        }

        try {
            $directory = self::DIR_IMAGE_ABSENSI . date('dmY') . '/';
            $base64 = str_replace(["data:image/png;base64,", "data:image/jpeg;base64,"], "", $input['image']);
            $filename = $this->generate_name(Auth::id()) . '.jpg';

            $this->check_base64_image($base64, $filename, $directory);

            DB::beginTransaction();
            $absensi = Absensi::firstOrNew(['id' => null]);
            $absensi->id_pegawai  = Auth::id();
            $absensi->latitude    = $input['latitude'];
            $absensi->longitude   = $input['longitude'];
            $absensi->tanggal     = date('Y-m-d H:i:s');
            $absensi->filename    = $filename;
            $absensi->save();

            DB::commit();
            return response()->json([
                'error'     => 0,
                'message'   => 'Data berhasil disimpan',
                'code'      => ''
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            if (Storage::exists($directory . $filename)) {
                Storage::delete($directory . $filename);
            }

            return response()->json([
                'error'    => 1,
                'message'  => $ex->getMessage(),
                'line'     => $ex->getLine(),
                'code'     => 'other'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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
     * @param  string  $periode
     * @return \Illuminate\Http\Response
     */
    public function report($periode = null)
    {
        if (!empty($periode)) {
            $tgl     = explode("-", $periode);
            $periode = $tgl[1] . '-' . $tgl[0];
        } else {
            $periode = date('Y-m');
        }

        $date = explode("-", $periode);
        $from = $periode . '-01 00:00:00';
        $to   = $periode . '-' . cal_days_in_month(CAL_GREGORIAN, $date[1], $date[0]) . ' 23:59:59';

        $absensi = Absensi::where('id_pegawai', Auth::id())
            ->whereBetween('tanggal', [$from, $to])
            ->orderBy('tanggal', 'DESC')->get();

        return view('absensi_report', [
            'records' => $absensi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $periode
     * @return \Illuminate\Http\Response
     */
    public function file($tgl, $filename)
    {
        $directory = 'app/' . self::DIR_IMAGE_ABSENSI . $tgl . '/';
        if (file_exists(storage_path($directory . $filename))) {
            $file = File::get(storage_path($directory . $filename));
            $type = File::mimeType(storage_path($directory . $filename));

            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }
    }
}
