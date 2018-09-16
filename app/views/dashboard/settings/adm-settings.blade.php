@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Pengaturan</span></h4>
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
      <li><i class="icon-menu position-left"></i> General</a></li>
      <li class="active"><i class="icon-cogs"></i> Pengaturan</li>
    </ul>
    <!--
    <ul class="breadcrumb-elements">
      <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="icon-gear position-left"></i>
          Settings
          <span class="caret"></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
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
  <form class="form-horizontal" id="formData" name="formData" method="post" action="<?php echo asset_url(); ?>/adm/settings">
    <div class="panel panel-flat border-top-primary">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Pengaturan Sistem</h5>
        <div class="heading-elements">
          <ul class="icons-list">
            <li><a data-action="collapse"></a></li>
          </ul>
        </div>
      </div>

      <div class="panel-body">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Nama Aplikasi</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-buysellads"></i></span>
              <input type="text" class="form-control first-input" id="appName" name="appName" placeholder="Nama aplikasi..." value="<?php echo getSetting("APP_NAME"); ?>">
            </div>
          </div>
        </div>

        <!-- <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Prefix</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-code"></i></span>
              <input type="text" class="form-control" id="companyPrefix" name="companyPrefix" placeholder="Kode prefix perusahaan klien..." value="<?php echo getSetting("COMPANY_PREFIX"); ?>">
            </div>
          </div>
        </div> -->

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Google Maps API Key</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-location4"></i></span>
              <input type="text" disabled="" class="form-control" id="googleMapsAPIKey" name="googleMapsAPIKey" placeholder="API key untuk akses Google Maps..." value="<?php echo getSetting("GOOGLE_MAPS_API_KEY"); ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Interval Monitoring Collector</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class=" icon-history"></i></span>
              <input type="text" class="form-control" id="intervalMonitoring" name="intervalMonitoring" placeholder="Durasi jeda monitoring lokasi collector..." value="<?php echo getSetting("MONITORING_INTERVAL_MS"); ?>">
              <span class="input-group-addon">milidetik</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-2">
            <button class="btn bg-blue-theme btn-block btn-labeled" onClick="saveData()"><b><i class="fa fa-check position-right"></i></b> Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="footer text-muted"></div>
</div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/jquery.form.js"></script>
<script type="text/javascript">
  $(function() {

  });

  var gBusy = false;

  $(".first-input").focus();

  var saveData = function() {
    if(!gBusy) {
      gBusy = true;
      //- content action form
      var optFormContentUpdate = {
        beforeSubmit  : function(e) {
          $(".btn").attr("disabled","");
          createOverlay("Mohon Tunggu...");
          return true;
        },
        resetForm : false,
        success : function(result) {
          gBusy = false;
          gOverlay.hide();
          var data = JSON.parse(result);
          $(".btn").removeAttr("disabled");
          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/adm/settings";
            }, 500);
          }
          else {
            //toastr.error(data["MESSAGE"]);
            swal({
              title: "GAGAL",
              text: data["MESSAGE"],
              type: "error",
              showCancelButton: false,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "OK",
              closeOnConfirm: true,
              html: true
            },
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/adm/settings";
              }, 500);
            });
          }
          //$(".btn").removeAttr("disabled");
          //window.location = "<?php echo asset_url(); ?>/adm/settings";
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          window.location = "<?php echo asset_url(); ?>/adm/settings";
        }
      };
      $('#formData').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate);
        // return false to prevent standard browser submit and page navigation
        return false;
      });
    }
  }
</script>
@stop
