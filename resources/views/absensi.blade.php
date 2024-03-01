@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('ABSENSI') }}

                    <button id="save-absen" type="button" class="btn btn-sm btn-primary float-end"> Simpan </button>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <form role="form" class="form-horizontal" id="form-absen" method="post">
                                @csrf
                                <div class="card" style="width: 100%;">
                                    <video autoplay="true" id="videoElement" disablePictureInPicture="true" controlsList="nodownload"></video>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-6">
                            <div class="card" style="width: 100%">
                                <div id="googleMap" style="width:100%;height:250px;"></div>
                                <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
                            </div>
                        </div>
                    </div>
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
    const latUnit = <?php echo  $latUnit ?>;
    const longUnit = <?php echo $longUnit ?>;

    var video = document.querySelector("#videoElement");
    var canvas = document.querySelector("#canvas");
    var radius = <?php echo $radius  ?>;
    var errorDistance = false;
    var lat, long;

    const optionLocation = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0,
    };

    getLocation();
    getStream();

    function getStream() {
        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    video.srcObject = stream;
                })
                .catch(function(error) {
                    console.log(error)
                    alert("permission camera");
                });
        } else {
            alert('sistem error');
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(myMap, errorPosition, optionLocation);
        } else {
            alert("Geolocation is not supported");
        }
    }

    function errorPosition(err) {
        alert(err.message);
    }

    function myMap(position) {
        let lat = position.coords.latitude
        let long = position.coords.longitude;
        if (!lat || !long) {
            alert('Lokasi tidak ditemukan');
            return false;
        }

        let distance = getDistance({
            'lat': lat,
            'lng': long
        }, {
            'lat': latUnit,
            'lng': longUnit
        });
        if (distance > radius) {
            errorDistance = true;
            alert('Posisi lebih jauh dari lokasi absen');
        }

        let mapProp = {
            center: new google.maps.LatLng(lat, long),
            zoom: 21,
            mapTypeControl: false,
            // draggable: false,
            zoomControl: false,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            //fullscreenControl: false,
            keyboardShortcuts: false
        };

        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        //lokasi user
        var userMarker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, long),
            map: map,
            icon: 'images/map-person.png',
            title: "Posisi sekarang",
        });

        //lokasi unit
        var unitMarker = new google.maps.Marker({
            position: new google.maps.LatLng(latUnit, longUnit),
            map: map,
            title: "Posisi unit",
        });

        //radius lokasi
        var sunCircle = {
            strokeColor: "#c3fc49",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#c3fc49",
            fillOpacity: 0.35,
            map: map,
            center: new google.maps.LatLng(latUnit, longUnit),
            radius: radius // in meters
        };
        cityCircle = new google.maps.Circle(sunCircle);
        cityCircle.bindTo('center', unitMarker, 'position');
    }

    $('#save-absen').click(function() {
        if (errorDistance) {
            alert('Posisi lebih jauh dari lokasi absen');
            return false;
        }

        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let image = canvas.toDataURL('image/jpeg');
        let data = {
            "_token": "{{ csrf_token() }}",
            "image": image,
            "latitude": lat,
            "longitude": long
        };

        if (confirm("Simpan data ?")) {
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
        }
    });

    function rad(x) {
        return x * Math.PI / 180;
    };

    function getDistance(p1, p2) {
        let R = 6378137; // Earth’s mean radius in meter
        let dLat = rad(p2.lat - p1.lat);
        let dLong = rad(p2.lng - p1.lng);
        let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat)) * Math.sin(dLong / 2) * Math.sin(dLong / 2);

        let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        let d = R * c;

        return Number(d).toFixed(2); // returns the distance in meter
    };
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxo2e2wC0EWmZ5gdtroKuFs1TUPCTbAq0"></script>
@endsection