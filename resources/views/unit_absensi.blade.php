@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <strong>Unit Absen</strong>

                    <button class="btn btn-sm btn-success float-end" data-bs-toggle="modal" data-bs-target="#ModalUnitAbsen">
                        Add
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-unit_absen" class="table table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="text-align:left !important; width: 30%;">Nama</th>
                                    <th style="text-align:left !important; width: 15%;">Latitude</th>
                                    <th style="text-align:left !important; width: 15%;">Longitude</th>
                                    <th style="text-align:left !important; width: 15%">Radius</th>
                                    <th style="text-align:left !important; width: 10%">Aktif</th>
                                    <th style="text-align:center !important; width: 15%">...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalUnitAbsen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form id="formUnitAbsen">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px; padding-bottom:8px;">
                    <h5 class="modal-title" id="staticBackdropLabel">Form Unit Absen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="" class="form-control">
                    @csrf
                    <div class="mb-3 row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inp-nama" name="nama" value="">
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Latitude</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inp-latitude" name="latitude" value="" readonly>
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Longitude</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inp-longitude" name="longitude" value="" readonly>
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">Radius</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="inp-radius" name="radius" value="" style="width: 50%;">
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary btn-save">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    const optionLocation = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0,
    };

    $("#ModalUnitAbsen").on('shown.bs.modal', function() {
        $('.form-control').val('');
        $('.form-control').removeClass('is-invalid');

        getLocation();
    });

    $(".btn-save").click(function() {
        url = "{{ route('unit_absensi.save') }}";
        data = $('#formUnitAbsen').serialize();

        $('.form-control').removeClass('is-invalid');
        $.post(url, data, function(result) {
            if (result.error == 0) {
                $('#ModalUnitAbsen').modal('hide');
                $unitAbsen.ajax.reload();
            } else if (result.error == 1) {
                if (result.code == 'csrf' || result.code == 'other') {
                    alert(result.message);
                }

                if (result.code == 'validation') {
                    $.each(result.message, function(index, value) {
                        $("[name='" + index + "']").addClass('is-invalid').next().html(value);
                    });

                    alert('System Error');
                }
            } else {
                alert('System Error');
            }
        });
    });

    $("#table-unit_absen tbody").on("click", ".btn-delete", function() {
        id = $(this).data('id');
        url = "{{ url('unit_absensi/delete') }}/" + id;
        $.get(url, function(data) {
            if (data.error == 0) {
                $unitAbsen.ajax.reload();
            } else {
                alert('System Error');
            }
        }, "json");
    });

    $("#table-unit_absen tbody").on("click", ".btn-edit", function() {
        $('.form-control').val('');
        $('.form-control').removeClass('is-invalid');

        id = $(this).data('id');
        url = "{{ url('unit_absensi/edit') }}/" + id;
        $.get(url, function(result) {
            $('#ModalUnitAbsen').modal('show');
            $.each(result.data, function(index, value) {
                $('[name ="' + index + '"]').val(value);
            });

        }, "json");
    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(setPosition, errorPosition, optionLocation);
        } else {
            alert("Geolocation is not supported");
        }
    }

    function errorPosition(err) {
        alert(err.message);
    }

    function setPosition(position) {
        let lat = position.coords.latitude
        let long = position.coords.longitude;
        if (!lat || !long) {
            alert('Lokasi tidak ditemukan');
            return false;
        }

        $('#inp-latitude').val(lat);
        $('#inp-longitude').val(long);
    }

    function set_status(data) {
        $.get("{{ url('/unit_absensi/set_status/') }}/" + data, function(data) {
            if (data.error == 0) {
                $unitAbsen.ajax.reload();
            } else {
                alert('System error');
            }
        }, "json");
    }

    var $unitAbsen = $('#table-unit_absen').DataTable({
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [1, 2, 3, 4]
        }, ],
        //fixedHeader: true,
        orderCellsTop: true,
        "aaSorting": [
            [0, 'asc']
        ],
        // set the initial value
        "iDisplayLength": 10, // default records per page
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": true,
        "bProcessing": true, // enable/disable display message box on record load
        "bServerSide": true, // enable/disable server side ajax loading
        "sAjaxSource": "{{ route('unit_absensi.data') }}", // define ajax source URL
        "fnServerParams": function(aoData) {
            //aoData.push(
            //    { name:'nama',  value: $('#nama').val() }
            //);
        },
        "drawCallback": function() {
            $('.pagination').addClass('pagination-sm');
        },
        "sServerMethod": "GET",
        "aoColumns": [{
                mData: 'nama',
                "sWidth": "30%",
                "sClass": ""
            }, {
                mData: 'latitude',
                "sWidth": "15%",
                "sClass": ""
            },
            {
                mData: 'longitude',
                "sWidth": "15%",
                "sClass": ""
            },
            {
                mData: 'radius',
                "sWidth": "10%",
                "sClass": "",
                render: function(data, type, row) {
                    return row.radius + ' Meter'
                }
            },
            {
                mData: 'status',
                "sWidth": "10%",
                "sClass": "text-center",
                render: function(data, type, row) {

                    return (row.status == 1) ? '<b><i class="fa-solid fa-check"></i></b>' : ''
                }
            },
            {
                mData: 'data',
                "sWidth": "20%",
                "sClass": "text-center",
                render: function(data, type, row) {

                    $label = (row.status == 1) ? 'Non Aktif' : 'Aktif';
                    $btnAktif = '<button type="button" class="btn btn-sm btn-warning"' +
                        'onclick=set_status("' + row.data + '") >' + $label +
                        '</button>';

                    $btnEdit = '<button type="button" class="btn btn-sm btn-edit btn-primary"' +
                        'data-id="' + row.data + '" >' +
                        ' &nbsp; Edit &nbsp; ' +
                        '</button>'
                    return $btnAktif + ' ' + $btnEdit
                }
            },
        ]
    });
</script>
@endsection