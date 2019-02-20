@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Tabungan</span></h4>
    </div>
  </div>

  <div class="breadcrumb-line">
    <ul class="breadcrumb">
      <li><a href="<?= asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
      <li><i class="icon-menu position-left"></i> Collection</a></li>
      <li class="active"><i class="icon-calendar2"></i>Tabungan</li>
    </ul>
  </div>
</div>

<div class="content">
  <div class="panel panel-flat">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Unggah File Daftar Nasabah</h5>
      <div class="heading-elements">
        <ul class="icons-list">
          <li><a href="<?= asset_url(); ?>/assets/documents/sample_tabungan.xlsx"><i class="  icon-download4"></i> Download File Contoh</a></li>
          <li><a data-action="collapse"></a></li>
        </ul>
      </div>
    </div>

    <div class="panel-body">
      <p class="content-group">
        Unggah file Excel berisi daftar nasabah.
      </p>

      <p class="content-group">
        Harap pastikan isi dan format file sudah benar. Setelah diunggah, nasabah akan sinkron dengan aplikasi mobile petugas lapangan.
      </p>

      <form class="form-horizontal" enctype="multipart/form-data" method="post" id="formUploadData" name="formUploadData" action="<?= asset_url(); ?>/tabungan/store">
        <div class="form-group">
          <!--<label class="col-lg-2 control-label text-semibold">File Data Format Excel</label>-->
          <div class="col-lg-4">
            <input type="file" class="file-input" data-show-preview="false" data-show-upload="false" id="tabungan" name="tabungan" placeholder="File format Excel...">
          </div>
        </div>

        <div class="form-group">
          <div class="col-lg-4">
            <button  onClick="saveContent()" class="btn bg-blue-theme btn-block">Unggah File<i class="fa fa-upload position-right"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Data Penitipan Tabungan</h5>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-12">
          <table class="table datatable-basic" style="font-size:90%;">
            <thead>
              <tr>
                <th style="text-align: center" width="5%">No</th>
                <th>Tanggal Penitipan</th>
                <th>Jumlah Data</th>
                <th>Jumlah Dana</th>
                <th data-orderable="false">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tabungans as $key => $tabungan) : ?>
              <tr>
                  <td><?= $key+1 ?></td>
                  <td><?= $tabungan->TGL ?></td>
                  <td><?= $tabungan->JUMLAH ?></td>
                  <td><?= $tabungan->TOTAL ?></td>
                  <td><a href="<?= asset_url() . '/tabungan/' . $tabungan->TGL ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a></td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-flat">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Unggah File Daftar Nasabah Yang Dinonaktifkan</h5>
      <div class="heading-elements">
        <ul class="icons-list">
          <li><a href="<?= asset_url(); ?>/assets/documents/sample_tabungan_nonaktif.xlsx"><i class="  icon-download4"></i> Download File Contoh</a> </li>
          <li> <a data-action="collapse"></a></li>
        </ul>
      </div>
    </div>

    <div class="panel-body collapse">
      <p class="content-group">
        Unggah file Excel berisi daftar nasabah yang dinonaktikan.
      </p>

      <p class="content-group">
        Harap pastikan isi dan format file sudah benar. Setelah diunggah, nasabah akan sinkron dengan aplikasi mobile petugas lapangan.
      </p>

      <form class="form-horizontal" enctype="multipart/form-data" method="post" id="formUploadDataUpdate" name="formUploadDataUpdate" action="<?= asset_url(); ?>/tabungan/update">
        <div class="form-group">
          <div class="col-lg-4">
            <input type="file" class="file-input" data-show-preview="false" data-show-upload="false" id="tabungan" name="tabungan" placeholder="File format Excel...">
          </div>
        </div>

        <div class="form-group">
          <div class="col-lg-4">
            <button  onClick="updateContent()" class="btn bg-blue-theme btn-block">Unggah File<i class="fa fa-upload position-right"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Data Nasabah Tabungan</h5>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-12">
          <table class="table datatable-basic" style="font-size:90%;">
            <thead>
              <tr>
                <th style="text-align: center" width="5%">No</th>
                <th>CAB</th>
                <th>No Rek</th>
                <th>Nama Nasabah</th>
                <th>Telp Nasabah</th>
                <th>Setor Minimal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($nasabah as $key => $value) : ?>
              <tr>
                  <td><?= $key+1 ?></td>
                  <td><?= $value->CAB ?></td>
                  <td><?= $value->REK ?></td>
                  <td><?= $value->CUST_NAMA ?></td>
                  <td><?= $value->CUST_PONSEL ?></td>
                  <td><?= number_format($value->SETOR_MINIMUM, 0,',','.') ?></td>
                  <td class="<?= $value->TUTUP ? 'bg-danger' : 'bg-success' ?>"<?= $value->TUTUP ? '' : ' onclick="return update(\'' . $value->REK . '\')" style="cursor:pointer;"' ?>><?= $value->TUTUP ? 'TUTUP' : 'AKTIF' ?></td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/pages/uploader_bootstrap.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/jquery.form.js"></script>
<script type="text/javascript">
  $(function() {
    // Table setup
    // ------------------------------
    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Search &nbsp;</span> _INPUT_',
            lengthMenu: '<span>Show &nbsp;</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-4).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-4).find('.dropdown, .btn-group').removeClass('dropup');
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

    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder','Keyword...');

    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: "-1"
    });

    $(".datepicker").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });

  var gBusy = false;

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
              window.location = "<?= asset_url(); ?>/tabungan";
            }, 500);
          }
          else {
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
                window.location = "<?= asset_url(); ?>/tabungan";
              }, 500);
            });
          }
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          window.location = "<?= asset_url(); ?>/tabungan";
        }
      };
      $('#formUploadData').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate);
        return false;
      });
    }
  }

  function updateContent() {
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
              window.location = "<?= asset_url(); ?>/tabungan";
            }, 500);
          }
          else {
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
                window.location = "<?= asset_url(); ?>/tabungan";
              }, 500);
            });
          }
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          window.location = "<?= asset_url(); ?>/tabungan";
        }
      };
      $('#formUploadDataUpdate').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate);
        return false;
      });
    }
  }

    function update(rek) {
        swal({
            type: 'warning',
            title: 'Yakin Nonaktikan tabungan',
            text: 'NO REKENING ' + rek,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            html: true
        }, function () {
            createOverlay("Mohon Tunggu...");
            $.ajax({
                type: 'POST',
                url: '<?= asset_url() ?>/tabungan/update',
                data: {
                    NO_REKENING: rek
                },
                beforeSend: function () {
                    $(this).attr('disabled', true)
                },
                success: function (response) {
                    gOverlay.hide();
                    var data = JSON.parse(response)
                    toastr.success(data.MESSAGE);
                    window.location = "<?= asset_url() ?>/tabungan"
                }
            });
        })
    }

    $('.filterPeriode').on('click', function () {
      var dari = $('#dari').val();
      var sampai = $('#sampai').val();
      window.location = "<?php echo asset_url(); ?>/tabungan?dari=" + dari + "&sampai=" + sampai;
    });
</script>
@stop
