@extends('dashboard.layout-dashboard')

@section('content')

<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Laporan Tabungan</span></h4>
    </div>
  </div>

  <div class="breadcrumb-line">
    <ul class="breadcrumb">
      <li><a href="<?= asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
      <li><i class="icon-menu position-left"></i> Tabungan</a></li>
      <li class="active"><i class="icon-clipboard5"></i> Laporan</li>
    </ul>
  </div>
</div>

<div class="content">
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Tabungan Bulanan</h5>
    </div>
    <div class="panel-body">
      <div class="table-resposive">
        <table class="table table-bordered datatables">
          <thead>
            <tr>
              <th data-orderable="false">USER ID</th>
              <th data-orderable="false">TANGGAL</th>
              <th data-orderable="false">NAMA COLLECTOR</th>
              <th data-orderable="false">KODE GROUP</th>
              <th data-orderable="false">CAB</th>
              <th data-orderable="false">NO REKENING</th>
              <th data-orderable="false">ID NASABAH</th>
              <th data-orderable="false">NAMA NASABAH</th>
              <th data-orderable="false">ALAMAT</th>
              <th data-orderable="false">HP</th>
              <th data-orderable="false">SETORAN</th>
              <th data-orderable="false">TANGGAL BAYAR</th>
              <th data-orderable="false">KETERANGAN</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tabungans as $key => $value): ?>
            <tr>
              <td><?= $value->USERBIGID ?></td>
              <td><?= date_format(date_create($value->TGL_SETORAN), 'd-m-Y') ?></td>
              <td><?= $value->U_NAMA ?></td>
              <td><?= $value->COLLECT_KODE ?></td>
              <td><?= $value->CAB ?></td>
              <td><?= $value->REK ?></td>
              <td><?= $value->CUST_ID ?></td>
              <td><?= $value->CUST_NAMA ?></td>
              <td><?= $value->CUST_ALAMAT ?></td>
              <td><?= $value->CUST_PONSEL ?></td>
              <td><?= $value->SETORAN ?></td>
              <td><?= $value->TGL_SETORAN ?></td>
              <td><?= $value->KETERANGAN ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer">
      <a href="<?= asset_url(); ?>/admin/laporan" class="btn btn-warning"><i class="fa fa-fw fa-reply"></i>Kembali</a>
      <a href="<?= asset_url(); ?>/admin/tabungan/laporan/download?<?= Request::server('QUERY_STRING') ?>" class="btn btn-primary"><i class="fa fa-fw fa-download"></i>Unduh File</a>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script>
  $(document).ready(function () {
    $('.datatables').DataTable({
      "columnDefs": [
        { "visible": false, "targets": 1 }
      ],
      "order": [[ 1, 'asc' ]],
      "scrollX": true,
      "drawCallback": function ( settings ) {
        var api = this.api();
        var rows = api.rows( {page:'current'} ).nodes();
        var last=null;
        api.column(1, {page:'current'} ).data().each( function ( group, i ) {
          if ( last !== group ) {
            $(rows).eq(i).before(
              '<tr class="group"><td colspan="17">'+group+'</td></tr>'
            );
            last = group;
          }
        })
      }
    });
  });
</script>

@stop
