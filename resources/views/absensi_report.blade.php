@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Data Kehadiran') }}</div>

                <div class="card-body">
                    <div class="mb-1 row">
                        <label for="inputPassword" class="col-sm-10 col-form-label text-end"><b>Periode</b></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm" id="periode" name="periode" value="{{ $periode }}">
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Jam</th>
                                    <th scope="col">Lokasi</th>
                                    <th scope="col">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $row )
                                <tr>
                                    <td>
                                        {{ $row->tanggal->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        {{ $row->tanggal->format('H:i:s') }}
                                    </td>
                                    <td>
                                        {{ $row->latitude .' , '.$row->longitude }}
                                    </td>
                                    <td>
                                        <a class="nav-link" target="_blank" href="{{ url('absensi/'.$row->tanggal->format('dmY').'/file/'.$row->filename.'/' ) }}">
                                            {{ $row->filename }}
                                        </a>
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
        $('#periode').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        }).on('changeDate', function(selected) {
            window.location.href = "{{ route('absensi.report') }}/" + $('#periode').val();

        });
    });
</script>
@endsection