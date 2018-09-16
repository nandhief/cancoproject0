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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Laporan Penagihan By Perusahaan</h5>
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
              <th data-orderable="false">HP</th>
              <th data-orderable="false">AGUNAN</th>
              <th data-orderable="false">JML PINJAMAN</th>
              <th data-orderable="false">SALDO NOMINATIF</th>
              <th data-orderable="false">FP</th>
              <th data-orderable="false">FB</th>
              <th data-orderable="false">POKOK /BLN</th>
              <th data-orderable="false">BUNGA /BLN</th>
              <th data-orderable="false">KOLEKTIBILITAS</th>
              <th data-orderable="false">ANGSURAN KE</th>
              <th data-orderable="false">JANGKA WAKTU</th>
              <th data-orderable="false">TGL REALISASI</th>
              <th data-orderable="false">TGL UPLOAD</th>
              <th data-orderable="false">TGL JADWAL</th>
              <th data-orderable="false">TUNGG POKOK</th>
              <th data-orderable="false">TUNGG BUNGA</th>
              <th data-orderable="false">TUNGG DENDA</th>
              <th data-orderable="false">TAGIHAN</th>
              <th data-orderable="false">STATUS</th>
              <th data-orderable="false">KETERANGAN</th>
              <th data-orderable="false">BAYAR POKOK</th>
              <th data-orderable="false">BAYAR BUNGA</th>
              <th data-orderable="false">BAYAR DENDA</th>
              <th data-orderable="false">TOTAL BAYAR</th>
              <th data-orderable="false">TANGGAL BAYAR</th>
              <th data-orderable="false">JAM BAYAR</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($jadwal as $key => $value): ?>
            <tr>
              <td><?= $value->J_TGL ?></td>
              <td><?= $value->BUD_KODE_GROUP ?></td>
              <td><?= $value->BUD_COLL_U_ID ?></td>
              <td><?= $value->BUD_CAB ?></td>
              <td><?= $value->BUD_PINJ_ID ?></td>
              <td><?= $value->BUD_CUST_ID ?></td>
              <td><?= $value->BUD_CUST_NAMA ?></td>
              <td><?= $value->BUD_CUST_ALAMAT ?></td>
              <td><?= $value->BUD_CUST_PONSEL ?></td>
              <td><?= $value->BUD_AGUNAN ?></td>
              <td><?= $value->BUD_JML_PINJAMAN ?></td>
              <td><?= $value->BUD_SALDO_NOMINATIF ?></td>
              <td><?= $value->BUD_FP ?></td>
              <td><?= $value->BUD_FB ?></td>
              <td><?= $value->BUD_BLN_POKOK ?></td>
              <td><?= $value->BUD_BLN_BUNGA ?></td>
              <td><?= $value->BUD_KOLEKTIBILITAS ?></td>
              <td><?= $value->BUD_PINJ_PERIODE ?></td>
              <td><?= $value->BUD_PINJ_MASA_KREDIT ?></td>
              <td><?= tglIndo($value->BUD_PINJ_TGL_KREDIT, "SHORT") ?></td>
              <td><?= tglIndo($value->BUD_PINJ_TGL_JADWAL, "SHORT") ?></td>
              <td><?= $value->BUD_TGL_DEPAN_JADWAL ?></td>
              <td><?= $value->BUD_PINJ_POKOK ?></td>
              <td><?= $value->BUD_PINJ_BUNGA ?></td>
              <td><?= $value->BUD_PINJ_DENDA ?></td>
              <td><?= $value->BUD_PINJ_JUMLAH ?></td>
              <td><?= $value->J_STATUS ?></td>
              <td><?= $value->BUD_KETERANGAN ?></td>
              <td><?= $value->BUD_EDIT_POKOK ?></td>
              <td><?= $value->BUD_EDIT_BUNGA ?></td>
              <td><?= $value->BUD_EDIT_DENDA ?></td>
              <td><?= $value->J_PINJ_JUMLAH_BAYAR ?></td>
              <td><?= tglIndo($value->BUD_PINJ_TGL_BAYAR, "LONG") ?></td>
              <td><?= date("H:i:s", strtotime($value->J_PINJ_JUMLAH_BAYAR)) ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="panel-footer">
      <a href="<?php echo asset_url(); ?>/admin/laporan" class="btn btn-warning"><i class="fa fa-fw fa-reply"></i>Kembali</a>
      <a href="<?php echo asset_url(); ?>/admin/laporan/download?<?= Request::server('QUERY_STRING') ?>" class="btn btn-primary"><i class="fa fa-fw fa-download"></i>Unduh File</a>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script>
  $(document).ready(function () {
    $('.datatables').DataTable({
      "columnDefs": [
        { "visible": false, "targets": 0 }
      ],
      "order": [[ 0, 'asc' ]],
      "scrollX": true,
      "drawCallback": function ( settings ) {
        var api = this.api();
        var rows = api.rows( {page:'current'} ).nodes();
        var last=null;
        api.column(0, {page:'current'} ).data().each( function ( group, i ) {
          if ( last !== group ) {
            $(rows).eq( i ).before(
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