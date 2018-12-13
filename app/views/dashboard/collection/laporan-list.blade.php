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
      <li><a href="<?= asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
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
    <div class="col-md-6">
        <div class="panel panel-flat border-top-primary">
            <div class="panel-heading">
                <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Collecting Bulanan</h5>
            </div>

            <div class="panel-body">
                <p class="content-group">
                Laporan yang berisi data dan status collecting yang dilakukan petugas lapangan.
                </p>

                <form class="form-horizontal" method="post">
                <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">Bulan</label>
                    <div class="col-md-8">
                        <select class="form-control" id="filterPeriode" onChange="reloadPage()">
                            <option value="<?= (date("Y")-1); ?>-12">Desember <?= date("Y")-1; ?></option>
                            <option value="<?= date("Y"); ?>-01">Januari <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-02">Februari <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-03">Maret <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-04">April <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-05">Mei <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-06">Juni <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-07">Juli <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-08">Agustus <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-09">September <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-10">Oktober <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-11">November <?= date("Y"); ?></option>
                            <option value="<?= date("Y"); ?>-12">Desember <?= date("Y"); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">&nbsp;</label>
                    <div class="col-md-8">
                        <button type="button" class="btn bg-green btn-block btn-labeled" onClick="return viewReport('RPT_COLLECTING')"><b><i class="fa fa-list"></i></b> Lihat Data</button>
                        <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="downloadReport('RPT_COLLECTING')"><b><i class="fa fa-download"></i></b> Unduh File</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-flat border-top-primary">
            <div class="panel-heading">
                <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Collecting By Collector</h5>
            </div>

            <div class="panel-body">
                <form class="form-horizontal" method="post">
                    <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">Collector</label>
                    <div class="col-md-8">
                        <select class="form-control" name="laporanCollector" id="laporanCollector">
                        <option value="ALL">[Semua kolektor]</option>
                        <?php
                        if(isset($ctlCollector) && count($ctlCollector) > 0) {
                            foreach ($ctlCollector as $aData) {
                            ?>
                            <option value="<?= $aData->{"U_ID"}; ?>"><?= $aData->{"U_NAMA"}; ?></option>
                            <?php
                            }
                        }
                        ?>
                        </select>
                    </div>
                    </div>

                    <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">Tanggal Awal</label>
                    <div class="col-md-8">
                        <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                        <input type="text" id="laporanTglAwal" class="form-control pickadate" placeholder="Tanggal awal&hellip;" data-date-format="dd-mm-yyyy" value="<?= date('d-m-Y'); ?>">
                        </div>
                    </div>
                    </div>

                    <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">Tanggal Akhir</label>
                    <div class="col-md-8">
                        <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                        <input type="text" id="laporanTglAkhir" class="form-control pickadate" placeholder="Tanggal akhir&hellip;" data-date-format="dd-mm-yyyy" value="<?= date('d-m-Y'); ?>">
                        </div>
                    </div>
                    </div>

                    <div class="form-group col-md-12">
                    <label class="col-md-4 control-label text-semibold">&nbsp;</label>
                    <div class="col-md-8">
                        <button type="button" class="btn bg-green btn-block btn-labeled" onClick="viewReport('RPT_COLLECTING_QUERY');"><b><i class="fa fa-download"></i></b> Lihat Data</button>
                        <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="downloadReport('RPT_COLLECTING_QUERY');"><b><i class="fa fa-download"></i></b> Unduh File</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Tabungan Bulanan</h5>
                </div>
                <div class="panel-body">
                    <p class="content-group">
                    Laporan yang berisi data dan status tabungan yang dilakukan petugas lapangan.
                    </p>
                    <form class="form-horizontal" method="post" id="tabungan_bulanan">
                    <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">Bulan</label>
                        <div class="col-md-8">
                            <select class="form-control" name="periode" id="filterPeriode">
                                <option value="<?= (date("Y")-1); ?>-12">Desember <?= date("Y")-1; ?></option>
                                <option value="<?= date("Y"); ?>-01">Januari <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-02">Februari <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-03">Maret <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-04">April <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-05">Mei <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-06">Juni <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-07">Juli <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-08">Agustus <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-09">September <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-10">Oktober <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-11">November <?= date("Y"); ?></option>
                                <option value="<?= date("Y"); ?>-12">Desember <?= date("Y"); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">&nbsp;</label>
                        <div class="col-md-8">
                            <button type="button" class="btn bg-green btn-block btn-labeled" onClick="return viewReportTabungan('bulanan')"><b><i class="fa fa-list"></i></b> Lihat Data</button>
                            <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="return downloadReportTabungan('bulanan')"><b><i class="fa fa-download"></i></b> Unduh File</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Tabungan By Collector</h5>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" method="post" id="tabungan_collector">
                        <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">Collector</label>
                        <div class="col-md-8">
                            <select class="form-control" name="laporanCollector" id="laporanCollector">
                            <option value="ALL">[Semua kolektor]</option>
                            <?php
                            if(isset($ctlCollector) && count($ctlCollector) > 0) {
                                foreach ($ctlCollector as $aData) {
                                ?>
                                <option value="<?= $aData->{"U_ID"}; ?>"><?= $aData->{"U_NAMA"}; ?></option>
                                <?php
                                }
                            }
                            ?>
                            </select>
                        </div>
                        </div>

                        <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">Tanggal Awal</label>
                        <div class="col-md-8">
                            <div class="input-group">
                            <span class="input-group-addon"><i class="icon-calendar"></i></span>
                            <input type="text" name="laporanTglAwal" id="laporanTglAwal" class="form-control pickadate" placeholder="Tanggal awal&hellip;" data-date-format="dd-mm-yyyy" value="<?= date('d-m-Y'); ?>">
                            </div>
                        </div>
                        </div>

                        <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">Tanggal Akhir</label>
                        <div class="col-md-8">
                            <div class="input-group">
                            <span class="input-group-addon"><i class="icon-calendar"></i></span>
                            <input type="text" name="laporanTglAkhir" id="laporanTglAkhir" class="form-control pickadate" placeholder="Tanggal akhir&hellip;" data-date-format="dd-mm-yyyy" value="<?= date('d-m-Y'); ?>">
                            </div>
                        </div>
                        </div>

                        <div class="form-group col-md-12">
                        <label class="col-md-4 control-label text-semibold">&nbsp;</label>
                        <div class="col-md-8">
                            <button type="button" class="btn bg-green btn-block btn-labeled" onClick="return viewReportTabungan('collector');"><b><i class="fa fa-download"></i></b> Lihat Data</button>
                            <button type="button" class="btn bg-blue btn-block btn-labeled" onClick="return downloadReportTabungan('collector');"><b><i class="fa fa-download"></i></b> Unduh File</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer text-muted"></div>

