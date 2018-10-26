<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo getSetting("APP_NAME"); ?></title>
  <link rel="shortcut icon" href="<?php echo asset_url(); ?>/assets/images/pintech.png">
  <!-- Global stylesheets -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/core.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/components.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/colors.css" rel="stylesheet" type="text/css">

  <link href="<?php echo asset_url(); ?>/assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo asset_url(); ?>/assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
  <!-- /global stylesheets -->

  <style type="text/css">
    .custom > li > a:hover {
      background-color:#378a3b;
    }
    .dropdown-menu>li>a:hover, .dropdown-menu>li>a:focus {
        text-decoration: none;
        color: #333;
        background-color: #940000 !important;
    }
  </style>

  <!-- Core JS files -->
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/loaders/pace.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/loaders/blockui.min.js"></script>
  <!-- /core JS files -->

  <!-- Theme JS files -->  
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/switchery.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/uniform.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/ui/moment/moment.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery_ui/interactions.min.js"></script>  
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery_ui/touch.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/selects/select2.min.js"></script>
 
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/app.js"></script>
  
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/jquery.form.js"></script>

  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/autoNumeric.js"></script>
  <!-- /theme JS files -->

  <!-- Toastr -->
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/toastr/toastr.js"></script>
  <link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>/assets/js/toastr/toastr.css" />     

  <!-- sweet alert-->
  <script src="<?php echo asset_url(); ?>/assets/js/sweetalert/sweetalert.min.js"></script>
  <link rel="stylesheet" href="<?php echo asset_url(); ?>/assets/js/sweetalert/sweetalert.css">

  <!-- datepicker -->
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery_ui/datepicker.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/core/libraries/jquery_ui/effects.min.js"></script>

  <!--pick-a-time -->
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.time.js"></script>  
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.date.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/legacy.js"></script>

  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/daterangepicker.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/anytime.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/picker_date.js"></script>

  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/validation/validate.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/inputs/touchspin.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/selects/select2.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/switch.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/switchery.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/styling/uniform.min.js"></script>

  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/validation/validate.min.js"></script>
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/form_validation.js"></script>

  <!-- tags input -->
  <script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/forms/tags/tagsinput.min.js"></script>

  <script type="text/javascript">   
    var gModalContactCallback = "";

    $(document).ready(function() {
      toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-full-width",
        "onclick": null,
        "showDuration": "15000",
        "hideDuration": "15000",
        "timeOut": "15000",
        "extendedTimeOut": "15000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
      }

      $('.bootstrap-select').selectpicker();

      $(".styled").uniform({
        radioClass: 'choice'
      });

      //autonumeric
      $(".numeric-input").autoNumeric({aSep: '.', aDec: ',', aSign: 'Rp. '});

      //console.log("document ready");
    });

    function isNumeric(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }
    
    function isEmail(elem){
      var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
      if(elem.value.match(emailExp)){
        //alert('isEmail : true');
        return true;
      }
      else{
        elem.focus();
        //alert('isEmail : false');
        return false;
      }
    }
    
    function getResultStatus(result) {
      var resultStatus = "";
      var data = JSON.parse(result);
      resultStatus = data["STATUS"];
      
      return resultStatus;
    }

    function getResultMessage(result) {
      var resultMessage = "";
      var data = JSON.parse(result);
      resultMessage = data["MESSAGE"];
      
      return resultMessage;     
    }
       
    function formatDate(d) {
      var dd = d.getDate();
      if ( dd < 10 ) dd = '0' + dd;

      var mm = d.getMonth()+1;
      if ( mm < 10 ) mm = '0' + mm;

      //var yy = d.getFullYear() % 100;
      //if ( yy < 10 ) yy = '0' + yy;
      var yy = d.getFullYear();

      return  mm +'/'+ dd + '/'+ yy;
    }
    
    function formatDate_DB(d) {
      var dd = d.getDate();
      if ( dd < 10 ) dd = '0' + dd;

      var mm = d.getMonth()+1;
      if ( mm < 10 ) mm = '0' + mm;

      var yy = d.getFullYear();

      return  yy +'-'+ mm + '-'+ dd;
    }
    
    function dateYMD(dateDMY) {
      var hasil = "0000-00-00";
      var kompTgl = dateDMY.split("-");
      var bln = kompTgl[1];
      if(bln.toUpperCase() == "JAN")                                bln = "01";
      if(bln.toUpperCase() == "FEB" || bln.toUpperCase() == "PEB")  bln = "02";
      if(bln.toUpperCase() == "MAR")                                bln = "03";
      if(bln.toUpperCase() == "APR")                                bln = "04";
      if(bln.toUpperCase() == "MAY" || bln.toUpperCase() == "MEI")  bln = "05";
      if(bln.toUpperCase() == "JUN")                                bln = "06";
      if(bln.toUpperCase() == "JUL")                                bln = "07";
      if(bln.toUpperCase() == "AGS" || bln.toUpperCase() == "AUG")  bln = "08";
      if(bln.toUpperCase() == "SEP")                                bln = "09";
      if(bln.toUpperCase() == "OKT" || bln.toUpperCase() == "OCT")  bln = "10";
      if(bln.toUpperCase() == "NOV")                                bln = "11";
      if(bln.toUpperCase() == "DES" || bln.toUpperCase() == "DEC")  bln = "12";

      if(kompTgl[0].length == 4) { //format YYYY-MM-DD (walau seharusnya DD-MM-YYYY)
        var tgl = kompTgl[2];
        var thn = kompTgl[0];
      }
      else { //DD-MM-YYYY
        var tgl = kompTgl[0];
        var thn = kompTgl[2];
        if(thn.length == 2)  thn = "20" + thn; 
      }

      hasil = thn + "-" + bln + "-" + tgl;
      return hasil;
    } 

    //UPDATE 11-Mar-15
    Date.prototype.yyyymmdd = function() {         
      var yyyy = this.getFullYear().toString();
      var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based         
      var dd  = this.getDate().toString();             
                              
      return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
    }; 

    function removeDuplicates(arr) {
      var obj = {};
      for (var i = 0; i < arr.length; i++) {
        obj[arr[i]] = true;
      }
      arr = [];
      for (var key in obj) {
        arr.push(key);
      }
      return arr;
    }
  </script>   

  <!-- iOS overlay -->
  <script src="<?php echo asset_url(); ?>/assets/js/overlay/iosOverlay.js"></script>
  <script src="<?php echo asset_url(); ?>/assets/js/overlay/spin.min.js"></script>
  <link rel="stylesheet" href="<?php echo asset_url(); ?>/assets/js/overlay/iosOverlay.css">
  <script src="<?php echo asset_url(); ?>/assets/js/overlay/modernizr-2.0.6.min.js"></script>
  <script type="text/javascript">
    function createOverlay(screenText) {
      var target = document.createElement("div");
      document.body.appendChild(target);
      var opts = {
        lines: 13, // The number of lines to draw
        length: 11, // The length of each line
        width: 5, // The line thickness
        radius: 17, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        color: '#FFF', // #rgb or #rrggbb
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: 'auto', // Top position relative to parent in px
        left: 'auto' // Left position relative to parent in px
      };        
      var spinner = new Spinner(opts).spin(target);
      gOverlay = iosOverlay({
        text: screenText,
        /*duration: 2e3,*/
        spinner: spinner
      });
    }

    var gOverlay;
  </script> 

  <script type="text/javascript">
    <?php if (!$ctlUserData->U_GANTIPASS): ?>
    $(document).ready(function(){
        swal({
          title: "<strong>Perhatian!</strong>",
          type: "warning",
          text: "<p>Password masih default, <br />mohon segera diganti demi keamanan, <br /><a href='<?= asset_url() ?>/user/change-password'>Ganti Password</a></p>",
          html: true
        }, function () {
            window.location = "<?= asset_url() ?>/user/change-password";
        });
    });
    <?php endif ?>
    function saveContact() {
      var ctType = $("#ctType").val();
      var ctName = $("#ctName").val();
      var ctPhone = $("#ctPhone").val();
      var ctEmail = $("#ctEmail").val();

      if(ctType != "" && ctName != "") {
        createOverlay("Processing...");
        $.ajax({  
          type  : "POST",
          url   : "<?php echo asset_url(); ?>/contacts",
          data  : "ctType=" + encodeURI(ctType) + "&ctName=" + encodeURI(ctName) + "&ctEmail=" + encodeURI(ctEmail) + "&ctPhone=" + encodeURI(ctPhone),
          success : function(result) { 
            gOverlay.hide();
            var data = JSON.parse(result);

            if(data["STATUS"] == "SUCCESS") {            
              toastr.success(data["MESSAGE"]);
              if(gModalContactCallback != "") {                
                setTimeout(function(){ 
                  window.location = "<?php echo asset_url(); ?>/" + gModalContactCallback;
                }, 100);
              }
              else {
                $("#mdlNewContact").modal("hide");
              }
            }
            else {
              //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
              toastr.error(data["MESSAGE"]);
              /*
              swal({
                title: "GAGAL",
                text: data["MESSAGE"],
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "OK",
                closeOnConfirm: false,
                html: true
              },
              function(){
                setTimeout(function(){ 
                  window.location = "<?php echo asset_url(); ?>/administrator/pricing";
                }, 500);              
              });
              */
            }
          },
          error : function(error) {   
            gOverlay.hide();
            alert("Gangguan pada server/jaringan\r\n" + error);
          }
        });
      }    
    }
  </script>
