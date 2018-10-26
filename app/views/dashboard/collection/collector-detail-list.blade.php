@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Data Collector</span></h4>
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
      <li><a href="<?php echo asset_url(); ?>/collection/collector"><i class="icon-users4"></i> Data Collector</a></li>
      <li class="active"> <?php echo $ctlCollectorData->{"U_ID"}." (".$ctlCollectorData->{"U_NAMA"}.")"; ?></li>
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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Detil Data Collector <?php echo $ctlCollectorData->{"U_ID"}; ?></h5>
      <!--
      <div class="heading-elements">
        <ul class="icons-list">  
          <li><a href="javascript:addPayment()"><i class=" icon-plus2"></i> New Payment</a></li>
          <li><a data-action="collapse"></a></li>
        </ul>
      </div>
      -->
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-3">          
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
        <div class="col-lg-8">&nbsp;</div>
      </div>          
      <div class="row">
        <div class="col-lg-12">
          <table class="table datatable-basic" style="font-size:90%;">
            <thead>
              <tr>
                <!--th style="text-align:center" width="20%">Petugas</th-->
                <th style="text-align:center" width="15%">Tgl Upload</th>
                <th style="text-align:center" width="18%">No Rek</th>
                <th style="text-align:center" width="18%">Status</th>
                <th style="text-align:center" width="18%">ID Nasabah</th>
                <th style="text-align:center" width="18%">Nama Nasabah</th>
                <th style="text-align:center" width="15%">Masa Kredit</th>
                <th style="text-align:center" width="15%">Periode</th>
                <th style="text-align:center" width="25%">Alamat Nasabah</th>
                <th style="text-align:center" width="15%">No.Ponsel</th>
                <th style="text-align:center" width="15%">Tgl Kredit</th>
                <th style="text-align:center" width="15%">Tgl Angsuran</th>
                <th style="text-align:center" width="15%">Tgl Jadwal</th>
                <th style="text-align:center" width="15%">Tungg Pokok</th>
                <th style="text-align:center" width="15%">Tungg Bunga</th>
                <th style="text-align:center" width="15%">Tungg Denda</th>
                <th style="text-align:center" width="15%">Pokok</th>
                <th style="text-align:center" width="15%">Bunga</th>
                <th style="text-align:center" width="15%">Denda</th>
                <th style="text-align:center" width="15%">Jumlah</th>
                <th style="text-align:center" width="15%">Pembayaran</th>
                <th style="text-align:center" width="15%">Tgl Bayar</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if(isset($ctlCollRecords) && count($ctlCollRecords) > 0) {
                foreach ($ctlCollRecords as $aData) {
                  ?>
                  <tr>
                    <!--td style="text-align:left" ><?php echo $ctlCollectorData->{"U_NAMA"}; ?></td-->
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"BU_TGL"},"SHORT"); ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BUD_PINJ_ID"}; ?></td>
                    <td style="text-align:center" ><?php echo getReferenceInfo("STATUS_COLLECTION", $aData->{"BUD_STATUS"}); ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BUD_CUST_ID"}; ?></td>
                    <td style="text-align:left" ><?php echo $aData->{"BUD_CUST_NAMA"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BUD_PINJ_MASA_KREDIT"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BUD_PINJ_PERIODE"}; ?></td>
                    <td style="text-align:left" ><?php echo $aData->{"BUD_CUST_ALAMAT"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BUD_CUST_PONSEL"}; ?></td>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"BUD_PINJ_TGL_KREDIT"},"SHORT"); ?></td>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"BUD_PINJ_TGL_ANGS"},"SHORT"); ?></td>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"BUD_PINJ_TGL_JADWAL"},"SHORT"); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_PINJ_POKOK"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_PINJ_BUNGA"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_PINJ_DENDA"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_EDIT_POKOK"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_EDIT_BUNGA"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_EDIT_DENDA"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_PINJ_JUMLAH"}); ?></td>
                    <td style="text-align:center" ><?php echo number_format($aData->{"BUD_PINJ_JUMLAH_BAYAR"}); ?></td>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"},"SHORT"); ?></td>
                  </tr>
                  <?php
                }
              }
              else {
                ?>
                <!--
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td class="text-center">-- Tidak ada data --</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                //-->
                <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>        
    </div>
  </div>
</div>  

<div class="footer text-muted"></div>

<!-- Theme JS files -->
<script type="text/javascript">
  // Select with search
  //$('.select').select2();
</script>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/uploader_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/jquery.form.js"></script>
<script type="text/javascript">
  $(function() {
    // Table setup
    // ------------------------------
    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,        
        /*
        columnDefs: [{ 
            orderable: false,
            width: '80px',
            targets: [ 3 ]
        }],*/
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Search &nbsp;</span> _INPUT_',
            lengthMenu: '<span>Show &nbsp;</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });

    // Datatable with saving state
    $('.datatable-basic').DataTable({
        stateSave: true,
        "order": [[ 0, "desc" ]],
        scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        fixedColumns:   {
          leftColumns: 1,
          rightColumns: 1
        }
    });

    // External table additions
    // ------------------------------
    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder','Keyword...');

    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: "-1"
    });     
  });

  function reloadPage() {
    var filterPeriode = $("#filterPeriode").val();
    window.location = "<?php echo asset_url(); ?>/collection/collector/detail?periode=" + filterPeriode + "&id=<?php echo $ctlCollectorData->{'U_ID'}; ?>";
  }
</script>
@stop