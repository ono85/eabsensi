@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong>Data User</strong>

                    <div class="float-end ">
                        <button class="btn btn-sm btn-success btn-add">
                            <i class="fa-solid fa-plus"></i> Add
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive" style="margin-top: 10px; padding-bottom:15px;">
                        <table id="table-user" class="table table-bordered" style="width:100%">
                            <thead>
                                <tr class="table-light">
                                    <th style="text-align:left !important">Nama</th>
                                    <th style="text-align:left !important">Username</th>
                                    <th style="text-align:left !important">Lokasi Absen</th>
                                    <th style="text-align:left !important">Group</th>
                                    <th style="text-align:center !important">...</th>
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
<div class="modal fade" id="userModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form id="formUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px; padding-bottom:8px;">
                    <h5 class="modal-title" id="staticBackdropLabel">Form User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="" class="form-control">
                    @csrf
                    <div class="mb-1 row">
                        <label for="inputPassword" class="col-sm-3 col-form-label">Pegawai</label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm select2" name="id_pegawai" aria-label="Default select example" data-placeholder="Data pegawai">
                                <option></option>
                                @foreach( $employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                @endforeach()
                            </select>
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="inputPassword" class="col-sm-3 col-form-label">Lokasi Absen</label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm select2" name="id_unit_absensi" aria-label="Default select example" data-placeholder="Lokasi absen">
                                <option></option>
                                @foreach( $locations as $location)
                                <option value="{{ $location->id }}">{{ $location->nama }}</option>
                                @endforeach()
                            </select>
                            <div class="invalid-feedback" style="font-size: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="inputPassword" class="col-sm-3 col-form-label">Group</label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm select2" name="id_group" aria-label="Default select example" data-placeholder="Group user">
                                <option></option>
                                @foreach( $groups as $group)
                                <option value="{{ $group->id }}">{{ $group->nama }}</option>
                                @endforeach()
                            </select>
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

@section('css')

<link href="{{ asset('js/lib/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('js/lib/select2/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
<script src="{{ asset('js/lib/select2/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            containerCssClass: "select2--small",
            dropdownCssClass: "select2--small",
            dropdownParent: $('#userModal'),
        });

        $(".btn-add").click(function() {
            $('.form-control').val('');
            $(".select2").val('').trigger('change');
            $('.form-control, .form-select ').removeClass('is-invalid');
            $("#userModal").modal('show');
        });

        $("#table-user tbody").on("click", ".btn-delete", function() {
            if (confirm('Apakah data dihapus ? ')) {
                $.get("{{ url('user/delete') }}/" + $(this).data('id'), function(result) {
                    if (result.error == 0) {
                        alert('Data berhasil dihapus');
                        $user.ajax.reload();
                    } else if (result.error == 1) {
                        $msg = '';
                        if (result.code == 'csrf' || result.code == 'other')
                            $msg = ' : ' + result.message;

                        alert('System error' + $msg);
                    } else {
                        alert('System error');
                    }
                }, "json");
            }
        });

        $("#table-user tbody").on("click", ".btn-edit", function() {
            $.get("{{ url('user/edit') }}/" + $(this).data('id'), function(result) {
                if (result.error == 0) {
                    $(".btn-add").click();

                    $.each(result.data, function(index, value) {
                        $('[name ="' + index + '"]').val(value);
                        if ($('[name ="' + index + '"]').hasClass("select2"))
                            $('[name ="' + index + '"]').trigger('change');
                    });
                } else if (result.error == 1) {
                    if (result.code == 'csrf' || result.code == 'other') {
                        alert('System error : ' + result.message);
                    }
                } else {
                    alert('System error');
                }
            }, "json");
        });

        $(".btn-save").click(function() {
            if (confirm('Apakah data disimpan ? ')) {
                $('.form-control, .form-select ').removeClass('is-invalid');
                $.post("{{ route('user.save') }}", $('#formUser').serialize(), function(result) {
                    if (result.error == 0) {
                        alert('Data berhasil disimpan')
                        $('#userModal').modal('hide');
                        $user.ajax.reload();
                    } else if (result.error == 1) {
                        if (result.code == 'csrf' || result.code == 'other') {
                            alert('System error : ' + result.message);
                        }

                        if (result.code == 'validation') {
                            $.each(result.message, function(index, value) {
                                $inp = $("[name='" + index + "']");
                                $inp.addClass('is-invalid');
                                $inp.closest('.row').find('.invalid-feedback').html(value);
                            });

                            alert('System error');
                        }
                    } else {
                        alert('System error');
                    }
                });
            }
        });

        var $user = $('#table-user').DataTable({
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [4]
            }],
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
            "sAjaxSource": "{{ route('user.data') }}", // define ajax source URL
            "fnServerParams": function(aoData) {},
            "drawCallback": function() {
                $('.pagination').addClass('pagination-sm');
            },
            "sServerMethod": "GET",
            "aoColumns": [{
                    mData: 'nama',
                    "sWidth": "25%",
                    "sClass": ""
                },
                {
                    mData: 'username',
                    "sWidth": "20%",
                    "sClass": ""
                },
                {
                    mData: 'lokasi',
                    "sWidth": "20%",
                    "sClass": ""
                },
                {
                    mData: 'ugroup',
                    "sWidth": "15%",
                    "sClass": ""
                },
                {
                    mData: 'data',
                    "sWidth": "20%",
                    "sClass": "text-center",
                    render: function(data, type, row) {
                        $btnDelete = '<button type="button" class="btn btn-sm btn-delete btn-danger"' +
                            'data-id="' + row.data + '" >' +
                            '<i class="fa-solid fa-trash"></i> Hapus ' +
                            '</button>';

                        $btnEdit = '<button type="button" class="btn btn-sm btn-edit btn-primary"' +
                            'data-id="' + row.data + '" >' +
                            ' <i class="fa-regular fa-pen-to-square"></i> Edit ' +
                            '</button>'
                        return $btnDelete + ' ' + $btnEdit
                    }
                },
            ]
        });
    });
</script>
@endsection