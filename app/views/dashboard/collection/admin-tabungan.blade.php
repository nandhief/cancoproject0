@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Tabungan</span></h4>
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
      <li class="active"><i class="icon-calendar2"></i>Tabungan</li>
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
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">List Data Jadwal Tabungan</h5>
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
        <div class="col-lg-4">
          <!-- <select class="form-control" id="filterPeriode" onChange="reloadPage()">
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
          </select> -->
          <div class="input-group">
            <input type="text" class="form-control datepicker" id="dari" data-value="<?= date_format(date_create(Input::get('dari')), 'Y/m/d') ?>">
            <div class="input-group-addon">s/d</div>
            <input type="text" class="form-control datepicker" id="sampai" data-value="<?= date_format(date_create(Input::get('sampai')), 'Y/m/d') ?>">
            <span class="input-group-btn">
              <button class="btn btn-sm btn-info filterPeriode">Tampilkan</button>
            </span>
          </div>
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
                <th style="text-align: center" width="5%">No</th>
                <th style="text-align:center" width="20%">Waktu Unggah</th>
                <th style="text-align: center;" width="20%">Perusahaan</th>
                <th style="text-align:center" width="15%">Jumlah Data</th>
                <th style="text-align:center" width="15%">Action</th>
              </tr>
            </thead>
            <tbody id="tblInvoice">
              <?php
              $no = 1;
              if(isset($ctlUploads) && count($ctlUploads) > 0) {
                foreach ($ctlUploads as $aData) {
                  $highlight = "";
                  if($aData->{"BU_TGL"} == date("Y-m-d")) $highlight = 'style="background-color: beige;"';
                  ?>
                  <tr <?php echo $highlight; ?>>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td style="text-align:center"><?php echo tglIndo($aData->{"BU_TGL"}, "LONG"); ?></td>
                    <td style="text-align: center;"><?php echo $aData->{"PRSH_NAMA"}; ?></td>
                    <td style="text-align:center"><?php echo $aData->{"JUMLAH_DATA"}; ?></td>
                    <td style="text-align:center;vertical-align:top;">
                      <button type="button" class="btn btn-primary btn-icon" onClick="showDetail('<?php echo $aData->{"BU_ID"}; ?>');"><i class="fa fa-ellipsis-h"></i></button>
                      <button type="button" class="btn btn-danger btn-icon" onClick="deleteData('<?php echo $aData->{"BU_ID"}; ?>');"><i class="fa fa-remove"></i></button>
                    </td>
                  </tr>
                  <?php
                }
              }
              else {
                ?>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td class="text-center">-- Tidak ada data yang dicari--</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
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

    $(".datepicker").datepicker({
      dateFormat: 'dd-mm-yy'
    });

    $('.pickatime-hidden').pickatime({
      formatSubmit: 'HH:i',
      hiddenName: true
    });
  });

  var gBusy = false;

  // function reloadPage() {
  //   var filterPeriode = $("#filterPeriode").val();
  //   window.location = "<?php echo asset_url(); ?>/admin/collection/tabungan?periode=" + filterPeriode;
  // }
    $('.filterPeriode').on('click', function () {
      var dari = $('#dari').val();
      var sampai = $('#sampai').val();
      window.location = "<?php echo asset_url(); ?>/admin/collection/tabungan?dari=" + dari + "&sampai=" + sampai;
    });

  function saveContent() {
    if(!gBusy) {
      gBusy = true;
      //- content action form
      var optFormContentUpdate = {
        //console.log();
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
              window.location = "<?php echo asset_url(); ?>/collection/tabungan";
            }, 500);
            /*
            swal({
              title: "SUKSES",
              text: data["MESSAGE"],
              type: "success",
              showCancelButton: false,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "OK",
              closeOnConfirm: false,
              html: true
            },
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/collection/jadwal-penagihan";
              }, 500);
            });
            */
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
                window.location = "<?php echo asset_url(); ?>/collection/tabungan";
              }, 500);
            });
          }
          //$(".btn").removeAttr("disabled");
          //window.location = "<?php echo asset_url(); ?>/collection/jadwal-penagihan";
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          window.location = "<?php echo asset_url(); ?>/collection/tabungan";
        }
      };
      $('#formUploadData').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate);
        // return false to prevent standard browser submit and page navigation
        return false;
      });
    }
  }

  function deleteData(buId) {
    swal({
      title: "Konfirmasi Hapus Data",
      text: "Hapus data upload ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya",
      cancelButtonText: "Tidak",
      closeOnConfirm: true,
      html: true
    },
    function(){
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/collection/tabungan/delete",
        data  : "id=" + buId,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            window.location = "<?php echo asset_url(); ?>/admin/collection/tabungan";
          }
          else {
            toastr.error(data["MESSAGE"]);
          }
        },
        error : function(error) {
          gOverlay.hide();
          alert("Network/server error\r\n" + error);
        }
      });
    });
  }

  function showDetail(buId) {
    window.location = "<?php echo asset_url(); ?>/collection/tabungan/detail/" + buId;
  }
</script>
@stop
