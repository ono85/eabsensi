@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <strong>Lokasi Absen</strong>

                    <div class="float-end ">
                        <a class="btn btn-sm btn-secondary" href="{{ route('absensi') }}">
                            <i class="fa-solid fa-camera"></i> &nbsp;Absen
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div id="googleMap" style="width:100%;height:500px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxo2e2wC0EWmZ5gdtroKuFs1TUPCTbAq0&loading=async&callback=initMap"></script>
<script type="text/javascript">
    var map;
    var markers = [];

    function initMap() {
        let mapProp = {
            center: new google.maps.LatLng(<?php echo $position ?>),
            zoom: 18,
            mapTypeControl: false,
            //draggable: false,
            //zoomControl: false,
            //scrollwheel: false,
            //disableDoubleClickZoom: true,
            //fullscreenControl: false,
            keyboardShortcuts: false
        };

        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

        ShowAbsenLocation()
    }

    function ShowAbsenLocation() {
        $.getJSON("{{ route('absensi.map.data') }}", function(result) {
            if (result.error == 0) {
                if (result.data.length > 0)
                    drawLocation(result.data);
            } else if (result.error == 1) {
                if (result.code == 'csrf' || result.code == 'other')
                    alert(result.message);
                else
                    alert('System error !!');
            } else {
                alert('System error');
            }
        });
    }

    function drawLocation(data) {
        let length = data.length;
        for (let i = 0; i < length; i++) {
            //lokasi unit
            let unitMarker = new google.maps.Marker({
                position: new google.maps.LatLng(data[i].latitude, data[i].longitude),
                map: map,
                icon: '{{ asset("images") }}/fingerprint3.png',
                title: "Lokasi absensi " + data[i].nama,
                label: {
                    text: 'Absensi ' + data[i].nama,
                    color: 'white',
                }
            });

            //radius lokasi
            let sunCircle = {
                strokeColor: "#c3fc49",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#c3fc49",
                fillOpacity: 0.35,
                map: map,
                center: new google.maps.LatLng(data[i].latitude, data[i].longitude),
                radius: data[i].radius // in meters
            };
            cityCircle = new google.maps.Circle(sunCircle);
            cityCircle.bindTo('center', unitMarker, 'position');
        }
    }
</script>
@endsection