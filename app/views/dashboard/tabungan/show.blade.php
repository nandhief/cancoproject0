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
      <li><a href="<?= asset_url(); ?>/tabungan"><i class="icon-calendar2"></i> Tabungan</a></li>
      <li class="active"> #<?= $tgl ?></li>
    </ul>
  </div>
</div>

<div class="content">
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Detil Tabungan TGL #<?= $tgl ?></h5>

      <div class="heading-elements">
        <ul class="icons-list">
          <li><a data-action="collapse"></a></li>
        </ul>
      </div>

    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-12">
          <table class="table datatable-basic" style="font-size:90%;">
            <thead>
              <tr>
                <th>Kode Cabang</th>
                <th>Kode Group</th>
                <th>ID Collector</th>
                <th>Nama Collector</th>
                <th>No Rekening</th>
                <th>ID Nasabah</th>
                <th>Nama Nasabah</th>
                <th>Alamat Nasabah</th>
                <th>No.Ponsel</th>
                <th>Setoran</th>
                <th>No.Nota</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tabungans as $key => $tabungan) : ?>
              <tr>
                  <td><?= $tabungan->CAB ?></td>
                  <td><?= $tabungan->KODE_GROUP ?></td>
                  <td><?= $tabungan->COLL_ID ?></td>
                  <td><?= $tabungan->U_NAMA ?></td>
                  <td><?= $tabungan->REK ?></td>
                  <td><?= $tabungan->CUST_ID ?></td>
                  <td><?= $tabungan->CUST_NAMA ?></td>
                  <td><?= $tabungan->CUST_ALAMAT ?></td>
                  <td><?= $tabungan->CUST_PONSEL ?></td>
                  <td><?= $tabungan->SETORAN ?></td>
                  <td><?= $tabungan->NO_NOTA ?></td>
                  <td><?= $tabungan->KETERANGAN ?></td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="footer text-muted"></div>

<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/pages/uploader_bootstrap.js"></script>
<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/jquery.form.js"></script>
<script type="text/javascript">
  $(function() {
    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Search &nbsp;</span> _INPUT_',
            lengthMenu: '<span>Show &nbsp;</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
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
  });

</script>
@stop
