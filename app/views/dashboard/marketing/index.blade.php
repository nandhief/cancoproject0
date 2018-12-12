@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Marketing</span></h4>
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?= asset_url(); ?>"><i class="icon-home2 position-left"></i> Beranda</a></li>
            <li><i class="icon-menu position-left"></i> Collection</a></li>
            <li class="active"><i class="icon-briefcase"></i> Marketing</li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tahap Pengajuan</h5>
                </div>
                <div class="panel-body">
                    <div class="table-resposive">
                        <table class="table datatable-basic" style="font-size:90%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center" width="5%">NO</th>
                                    <th>NO KTP</th>
                                    <th>NAMA</th>
                                    <th>TANGGAL</th>
                                    <th data-orderable="false">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengajuan as $key => $value) : ?>
                                <tr>
                                    <td><?= $key+1 ?></td>
                                    <td><?= $value->KTP ?></td>
                                    <td><?= $value->NAMA ?></td>
                                    <td><?= $value->updated_at ?></td>
                                    <td></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tahap Survey</h5>
                </div>
                <div class="panel-body">
                    <div class="table-resposive">
                        <table class="table datatable-basic" style="font-size:90%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center" width="5%">NO</th>
                                    <th>NO KTP</th>
                                    <th>NAMA</th>
                                    <th>TANGGAL</th>
                                    <th data-orderable="false">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($survey as $key => $value) : ?>
                                <tr>
                                    <td><?= $key+1 ?></td>
                                    <td><?= $value->KTP ?></td>
                                    <td><?= $value->NAMA ?></td>
                                    <td><?= $value->updated_at ?></td>
                                    <td></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tahap Pengambilan Berkas</h5>
                </div>
                <div class="panel-body">
                    <div class="table-resposive">
                        <table class="table datatable-basic" style="font-size:90%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center" width="5%">NO</th>
                                    <th>NO KTP</th>
                                    <th>NAMA</th>
                                    <th>TANGGAL</th>
                                    <th data-orderable="false">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($berkas as $key => $value) : ?>
                                <tr>
                                    <td><?= $key+1 ?></td>
                                    <td><?= $value->KTP ?></td>
                                    <td><?= $value->NAMA ?></td>
                                    <td><?= $value->updated_at ?></td>
                                    <td></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-flat border-top-primary">
                <div class="panel-heading">
                    <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tahapan Selesai</h5>
                </div>
                <div class="panel-body">
                    <div class="table-resposive">
                        <table class="table datatable-basic" style="font-size:90%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center" width="5%">NO</th>
                                    <th>NO KTP</th>
                                    <th>NAMA</th>
                                    <th>TANGGAL</th>
                                    <th data-orderable="false">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selesai as $key => $value) : ?>
                                <tr>
                                    <td><?= $key+1 ?></td>
                                    <td><?= $value->KTP ?></td>
                                    <td><?= $value->NAMA ?></td>
                                    <td><?= $value->updated_at ?></td>
                                    <td></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
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
  });
</script>
@stop
