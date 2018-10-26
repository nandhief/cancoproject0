@extends('dashboard.layout-dashboard')
@section('content')
<?php  ?>
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Laporan Collecting</span></h4>
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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Penagihan By Perusahaan</h5>
    </div>

    <div class="panel-body">
      <form class="form-horizontal" method="GET" action="<?php echo asset_url(); ?>/admin/laporan/download" id="penagihan">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Perusahaan</label>
          <div class="col-lg-4">
            <select class="js-example-basic-single" name="collector" id="laporanCollector">
              <option value="ALL">[Semua Perusahaan]</option>
              <?php
              if(isset($ctrlPrsh) && count($ctrlPrsh) > 0) {
                foreach ($ctrlPrsh as $aData) {
                  ?>
                  <option value="<?php echo $aData->{"PRSH_ID"}; ?>"><?php echo $aData->{"PRSH_NAMA"}; ?></option>
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
              <input type="text" id="laporanTglAwal" name="tglAwal" class="form-control pickadate" placeholder="Tanggal awal&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Tanggal Akhir</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-calendar"></i></span>
              <input type="text" id="laporanTglAkhir" name="tglAkhir" class="form-control pickadate" placeholder="Tanggal akhir&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-4">
            <!-- onClick="downloadReport('RPT_COLLECTING_QUERY');" -->
            <button type="button" class="btn bg-warning" onclick="return view('penagihan')"><b><i class="fa fa-list"></i></b> Lihat data</button>
            <button type="submit" class="btn bg-blue"><b><i class="fa fa-download"></i></b> Unduh File</button>
            <!-- <button type="submit">hhh</button> -->
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Tabungan By Perusahaan</h5>
    </div>

    <div class="panel-body">
      <form class="form-horizontal" method="GET" action="<?php echo asset_url(); ?>/admin/collection/tabungan/laporan" id="tabungan">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Perusahaan</label>
          <div class="col-lg-4">
            <select class="js-example-basic-single" name="collector" id="laporanCollector">
              <option value="ALL">[Semua Perusahaan]</option>
              <?php
              if(isset($ctrlPrsh) && count($ctrlPrsh) > 0) {
                foreach ($ctrlPrsh as $aData) {
                  ?>
                  <option value="<?php echo $aData->{"PRSH_ID"}; ?>"><?php echo $aData->{"PRSH_NAMA"}; ?></option>
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
              <input type="text" id="laporanTglAwal" name="tglAwal1" class="form-control pickadate" placeholder="Tanggal awal&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Tanggal Akhir</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-calendar"></i></span>
              <input type="text" id="laporanTglAkhir" name="tglAkhir1" class="form-control pickadate" placeholder="Tanggal akhir&hellip;" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-4">
            <!-- onClick="downloadReport('RPT_COLLECTING_QUERY');" -->
            <button type="button" class="btn bg-warning" onclick="return view('tabungan')"><b><i class="fa fa-list"></i></b> Lihat data</button>
            <button type="submit" class="btn bg-blue"><b><i class="fa fa-download"></i></b> Unduh File</button>
            <!-- <button type="submit">hhh</button> -->
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
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
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

  function downloadReport(rptType) {
    if(rptType == "RPT_COLLECTING_QUERY") {
      var laporanCollector = $("#laporanCollector").val();
      var laporanTglAwal = $('#laporanTglAwal').pickadate('picker').get('select', 'yyyy-mm-dd');
      var laporanTglAkhir = $('#laporanTglAkhir').pickadate('picker').get('select', 'yyyy-mm-dd');
      //alert("collector : " + laporanCollector + "\nawal : " + laporanTglAwal + "\nakhir : " + laporanTglAkhir);
      window.location = "<?php echo asset_url(); ?>/admin/laporan/download?tipe=" + rptType + "&collector=" + laporanCollector + "&awal=" + laporanTglAwal + "&akhir=" + laporanTglAkhir;
    }
  }
  function view(target) {
    console.log(target);
    if (target == 'tabungan') {
      window.location = '<?php echo asset_url(); ?>/admin/collection/tabungan/laporan/view?type=RPT_COLLECTING_QUERY&' + $('#tabungan').serialize();
    }
    if (target == 'penagihan') {
      window.location = '<?php echo asset_url(); ?>/admin/laporan/view?type=RPT_COLLECTING_QUERY&' + $('#penagihan').serialize();
    }
  }
</script>
@stop
