@extends('dashboard.layout-dashboard')

@section('content')

<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Laporan Collecting</span></h4>
    </div>
  </div>

  <div class="breadcrumb-line">
    <ul class="breadcrumb">
      <li><a href="<?php echo asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
      <li><i class="icon-menu position-left"></i> Collection</a></li>
      <li class="active"><i class="icon-clipboard5"></i> Laporan</li>
    </ul>
  </div>
</div>

<div class="content">
  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Tabungan By Perusahaan</h5>
    </div>
    <div class="panel-body">
      <div class="table-resposive">
        <table class="table table-bordered datatables">
          <thead>
            <tr>
              <th data-orderable="false">TANGGAL</th>
              <th data-orderable="false">KODE GROUP</th>
              <th data-orderable="false">NAMA COLLECTOR</th>
              <th data-orderable="false">KODE CAB</th>
              <th data-orderable="false">NO REKENING</th>
              <th data-orderable="false">NASABAH ID</th>
              <th data-orderable="false">NAMA NASABAH</th>
              <th data-orderable="false">ALAMAT</th>
              <th data-orderable="false">TGL REGISTRASI</th>
              <th data-orderable="false">TGL UPLOAD</th>
              <th data-orderable="false">SALDO AWAL</th>
              <th data-orderable="false">SETOR MINIMUM</th>
              <th data-orderable="false">SALDO MINIMUM</th>
              <th data-orderable="false">STATUS</th>
              <th data-orderable="false">KETERANGAN</th>
              <th data-orderable="false">SETORAN</th>
              <th data-orderable="false">TGL SETORAN</th>
              <th data-orderable="false">JAM SETORAN</th>
              <th data-orderable="false">PETUGAS LAPANGAN</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tabungan)): ?>
            <?php foreach ($tabungan as $key => $value): ?>
            <tr>
              <td><?= tglIndo($value->TGL_UPLOAD, "SHORT") ?></td>
              <td><?= $value->BT_KODE_GROUP ?></td>
              <td><?= $value->BT_COLL_ID ?></td>
              <td><?= $value->BT_CAB ?></td>
              <td><?= $value->BT_NO_REKENING ?></td>
              <td><?= $value->BT_NASABAH_ID ?></td>
              <td><?= $value->BT_NASABAH_NAMA ?></td>
              <td><?= $value->BT_ALAMAT ?></td>
              <td><?= $value->TGL_REGISTRASI ?></td>
              <td><?= $value->TGL_UPLOAD ?></td>
              <td><?= $value->BT_SALDO_AWAL ?></td>
              <td><?= $value->BT_SETOR_MINIMUM ?></td>
              <td><?= $value->BT_SALDO_MINIMUM ?></td>
              <td> <?php if ($value->BT_STATUS == 'BT_MENABUNG'): ?>
                  Menabung
                <?php elseif($value->BT_STATUS == 'BT_TIDAK_MENABUNG'): ?>
                  Tidak Menabung
                <?php else: ?>
                  Dalam Penjadwalan
                <?php endif ?>
              </td>
              <td><?= $value->BT_KETERANGAN ?></td>
              <td><?= $value->BT_SETORAN ?></td>
              <td><?= tglIndo($value->TGL_SETORAN, "LONG") ?></td>
              <td><?= date("H:i:s", strtotime($value->TGL_SETORAN)) ?></td>
              <td><?= $value->BT_PETUGAS ?></td>
            </tr>
            <?php endforeach ?>
            <?php endif ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer">
      <a href="<?php echo asset_url(); ?>/admin/laporan" class="btn btn-warning"><i class="fa fa-fw fa-reply"></i>Kembali</a>
      <a href="<?php echo asset_url(); ?>/admin/collection/tabungan/laporan?<?= Request::server('QUERY_STRING') ?>" class="btn btn-primary"><i class="fa fa-fw fa-download"></i>Unduh File</a>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script>
  $(document).ready(function () {
    $('.datatables').DataTable({
      // "columnDefs": [
      //   { "visible": false, "targets": 0 }
      // ],
      "dom": '<"top">rt<"bottom"pl>',
      // "order": [[ 0, 'asc' ]],
      "scrollX": true
      // "drawCallback": function ( settings ) {
      //   var api = this.api();
      //   var rows = api.rows( {page:'current'} ).nodes();
      //   var last=null;
      //   api.column(0, {page:'current'} ).data().each( function ( group, i ) {
      //     if ( last !== group ) {
      //       $(rows).eq( i ).before(
      //         '<tr class="group"><td colspan="17">'+group+'</td></tr>'
      //       );
      //       last = group;
      //     }
      //   })
      // }
    });
  });
</script>
      
@stop