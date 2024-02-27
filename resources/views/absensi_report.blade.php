@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Data Kehadiran') }}</div>

                <div class="card-body">
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
    @endsection

    @section('css')
    @endsection

    @section('scripts')
    <script type="text/javascript">
    </script>
    @endsection