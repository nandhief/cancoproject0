@extends('dashboard.layout-dashboard')
@section('content')
<style>
    #map-canvas {
        height: 75vh;
    }
</style>
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Marketing</span></h4>
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?= asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
            <li><i class="icon-menu position-left"></i> Collection</a></li>
            <li class="active"><i class="icon-briefcase"></i> Marketing</li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12"><div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Monitor Kolektor Penagihan</h5>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Jadwal Penagihan</label>
                                <select name="" id="jadwal" class="form-control">
                                    <option value="">--PILIH--</option>
                                    <?php foreach ($data as $key => $value) : ?>
                                        <option value="<?= $value->BU_ID ?>">[<?= $value->BU_TGL ?>] <?= $value->PRSH_NAMA ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="jadwal"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Data Kolektor</label>
                                <select name="" id="collector" class="form-control">
                                    <option value="">--PILIH--</option>
                                </select>
                                <div class="collector"></div>
                            </div>
                        </div>
                    </div>
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var icon = [
        '<?= asset_url() ?>/assets/images/collector-start-0.png',
        '<?= asset_url() ?>/assets/images/collector-location-0.png'
    ]
    var gMap, gDirectionService, gDirectionRender, gMarker, gMarkerArray = [], infoWindow, waypoints, info
    function initialize() {
        gMap = new google.maps.Map(document.getElementById('map-canvas'), {
            center: {
                lat: <?= getSetting("DEFAULT_MAP_LAT") ?>,
                lng: <?= getSetting("DEFAULT_MAP_LNG") ?>
            },
            zoom: 16,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        })
        gDirectionService = new google.maps.DirectionsService()
        gDirectionRender = new google.maps.DirectionsRenderer({
            suppressMarkers: true
        })
    }
    function bindInfoWindow(marker, html) {
        info = new google.maps.InfoWindow()
        google.maps.event.addListener(marker, 'click', function() {
            info.setContent(html);
            info.open(gMap, marker);
        });
    }
    function displayRoute(gDirectionService, gDirectionRender, dataMaps) {
        gDirectionService.route({
            origin: dataMaps.positions[0],
            destination: dataMaps.positions[dataMaps.positions.length - 1],
            waypoints: dataMaps.waypoints,
            travelMode: 'DRIVING'
        }, function (response, status) {
            if (status === 'OK') {
                gDirectionRender.setDirections(response)
            }
        })
    }
    function resetMarker() {
        for (let i = 0; i < gMarkerArray.length; i++) {
            gMarkerArray[i].setMap(null);
        }
        gMarkerArray = []
    }
    $('#jadwal').change(function () {
        if ($(this).val().length > 0) {
            $.get('<?= asset_url() ?>/direksi/monitor/jadwal/' + $(this).val(), function (response) {
                $('#collector').empty()
                $('#collector').append('<option value="" data-id="">--PILIH--</option>')
                $.each(response.PAYLOAD, function (key, value) {
                    $('#collector').append('<option value="' + value.U_ID + '" data-id="' + value.BU_ID + '">'+value.U_NAMA+'</option>')
                })
            })
        }
    })
    $('#collector').change(function () {
        resetMarker()
        if ($(this).val().length > 0) {
            $.post('<?= asset_url() ?>/direksi/monitor/route', {
                bu_id: $(this).find(':selected').data('id'),
                collect: $(this).val()
            },function (response) {
                gDirectionRender.setMap(gMap)
                waypoints = []
                positions = []
                response.PAYLOAD.forEach((data, key) => {
                    var position ={ lat: parseFloat(data.BUD_LOKASI_LAT), lng: parseFloat(data.BUD_LOKASI_LNG) }
                    gMarker = new google.maps.Marker({
                        position: position,
                        icon: (key == 0 ? icon[0] : icon[1]),
                        map: gMap
                    })
                    var status = ''
                    switch (data.BUD_STATUS) {
                        case 'ST_BAYAR_NON_TARGET':
                        case 'ST_BAYAR':
                            status = 'Bayar'
                            break;
                        case 'ST_BAYAR_PARSIAL_NON_TARGET':
                        case 'ST_BAYAR_PARSIAL':
                            status = 'Bayar Sebagian'
                            break;
                        case 'ST_TIDAK_BAYAR_NON_TARGET':
                        case 'ST_TIDAK_BAYAR':
                            status = 'Tidak Bayar'
                            break;
                        case 'ST_TIDAK_DITEMUKAN_NON_TARGET':
                        case 'ST_TIDAK_DITEMUKAN':
                            status = 'Tidak Bertemu'
                            break;
                    }
                    info = '<b>' + data.BUD_CUST_NAMA + '</b><br>'
                    info += data.U_NAMA + '/' + data.U_ID + '<br>'
                    info += status + ' @' + data.BUD_STATUS_WAKTU
                    bindInfoWindow(gMarker, info)
                    waypoints.push({
                        location: position,
                        stopover: true
                    })
                    positions.push(position)
                    gMarkerArray.push(gMarker)
                })
                var dataMaps = { positions, waypoints }
                displayRoute(gDirectionService, gDirectionRender, dataMaps)
            })
        }
    })
</script>
<script src="https://maps.google.com/maps/api/js?key=<?= getSetting('GOOGLE_MAPS_API_KEY'); ?>&callback=initialize" async defer></script>
@endsection
