@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Jadwal Tabungan</span></h4>
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
      <li><a href="<?php echo asset_url(); ?>/collection/jadwal-penagihan"><i class="icon-calendar2"></i> Jadwal Tabungan</a></li>
      <li class="active"> #<?php echo $ctlBuId; ?></li>
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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Detil Jadwal Tabungan ID#<?php echo $ctlBuId; ?></h5>
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
        <div class="col-lg-12">
          <table class="table datatable-basic" style="font-size:90%;">
            <thead>
              <tr>
                <th style="text-align:center" width="15%">Kode Cabang</th>
                <th style="text-align:center" width="15%">Kode Group</th>
                <th style="text-align:center" width="20%">ID Collector</th>
                <th style="text-align:center" width="20%">Nama Collector</th>
                <th style="text-align:center" width="18%">No Rekening</th>
                <th style="text-align:center" width="18%">Status</th>
                <th style="text-align:center" width="18%">ID Nasabah</th>
                <th style="text-align:center" width="18%">Nama Nasabah</th>
                <th style="text-align:center" width="18%">Alamat</th>
                <th style="text-align:center" width="18%">Tgl Registrasi</th>
                <th style="text-align:center" width="18%">Tgl Upload</th>
                <th style="text-align:center" width="20%">Saldo Awal</th>
                <th style="text-align:center" width="30%">Saldo Minimum</th>
                <th style="text-align:center" width="30%">Setoran Minimum</th>
                <th style="text-align:center" width="30%">Jumlah Setoran</th>
                <th style="text-align:center" width="35%">Tgl Setoran</th>
                <th style="text-align:center" width="18%">Keterangan</th>
                <th style="text-align:center" width="15%">Tindakan</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if(isset($ctlBUD) && count($ctlBUD) > 0) {
                foreach ($ctlBUD as $aData) {
                  ?>
                  <tr>
                     <td style="text-align: center;"><?php echo $aData->{"BT_CAB"}; ?></td>
                    <td style="text-align: center;"><?php echo $aData->{"BT_KODE_GROUP"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"USERBIGID"}; ?></td>
                    <td style="text-align:left" ><?php echo $aData->{"U_NAMA"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BT_NO_REKENING"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"R_INFO"}; ?></td>
                    <td style="text-align:center" ><?php echo $aData->{"BT_NASABAH_ID"}; ?></td>
                    <td style="text-align:left" ><?php echo $aData->{"BT_NASABAH_NAMA"}; ?></td>
                    <td style="text-align:left;" ><?php echo $aData->{"BT_ALAMAT"}; ?></td>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"TGL_REGISTRASI"},"LONG"); ?>
                    <td style="text-align:center" ><?php echo tglIndo($aData->{"TGL_UPLOAD"},"LONG"); ?>
                    <td style="text-align:center" >Rp. <?php echo number_format($aData->{"BT_SALDO_AWAL"}); ?></td>
                    <td style="text-align:center" >Rp. <?php echo number_format($aData->{"BT_SALDO_MINIMUM"}); ?></td>
                    <td style="text-align:center" >Rp. <?php echo number_format($aData->{"BT_SETOR_MINIMUM"}); ?></td>
                    <td style="text-align:center" >Rp. <?php echo number_format($aData->{"BT_SETORAN"}); ?></td>
                    <?php if($aData->{"TGL_SETORAN"} === "0000-00-00 00:00:00") { ?>
                    <td></td>
                    <?php } else { ?>
                    <td style="text-align:center" ><?php echo date("d-M-Y H:i:s", strtotime($aData->{"TGL_SETORAN"})); ?></td>
                    <?php } ?>
                    <td style="text-align:center" ><?php echo $aData->{"BT_KETERANGAN"}; ?></td>
                    <td style="text-align:center;vertical-align:top;">
                      <?php
                      if(trim($aData->{"BT_PATH_IMAGE"}) != "-" && trim($aData->{"BT_PATH_IMAGE"}) != "") {
                        ?>
                        <a href="<?php echo asset_url(); ?>/<?php echo $aData->{"BT_PATH_IMAGE"}; ?>" download><i class="fa fa-download"></i> Download Foto</a>
                        <?php
                      }
                      else {
                        ?>
                        &nbsp;
                        <?php
                      }
                      ?>
                    </td>
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

</script>
@stop