</head>

<body>
  <!-- Main navbar -->
  <div class="navbar navbar-inverse" style="background-color: #bb0a0a">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><strong><?php echo getSetting("APP_NAME"); ?></strong></a>

      <ul class="nav navbar-nav visible-xs-block">
        <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
        <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
      </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
      <ul class="nav navbar-nav">
        <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3" style="color: white;"></i></a></li>
      </ul>

      <!--<p class="navbar-text"><span class="label bg-success-400">Online</span></p>-->

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown dropdown-user">
          <a class="dropdown-toggle" data-toggle="dropdown">
            <!--<img src="<?php echo asset_url(); ?>/assets/images/logo.png" alt="">-->
            <span id="namaKlien" style="color: #fff"><strong><?php echo $ctlUserData->{"U_NAMA"}; ?></strong></span>
            <i class="caret"></i>
          </a>
          <ul class="dropdown-menu dropdown" style="background-color: #BB0A0A;">
            <li><a style="color: white;" href="<?php echo asset_url(); ?>/profil"><i class="icon-profile" style="color: white"></i> Profil</a></li>
            <li class="divider"></li>
            <li><a style="color: white" href="<?php echo asset_url(); ?>/logout"><i class="icon-switch2" style="color: white"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>

  <!-- Page container -->
  <div class="page-container">
    <div class="page-content">
      <!-- Main sidebar -->
      <div class="sidebar sidebar-main" style="background-color: #bb0a0a !important">
        <div class="sidebar-content">
          <!-- User menu -->
          <?php
          $userData = DB::table("coll_user")->where("U_ID", Session::get("SESSION_USER_ID"))->first();
          if($userData->{"U_GROUP_ROLE"} != "GR_ADMINISTRATOR") {
            $prshData = DB::table("coll_perusahaan")->where("PRSH_ID", $userData->{"PRSH_ID"})->first();
            if($prshData->{"PRSH_IMG_PATH"} == "" || $prshData->{"PRSH_IMG_PATH"} == "-") {
              $logo = asset_url()."/assets/images/placeholder.jpg";
            }
            else {
              $logo = asset_url()."/".$prshData->{"PRSH_IMG_PATH"};
            }
            ?>
            <div class="sidebar-user" style="background-color: #FAF9F5 !important">
              <div class="category-content">
                <div class="media">
                  <a href="#" class="media-left"><img src="<?php echo $logo; ?>" class="img-circle img-sm" alt="<?php echo Session::get("SESSION_COMPANY_NAME"); ?>"></a>
                  <div class="media-body" style="color: #000">
                    <span class="media-heading text-semibold"><?php echo Session::get("SESSION_COMPANY_NAME"); ?></span>
                    <div class="text-size-mini text-muted" style="color: #000">
                      <!--i class="icon-pin text-size-small"></i--><?php echo Session::get("SESSION_COMPANY_ADDRESS")."<br>".Session::get("SESSION_COMPANY_CITY"); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          } else {
            $role = 'GR_ADMINISTRATOR';
            $prshData = DB::table("coll_user")->where("U_GROUP_ROLE", $role)->first();
            //if($prshData->{"PRSH_IMG_PATH"} == "" || $prshData->{"PRSH_IMG_PATH"} == "-") {
              $logo = asset_url()."/assets/images/pintech.png";
            //}
            ?>
            <div class="sidebar-user" style="background-color: #FAF9F5 !important">
              <div class="category-content">
                <div class="media">
                  <a href="#" class="media-left"><img src="<?php echo $logo; ?>" class="img-circle img-sm" width="500px;" alt="<?php echo Session::get("SESSION_COMPANY_NAME"); ?>"></a>
                  <div class="media-body" style="color: #000">
                    <span class="media-heading text-semibold">Administrator Page</span>
                    <div class="text-size-mini text-muted" style="color: #000">
                      Pintech Mobile Apps
                    </div>
                  </div>
                </div>
              </div>
            </div>
         <?php }
          ?>          
          <!-- Main navigation -->
          <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding" style="color: white;">
              <ul class="navigation navigation-main navigation-accordion">
                <?php
                if($ctlUserData->{"U_GROUP_ROLE"} == "GR_SUPERVISOR") {
                  ?>
                  <li class="navigation-header" style="color: white"><span>COLLECTION</span> <i class="icon-menu" title="Collection"></i></li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollDashboard") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/dashboard"><i class="icon-meter-fast"  style="color: white"></i> 
                      <span  style="color: white">Dashboard</span>                     
                    </a>
                  </li>               
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollData") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/collection/collector"><i class=" icon-users4"  style="color: white"></i> 
                      <span  style="color: white">Data Collector</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollJadwal") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/collection/jadwal-penagihan"><i class=" icon-calendar2"  style="color: white"></i> 
                      <span  style="color: white">Jadwal Penagihan</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollTabungan") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/collection/tabungan"><i class=" icon-calendar2"  style="color: white"></i> 
                      <span  style="color: white">Tabungan</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollMonitoring") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/collection/monitoring"><i class=" icon-location4"  style="color: white"></i> 
                      <span  style="color: white">Monitoring</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollLaporan") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/collection/laporan"><i class="icon-clipboard5"  style="color: white"></i> 
                      <span  style="color: white">Laporan</span>                     
                    </a>
                  </li>
                  <?php
                }
                ?>

                <li class="navigation-header"  style="color: white"><span>General</span> <i class="icon-menu" title="General"></i></li>
                <?php
                if($ctlUserData->{"U_GROUP_ROLE"} == "GR_SUPERVISOR") {
                  ?>                  
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mSettings") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/settings"><i class="icon-wrench3"  style="color: white"></i> 
                      <span  style="color: white">Pengaturan</span>                     
                    </a>
                  </li>                  
                  <?php
                }
                
                if($ctlUserData->{"U_GROUP_ROLE"} == "GR_ADMINISTRATOR") {
                  ?>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollDashboard") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/dashboard/admin"><i class="icon-meter-fast"  style="color: white"></i> 
                      <span  style="color: white">Dashboard</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCompMgmt") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/company"><i class="icon-office"  style="color: white"></i> 
                      <span  style="color: white">Manajemen Perusahaan</span>                     
                    </a>
                  </li>
                  <?php if($ctlUserData->{"U_SUPERUSER"} === "S_USER") { ?>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mUserMgmt") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/admin"><i class="icon-users"  style="color: white"></i> 
                      <span  style="color: white">Manajemen User</span>                     
                    </a>
                  </li>
                  <?php } else { ?>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mUserMgmt") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/user"><i class="icon-users"  style="color: white"></i> 
                      <span  style="color: white">Manajemen User</span>                     
                    </a>
                  </li>
                  <?php } ?>

                  <li class="navigation-header"  style="color: white"><span>COLLECTION INFORMATION</span> <i class="icon-menu" title="General"></i></li> 

                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollJadwal") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/admin/collection/jadwal"><i class=" icon-calendar2"  style="color: white"></i> 
                      <span  style="color: white">List Data Penagihan</span>                     
                    </a>
                  </li>

                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollTabungan") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/admin/collection/tabungan"><i class=" icon-calendar2"  style="color: white"></i> 
                      <span  style="color: white">List Data Tabungan</span>                     
                    </a>
                  </li>

                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollMonitoring") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/admin/collection/monitoring"><i class=" icon-location4"  style="color: white"></i> 
                      <span  style="color: white">Monitoring Collector</span>                     
                    </a>
                  </li>
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mCollLaporan") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/admin/laporan"><i class="icon-clipboard5"  style="color: white"></i> 
                      <span  style="color: white">Laporan Perusahaan</span>                     
                    </a>
                  </li>  
                  <li class="navigation-header"  style="color: white"><span>SETTING INFORMATION</span> <i class="icon-menu" title="General"></i></li>              
                  <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mAdmSettings") echo "class='active'"; ?>>
                    <a href="<?php echo asset_url(); ?>/adm/settings"><i class="icon-cogs"  style="color: white"></i> 
                      <span  style="color: white">Pengaturan</span>                     
                    </a>
                  </li>
                  <?php
                }
                ?>                                                
                <li <?php if(isset($ctlNavMenu) && $ctlNavMenu == "mProfil") echo "class='active'"; ?>>
                  <a href="<?php echo asset_url(); ?>/profil"><i class=" icon-profile"  style="color: white"></i> 
                    <span  style="color: white">Profil</span>                     
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="content-wrapper" id="moduleContent" style="background-color:#FAF9F5;">
        @yield('content')
      </div>

      <div id="mdlNewContact" class="modal fade">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h6 class="modal-title"><i class="icon-vcard"></i> &nbsp; Create New Contact</h6>
            </div>

            <div class="modal-body">
              <!--
              <h6 class="text-semibold">Text in a modal</h6>
              <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

              <hr>

              <h6 class="text-semibold">Another paragraph</h6>
              <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
              <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
              -->
              <form class="form-horizontal" method="post" id="formContact" name="formContact" action="">    
                <div class="form-group">
                  <label class="col-lg-4 control-label text-semibold">Contact Type</label>
                  <div class="col-lg-8">
                    <select class="form-control" id="ctType" name="ctType">
                      <option value="">-- Contact type --</option>
                      <?php
                      if(isset($ctlRefContactType) && count($ctlRefContactType) > 0) {
                        foreach ($ctlRefContactType as $aData) {
                          ?>
                          <option value="<?php echo $aData->{"R_ID"}; ?>"><?php echo $aData->{"R_INFO"}; ?></option>
                          <?php
                        }
                      }
                      ?>                
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-lg-4 control-label text-semibold">Name</label>
                  <div class="col-lg-8">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="icon-vcard"></i></span>
                      <input type="text" id="ctName" class="form-control" placeholder="Contact name...">
                    </div>
                  </div>            
                </div>

                <div class="form-group">
                  <label class="col-lg-4 control-label text-semibold">Phone</label>
                  <div class="col-lg-8">
                    <div class="input-group">
                      <span class="input-group-addon"><i class=" icon-phone2"></i></span>
                      <input type="text" id="ctPhone" class="form-control" placeholder="Contact phone...">
                    </div>
                  </div>            
                </div>

                <div class="form-group">
                  <label class="col-lg-4 control-label text-semibold">Email</label>
                  <div class="col-lg-8">
                    <div class="input-group">
                      <span class="input-group-addon"><i class=" icon-mention"></i></span>
                      <input type="text" id="ctEmail" class="form-control" placeholder="Contact email...">
                    </div>
                  </div>            
                </div>
              </form>
            </div>

            <div class="modal-footer">
              <!--button type="button" class="btn btn-link" data-dismiss="modal"><i class=" icon-cross3 position-left"></i> Close </button-->
              <!--<button type="button" class="btn btn-primary"><i class="icon-checkmark2 position-left"></i> Save</button>-->
              <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Close </button>
              <button type="button" class="btn btn-primary btn-labeled btn-xs" onClick="saveContact();"><b><i class="icon-checkmark2"></i></b> Save </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
