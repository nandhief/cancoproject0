@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Monitoring</span></h4>
    </div>
    <!--
    <div class="heading-elements">
      <div class="heading-btn-group">
        <a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
        <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
        <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
      </div>
    </div>
    //-->
  </div>

  <div class="breadcrumb-line">
    <ul class="breadcrumb">
      <li><a href="<?php echo asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
      <li><i class="icon-menu position-left"></i> Collection</a></li>
      <li class="active"><i class="icon-location4"></i> Monitoring</li>
    </ul>
    <!--
    <ul class="breadcrumb-elements">
      <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="icon-download position-left"></i>
          Download
          <span class="caret"></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#"><i class=" icon-file-excel"></i> Laporan Penagihan</a></li>
          <li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
          <li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
          <li class="divider"></li>
          <li><a href="#"><i class="icon-gear"></i> All settings</a></li>
        </ul>
      </li>
    </ul>
    //-->
  </div>
</div>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-flat">
        <div class="panel-heading">
          <h5 class="panel-title" style="color: #bb0a0a !important">Lokasi Check-In Collector</h5>
        </div>

        <div class="panel-body">
          <div class="form-group">
            <div class="row">
              <div class="col-sm-8">
                <label>Pilih jadwal penagihan</label>
                <select class="form-control" id="jadwal" onChange="reloadCollectionData()">
                  <?php
                  if(isset($ctlJadwal) && count($ctlJadwal) > 0) {
                    foreach ($ctlJadwal as $aData) {
                      ?>
                      <option value="<?php echo $aData->{"BU_ID"}; ?>"><?php echo tglIndo($aData->{"BU_TGL"},"SHORT"); ?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-sm-8">
                <label>Pilih data penagihan</label>
                <select class="js-example-basic-single" id="collector" onChange="gotoMarker()"></select>
                <script type="text/javascript">
                $(document).ready(function() {
                  $('.js-example-basic-single').select2();
              });
              </script>
              </div>
              <br>
              <br>
            </div>
            <br>
            <input type="submit" class="btn btn-danger" value="Refresh Page" onclick="document.location.reload(true)">
                <script type="text/javascript">
                  function reloadPage() {
                    location.reload()
                  }
                </script>
          </div>

          <div class="map-container" id="map-canvas"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer text-muted"></div>
</div>

<div class="footer text-muted"></div>

<script type="text/javascript">
  //<![CDATA[
  var gMapsLoaded = false;

  var customIcons = {
    0: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-0.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    1: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-1.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    2: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-2.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    3: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-3.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    4: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-4.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    5: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-5.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    6: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-6.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    },
    7: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-location-7.png'/*,
      shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'*/
    }
  };

  var checkInStartIcon = {
    0: {
      icon: '<?php echo asset_url(); ?>/assets/images/collector-start-0.png'
    }
  };

  var gMarkersArray = [];
  var gMarkersCollectorArray = [];
  var gMap;
  var gInfoWindow;
  var gDirectionsService;
  var gDirectionsDisplay;

  window.gMapsCallback = function(){
    gMapsLoaded = true;
    $(window).trigger('gMapsLoaded');
    console.log("gMapsCallback");
  }
  window.loadGoogleMaps = function(){
    console.log("loadGoogleMaps");
    if(gMapsLoaded) return window.gMapsCallback();
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type","text/javascript");
    script_tag.setAttribute("src","https://maps.google.com/maps/api/js?key=<?php echo getSetting('GOOGLE_MAPS_API_KEY'); ?>&callback=gMapsCallback");
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
  }
      
  function initialize(){
    console.log("initialize");
    infoWindow = new google.maps.InfoWindow;
    var centerLat = <?php echo getSetting("DEFAULT_MAP_LAT"); ?>;
    var centerLong = <?php echo getSetting("DEFAULT_MAP_LNG"); ?>;
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
    // your first call to get & process inital data    
    locateCollector("<?php echo asset_url(); ?>/collection/monitoring/position", processXML);
  }

  function processXML(data) {
    //console.log("processXML");
    var xml = data.responseXML;
    var markers = xml.documentElement.getElementsByTagName("marker");
    if(markers.length > 0) {
      var optCollector = "<option value=''>-- Pilih data penagihan --</option>";  
    }
    else {
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
   
        //optCollector += "<option value='" + i + "'>" + markers[i].getAttribute("CUST_NAMA") + " - Collector : " + markers[i].getAttribute("COLL_NAMA") + "/" + markers[i].getAttribute("COLL_ID") + "</option>";
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
        }
        else {
          if(lastCollId != markers[i].getAttribute("COLL_ID")) {
            lastMarker = marker;
            //console.log("Collector ganti dari " + lastCollId + " ke  " + markers[i].getAttribute("COLL_ID") + " - marker = " + lastMarker.getPosition());
            lastCollId = markers[i].getAttribute("COLL_ID");
            uniqueCollectorCounter++;
            if(uniqueCollectorCounter > 7)  uniqueCollectorCounter = 0;
          }
          else { //collector masih sama -> draw route
            if(!!lastMarker) {       
              //console.log("Collector masih " + lastCollId + " - lastMarker : " + lastMarker.getPosition() + " - marker : " + marker.getPosition() + " - Cust : " + markers[i].getAttribute("CUST_NAMA"));            
            }
          }
        }

        //var icon = customIcons[type] || {};
        if(type == "collector") {
          var icon = customIcons[uniqueCollectorCounter] || {};          
        }
        if(type == "check-in_start") {
          var icon = checkInStartIcon[0] || {};        
        }

        var marker = new google.maps.Marker({
          map : gMap,
          position : point,
          icon : icon.icon/*,
          shadow : icon.shadow*/
        });
      }

      //store marker object in a new array
      gMarkersArray.push(marker);
      bindInfoWindow(marker, infoWindow, html);      

      gMarkersCollectorArray.push(markers[i].getAttribute("COLL_ID")); //sidekick nya gMarkersArray
    }

    $("#collector").html(optCollector);
    
    setTimeout(function() {
      locateCollector("<?php echo asset_url(); ?>/collection/monitoring/position", processXML);
    }, <?php echo getSetting("MONITORING_INTERVAL_MS"); ?>);
  }

  //clear existing markers from the map
  function resetMarkers(){
    for (var i=0; i<gMarkersArray.length; i++){
      gMarkersArray[i].setMap(null);
    }
    //reset the main marker array for the next call
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
 
  function doNothing() {}

  function gotoMarker() {
    var collector = $("#collector").val();
    if(collector != "") {
      gMap.panTo(gMarkersArray[collector].getPosition());
      //gMap.setCenter(gMarkersArray[collector].getPosition());

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
        } 
        else {
          window.alert("[ERROR]\r\nGoogle Maps error dengan status kesalahan : " + status);
        }
      });
    }
  }
  /*
  function displayRoutes() {
    var directionsService = new google.maps.DirectionsService();
    var renderOptions = { draggable: true };
    var directionDisplay = new google.maps.DirectionsRenderer(renderOptions);

    //set the directions display service to the map
    directionDisplay.setMap(map);
    //set the directions display panel
    //panel is usually just and empty div.  
    //This is where the turn by turn directions appear.
    directionDisplay.setPanel(directionsPanel); 

    //build the waypoints
    //free api allows a max of 9 total stops including the start and end address
    //premier allows a total of 25 stops. 
    var items = ["address 1", "address 2", "address 3"];
    var waypoints = [];
    for (var i = 0; i < items.length; i++) {
      var address = items[i];
      if (address !== "") {
        waypoints.push({
          location: address,
          stopover: true
        });
      }
    }

    //set the starting address and destination address
    var originAddress = "starting address";
    var destinationAddress = "destination address";

    //build directions request
    var request = {
      origin: originAddress,
      destination: destinationAddress,
      waypoints: waypoints, //an array of waypoints
      optimizeWaypoints: true, //set to true if you want google to determine the shortest route or false to use the order specified.
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    //get the route from the directions service
    directionsService.route(request, function (response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionDisplay.setDirections(response);
      }
      else {
        //handle error
      }
    });
  }
  */
  function reloadCollectionData() {
    locateCollector("<?php echo asset_url(); ?>/collection/monitoring/position", processXML);
  }

  $(window).bind('gMapsLoaded', initialize);
  window.loadGoogleMaps();
  //]]>
</script>
@stop