@extends('dashboard.layout-dashboard')

@section('content')

<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Laporan</span></h4>
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
      <li class="active"><i class="icon-clipboard5"></i> Laporan</li>
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
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Collecting Bulanan</h5>
      <!--
      <div class="heading-elements">
        <ul class="icons-list">  
          <li><a href="<?php echo asset_url(); ?>/assets/documents/sample_collector.xlsx"><i class="  icon-download4"></i> Unduh File Contoh</a></li>
          <li><a data-action="collapse"></a></li>
        </ul>
      </div>
      -->
      <!--
      <div class="heading-elements">
        <ul class="icons-list">
          <li><a data-action="collapse"></a></li>
          <li><a data-action="reload"></a></li>
          <li><a data-action="close"></a></li>
        </ul>
      </div>
      //-->
    </div>

    <div class="panel-body">
      <p class="content-group">
        Laporan yang berisi data dan status collecting yang dilakukan petugas lapangan.
      </p>

      <form class="form-horizontal" method="post">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Bulan</label>
          <div class="col-lg-4">
            <select class="form-control" id="filterPeriode" onChange="reloadPage()">
              <option value="<?php echo (date("Y")-1); ?>-12">Desember <?php echo date("Y")-1; ?></option>
              <option value="<?php echo date("Y"); ?>-01">Januari <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-02">Februari <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-03">Maret <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-04">April <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-05">Mei <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-06">Juni <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-07">Juli <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-08">Agustus <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-09">September <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-10">Oktober <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-11">November <?php echo date("Y"); ?></option>
              <option value="<?php echo date("Y"); ?>-12">Desember <?php echo date("Y"); ?></option>
            </select>
            <script type="text/javascript">
              $("#filterPeriode").val("<?php echo $ctlFilterYear; ?>-<?php echo $ctlFilterMonth; ?>");
            </script>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>            
          <div class="col-lg-2">
            <button type="button" class="btn bg-green btn-block btn-labeled" onClick="return viewReport('RPT_COLLECTING')"><b><i class="fa fa-list"></i></b> Lihat Data</button>
            <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="downloadReport('RPT_COLLECTING')"><b><i class="fa fa-download"></i></b> Unduh File</button>
          </div>            
        </div>
      </form>
    </div>
  </div>

  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Collecting By Collector</h5>
    </div>

    <div class="panel-body">
      <form class="form-horizontal" method="post">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Collector</label>
          <div class="col-lg-4">
            <select class="form-control" name="laporanCollector" id="laporanCollector">
              <option value="ALL">[Semua kolektor]</option>
              <?php
              if(isset($ctlCollector) && count($ctlCollector) > 0) {
                foreach ($ctlCollector as $aData) {
                  ?>
                  <option value="<?php echo $aData->{"U_ID"}; ?>"><?php echo $aData->{"U_NAMA"}; ?></option>
                  <?php
                }
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Tanggal Awal</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-calendar"></i></span>
              <input type="text" id="laporanTglAwal" class="form-control pickadate" placeholder="Tanggal awal&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>            
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Tanggal Akhir</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-calendar"></i></span>
              <input type="text" id="laporanTglAkhir" class="form-control pickadate" placeholder="Tanggal akhir&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>            
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>            
          <div class="col-lg-2">
            <button type="button" class="btn bg-green btn-block btn-labeled" onClick="viewReport('RPT_COLLECTING_QUERY');"><b><i class="fa fa-download"></i></b> Lihat Data</button>
            <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="downloadReport('RPT_COLLECTING_QUERY');"><b><i class="fa fa-download"></i></b> Unduh File</button>
          </div>            
        </div>
      </form>
    </div>
  </div>
</div>  

<div class="footer text-muted"></div>

<!-- Theme JS files -->
<!--pick-a-time -->
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.date.js"></script>

<script type="text/javascript">
  // Select with search
  //$('.select').select2();
  $('.pickadate').pickadate({
    format: 'dd-mm-yyyy',
    formatSubmit: 'yyyy-mm-dd'
  });
</script>

<script type="text/javascript">
  <?php
  if(Session::has("ctlError")) {
    ?>
    toastr.error("<?php echo Session::get('ctlError'); ?>");
    <?php
  }
  ?>

  function reloadPage() {
    var filterPeriode = $("#filterPeriode").val();
    window.location = "<?php echo asset_url(); ?>/collection/laporan?periode=" + filterPeriode;
  }

  function downloadReport(rptType) {
    if(rptType == "RPT_COLLECTING") {
      var filterPeriode = $("#filterPeriode").val();
      window.location = "<?php echo asset_url(); ?>/collection/laporan/download?tipe=" + rptType + "&periode=" + filterPeriode;
    }
    if(rptType == "RPT_COLLECTING_QUERY") {
      var laporanCollector = $("#laporanCollector").val();
      var laporanTglAwal = $('#laporanTglAwal').pickadate('picker').get('select', 'yyyy-mm-dd');
      var laporanTglAkhir = $('#laporanTglAkhir').pickadate('picker').get('select', 'yyyy-mm-dd');
      //alert("collector : " + laporanCollector + "\nawal : " + laporanTglAwal + "\nakhir : " + laporanTglAkhir);
      window.location = "<?php echo asset_url(); ?>/collection/laporan/download?tipe=" + rptType + "&collector=" + laporanCollector + "&awal=" + laporanTglAwal + "&akhir=" + laporanTglAkhir;
    }
  }
  function viewReport(rptType) {
    if(rptType == "RPT_COLLECTING") {
      var filterPeriode = $("#filterPeriode").val();
      window.location = "<?php echo asset_url(); ?>/collection/laporan/view?tipe=" + rptType + "&periode=" + filterPeriode;
    }
    if(rptType == "RPT_COLLECTING_QUERY") {
      var laporanCollector = $("#laporanCollector").val();
      var laporanTglAwal = $('#laporanTglAwal').pickadate('picker').get('select', 'yyyy-mm-dd');
      var laporanTglAkhir = $('#laporanTglAkhir').pickadate('picker').get('select', 'yyyy-mm-dd');
      //alert("collector : " + laporanCollector + "\nawal : " + laporanTglAwal + "\nakhir : " + laporanTglAkhir);
      window.location = "<?php echo asset_url(); ?>/collection/laporan/view?tipe=" + rptType + "&collector=" + laporanCollector + "&awal=" + laporanTglAwal + "&akhir=" + laporanTglAkhir;
    }
  }
</script>
@stop