<!-- Theme JS files -->
<!--pick-a-time -->
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/pickers/pickadate/picker.date.js"></script>

<script type="text/javascript">
  // Select with search
  //$('.select').select2();
  $('.pickadate').pickadate({
    format: 'dd-mm-yyyy',
    formatSubmit: 'yyyy-mm-dd'
  });
</script>

<script type="text/javascript">
  function reloadPage() {
    var filterPeriode = $("#filterPeriode").val();
    window.location = "<?= asset_url(); ?>/collection/laporan?periode=" + filterPeriode;
  }

  function downloadReport(rptType) {
    if(rptType == "RPT_COLLECTING") {
      var filterPeriode = $("#filterPeriode").val();
      window.location = "<?= asset_url(); ?>/collection/laporan/download?tipe=" + rptType + "&periode=" + filterPeriode;
    }
    if(rptType == "RPT_COLLECTING_QUERY") {
      var laporanCollector = $("#laporanCollector").val();
      var laporanTglAwal = $('#laporanTglAwal').pickadate('picker').get('select', 'yyyy-mm-dd');
      var laporanTglAkhir = $('#laporanTglAkhir').pickadate('picker').get('select', 'yyyy-mm-dd');
      //alert("collector : " + laporanCollector + "\nawal : " + laporanTglAwal + "\nakhir : " + laporanTglAkhir);
      window.location = "<?= asset_url(); ?>/collection/laporan/download?tipe=" + rptType + "&collector=" + laporanCollector + "&awal=" + laporanTglAwal + "&akhir=" + laporanTglAkhir;
    }
  }
  function viewReport(rptType) {
    if(rptType == "RPT_COLLECTING") {
      var filterPeriode = $("#filterPeriode").val();
      window.location = "<?= asset_url(); ?>/collection/laporan/view?tipe=" + rptType + "&periode=" + filterPeriode;
    }
    if(rptType == "RPT_COLLECTING_QUERY") {
      var laporanCollector = $("#laporanCollector").val();
      var laporanTglAwal = $('#laporanTglAwal').pickadate('picker').get('select', 'yyyy-mm-dd');
      var laporanTglAkhir = $('#laporanTglAkhir').pickadate('picker').get('select', 'yyyy-mm-dd');
      //alert("collector : " + laporanCollector + "\nawal : " + laporanTglAwal + "\nakhir : " + laporanTglAkhir);
      window.location = "<?= asset_url(); ?>/collection/laporan/view?tipe=" + rptType + "&collector=" + laporanCollector + "&awal=" + laporanTglAwal + "&akhir=" + laporanTglAkhir;
    }
  }
    function downloadReportTabungan(type) {
        if (type == 'bulanan') {
            window.location = '<?= asset_url(); ?>/tabungan/laporan/download?type=bulanan&' + $('form#tabungan_bulanan').serialize()
        }
        if (type == 'collector') {
            window.location = '<?= asset_url(); ?>/tabungan/laporan/download?type=collector&' + $('form#tabungan_collector').serialize()
        }
    }
    function viewReportTabungan(type) {
        if (type == 'bulanan') {
            window.location = '<?= asset_url(); ?>/tabungan/laporan/view?type=bulanan&' + $('form#tabungan_bulanan').serialize()
        }
        if (type == 'collector') {
            window.location = '<?= asset_url(); ?>/tabungan/laporan/view?type=collector&' + $('form#tabungan_collector').serialize()
        }
    }
</script>
@stop
