@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Data Kehadiran') }}</div>

                <div class="card-body">
                    <div class="mb-1 row">
                        <label for="inputPassword" class="col-sm-10 col-form-label text-end"><b>Tanggal</b></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm" id="tgl" name="tgl" value="{{ $tanggal }}">
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 35%;">Nama</th>
                                    <th scope="col">Masuk</th>
                                    <th scope="col">Pulang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $row )
                                <tr>
                                    <td>
                                        {{ $row->nama }}
                                    </td>
                                    <td>
                                        @if( !empty($row->masuk) )
                                        {{ date('d-m-Y  H:i:s', strtotime($row->masuk)) }} &nbsp;
                                        <a class="btn btn-sm btn-primary" target="_blank" href="{{ url('absensi/'.date('dmY', strtotime($row->masuk)).'/file/'.$row->mfilename.'/' ) }}">
                                            <i class="fa-regular fa-file-image"></i> Foto
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if( !empty($row->pulang) )
                                        {{ date('d-m-Y  H:i:s', strtotime($row->pulang)) }} &nbsp;
                                        <a class="btn btn-sm btn-primary" target="_blank" href="{{ url('absensi/'.date('dmY', strtotime($row->pulang)).'/file/'.$row->pfilename.'/' ) }}">
                                            <i class="fa-regular fa-file-image"></i> Foto
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <b>Data Kosong</b>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link href="{{ asset('js/lib/datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/lib/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('#tgl').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        }).on('changeDate', function(selected) {
            window.location.href = "{{ route('report.absensi') }}/" + $('#tgl').val();

        });
    });
</script>
@endsection