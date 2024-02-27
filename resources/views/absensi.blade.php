@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('ABSENSI') }}</div>

                <div class="card-body d-flex align-items-center justify-content-center">
                    <form role="form" class="form-horizontal" id="form-absen" method="post">
                        @csrf
                        <div class="card" style="width: 18rem;">
                            <video autoplay="true" id="videoElement" disablePictureInPicture="true" controlsList="nodownload"></video>
                            <div id="googleMap" style="width:100%;height:125px; margin-top:2px"></div>
                            <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button id="save-absen" type="button" class="btn btn-sm btn-primary"> Simpan </button>
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
    var video = document.querySelector("#videoElement");
    var canvas = document.querySelector("#canvas");
    var lat, long;

    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(function(stream) {
                video.srcObject = stream;
                getLocation();
            })
            .catch(function(error) {
                console.log(error)
                alert("permission camera");
            });
    } else {
        alert('sistem error');
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        lat = position.coords.latitude
        long = position.coords.longitude;
        if (!lat || !long) {
            alert('Lokasi tidak ditemukan');
            return false;
        }
        myMap(lat, long);
    }

    function myMap(lat, long) {
        let mapProp = {
            center: new google.maps.LatLng(lat, long),
            zoom: 21,
            mapTypeControl: false,
            draggable: false,
            zoomControl: false,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            //fullscreenControl: false,
            keyboardShortcuts: false
        };

        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, long),
            map: map,
        });
    }

    $('#save-absen').click(function() {
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let image = canvas.toDataURL('image/jpeg');
        let data = {
            "_token": "{{ csrf_token() }}",
            "image": image,
            "latitude": lat,
            "longitude": long
        };

        $.post("{{ route('absensi.save') }}", data, function(result) {
            if (result.error == 0) {
                alert('Data berhasil disimpan');
                location.reload(true);
            } else if (result.error == 1) {
                if (result.code == 'csrf' || result.code == 'other') {
                    alert(result.message);
                }

                if (result.code == 'validation') {
                    alert('System error : lokasi tidak valid');
                }
            } else {
                alert("Terdapat kesalahan, segera hubungi admin!");
            }
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxo2e2wC0EWmZ5gdtroKuFs1TUPCTbAq0"></script>
@endsection