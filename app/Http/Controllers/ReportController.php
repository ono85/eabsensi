<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use App\Models\UnitAbsensi;
use App\Models\Absensi;

class ReportController extends Controller
{
    const DIR_IMAGE_ABSENSI = 'public/img-absen/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home', []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $periode
     * @return \Illuminate\Http\Response
     */
    public function absensi($tgl = null)
    {
        $tanggal = date('Y-m-d');
        if (!empty($tgl)) {
            $tanggal = date("Y-m-d", strtotime($tgl));
        }

        $from = $tanggal . ' 00:00:00';
        $to   = $tanggal . ' 23:59:59';

        $absensi = DB::select("
            select
                u.nama,
                absm.tanggal as masuk,
                absm.filename as mfilename,
                absp.tanggal as pulang,
                absp.filename  as pfilename
            from
                bangil_kepegawaian.ms_user u
            left join (
                select
                    *
                from
                    eabsensi_bangil.absensi a
                where
                    a.tanggal between '$from' and '$to'
                order by
                    tanggal asc
                limit 1) absm on
                absm.id_pegawai = u.id
            left join (
                select
                    *
                from
                    eabsensi_bangil.absensi a
                where
                    a.tanggal between '$from' and '$to'
                order by
                    tanggal desc
                limit 1) absp on
                absp.id_pegawai = u.id
            order by
                u.nama desc
        ");

        return view('absensi_report_all', [
            'records'   => $absensi,
            'tanggal'   => date("d-m-Y", strtotime($tanggal))
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
