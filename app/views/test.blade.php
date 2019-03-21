<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    TEST
    <script>
    var gMapsLoaded = false;
    var customIcons = {
        0: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-0.png'
        },
        1: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-1.png'
        },
        2: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-2.png'
        },
        3: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-3.png'
        },
        4: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-4.png'
        },
        5: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-5.png'
        },
        6: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-6.png'
        },
        7: {
            icon: '<?= asset_url(); ?>/assets/images/collector-location-7.png'
        }
    };
    var checkInStartIcon = {
        0: {
            icon: '<?= asset_url(); ?>/assets/images/collector-start-0.png'
        }
    };
    var gMarkersArray = [];
    var gMarkersCollectorArray = [];
    var gMap;
    var gInfoWindow;
    var gDirectionsService;
    var gDirectionsDisplay;

    window.gMapsCallback = function() {
        gMapsLoaded = true;
        $(window).trigger('gMapsLoaded');
        console.log("gMapsCallback");
    }

    window.loadGoogleMaps = function() {
        console.log("loadGoogleMaps");
        if(gMapsLoaded) return window.gMapsCallback();
        var script_tag = document.createElement('script');
        script_tag.setAttribute("type","text/javascript");
        script_tag.setAttribute("src","https://maps.google.com/maps/api/js?key=<?= getSetting('GOOGLE_MAPS_API_KEY'); ?>&callback=gMapsCallback");
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    }

    function initialize() {
        console.log("initialize");
        infoWindow = new google.maps.InfoWindow;
        var centerLat = <?= getSetting("DEFAULT_MAP_LAT"); ?>;
        var centerLong = <?= getSetting("DEFAULT_MAP_LNG"); ?>;
        var mapOptions = {
            zoom: 16,
            center: new google.maps.LatLng(centerLat,centerLong),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        gMap = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
        gDirectionsService = new google.maps.DirectionsService;
        gDirectionsDisplay = new google.maps.DirectionsRenderer({
            map: gMap,
            suppressMarkers: true
        });
        locateCollector("<?= asset_url(); ?>/collection/monitoring/position", processXML);
    }

    function processXML(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        if(markers.length > 0) {
            var optCollector = "<option value=''>-- Pilih data penagihan --</option>";
        } else {
            var optCollector = "<option value=''>-- Tidak ada data check-in --</option>";
        }

        resetMarkers();

        var lastCollId = "";
        var lastMarker;
        var uniqueCollectorCounter = 0;

        for(var i=0; i<markers.length; i++) {
            var type = markers[i].getAttribute("TYPE");
            if(type == "collector" || type == "check-in_start") {
                var latPos = parseFloat(markers[i].getAttribute("COLL_POSISI_LAT"));
                var longPos = parseFloat(markers[i].getAttribute("COLL_POSISI_LNG"));

                if(type == "collector")       optCollector += "<option value='" + i + "'>" + markers[i].getAttribute("COLL_NAMA") + "/" + markers[i].getAttribute("COLL_ID") + " - Customer : " + markers[i].getAttribute("CUST_NAMA") + "</option>";
                if(type == "check-in_start")  optCollector += "<option value='" + i + "'>" + markers[i].getAttribute("COLL_NAMA") + "/" + markers[i].getAttribute("COLL_ID") + " - " + markers[i].getAttribute("CUST_NAMA") + "</option>";

                var point = new google.maps.LatLng(latPos,longPos);
                var html = "<b>" + markers[i].getAttribute("CUST_NAMA") + "</b>";
                    html += "<br>" + markers[i].getAttribute("COLL_NAMA") + " /" + markers[i].getAttribute("COLL_ID");
                    html += "<br>" + markers[i].getAttribute("COLL_STATUS_INFO") + " @" + markers[i].getAttribute("COLL_STATUS_WAKTU")
                if(type == "collector") html += "<br>" + markers[i].getAttribute("SELISIH");

                if(lastCollId == "")  {
                    lastCollId = markers[i].getAttribute("COLL_ID");
                    lastMarker = marker;
                    //console.log("1st : lastCollId = " + lastCollId + " - lastMarker = " + lastMarker.getPosition());
                } else {
                    if(lastCollId != markers[i].getAttribute("COLL_ID")) {
                        lastMarker = marker;
                        lastCollId = markers[i].getAttribute("COLL_ID");
                        uniqueCollectorCounter++;
                        if(uniqueCollectorCounter > 7)  uniqueCollectorCounter = 0;
                    } else {
                        if(!!lastMarker) {
                            //console.log("Collector masih " + lastCollId + " - lastMarker : " + lastMarker.getPosition() + " - marker : " + marker.getPosition() + " - Cust : " + markers[i].getAttribute("CUST_NAMA"));
                        }
                    }
                }

                if(type == "collector") {
                    var icon = customIcons[uniqueCollectorCounter] || {};
                }
                if(type == "check-in_start") {
                    var icon = checkInStartIcon[0] || {};
                }

                var marker = new google.maps.Marker({
                    map : gMap,
                    position : point,
                    icon : icon.icon
                });
            }

            gMarkersArray.push(marker);
            bindInfoWindow(marker, infoWindow, html);

            gMarkersCollectorArray.push(markers[i].getAttribute("COLL_ID")); //sidekick nya gMarkersArray
        }

        $("#collector").html(optCollector);

        setTimeout(function() {
            locateCollector("<?= asset_url(); ?>/collection/monitoring/position", processXML);
        }, <?= getSetting("MONITORING_INTERVAL_MS"); ?>);
    }

    function resetMarkers() {
        for (var i=0; i<gMarkersArray.length; i++){
            gMarkersArray[i].setMap(null);
        }
        gMarkersArray = [];

        gMarkersCollectorArray = [];
    }

    function bindInfoWindow(marker, infoWindow, html) {
        google.maps.event.addListener(marker, 'click', function() {
            infoWindow.setContent(html);
            infoWindow.open(gMap, marker);
        });
    }

    function locateCollector(url, callback) {
        var jadwal = $("#jadwal").val();
        url = url + "?jadwal=" + jadwal;
        console.log("locateCollector URL : " + url);
        $("#collector").html("<option value=''>-- Me-refresh data batch upload #" + jadwal + "--</option>");
        var request = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest;
        request.onreadystatechange = function() {
            if(request.readyState == 4) {
                request.onreadystatechange = doNothing;
                callback(request, request.status);
            }
        };

        request.open('GET', url, true);
        request.send(null);
    }

    function doNothing() {
        //
    }

    function gotoMarker() {
        var collector = $("#collector").val();
        if(collector != "") {
        gMap.panTo(gMarkersArray[collector].getPosition());

        var locationArray = [];
        var tmpIdxArray = [];

        var selectedCollector = gMarkersCollectorArray[collector];
        for(var i=0; i<gMarkersCollectorArray.length; i++) {
            if(gMarkersCollectorArray[i] == selectedCollector) {
                tmpIdxArray.push(i);
                locationArray.push(gMarkersArray[i].getPosition());
            }
        }

        var waypointsArray = [];
        for (var i = 0; i < locationArray.length; i++) {
            if (locationArray[i] !== "") {
                waypointsArray.push({
                    location: locationArray[i],
                    stopover: true
                });
            }
        }

        var startLocation = tmpIdxArray[0];
        var endLocation = tmpIdxArray[tmpIdxArray.length-1];
        gDirectionsDisplay.setMap(null);
        gDirectionsService.route({
            origin: gMarkersArray[startLocation].getPosition(),
            destination: gMarkersArray[endLocation].getPosition(),
            waypoints: waypointsArray, //an array of waypoints
            travelMode: google.maps.TravelMode.DRIVING
        }, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                gDirectionsDisplay.setMap(gMap);
                gDirectionsDisplay.setDirections(response);
                //console.log("direction status OK from " + gMarkersArray[markerIdx].getPosition() + " to " + gMarkersArray[nextMarkerIdx].getPosition());
            } else {
                window.alert("[ERROR]\r\nGoogle Maps error dengan status kesalahan : " + status);
            }
        });
    }

    function reloadCollectionData() {
        locateCollector("<?= asset_url(); ?>/collection/monitoring/position", processXML);
    }

    $(window).bind('gMapsLoaded', initialize);
    window.loadGoogleMaps();
    </script>
</body>
</html>
