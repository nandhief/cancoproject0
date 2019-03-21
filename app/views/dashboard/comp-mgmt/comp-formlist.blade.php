@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left" ></i> <span class="text-semibold" style="color: #bb0a0a !important">Manajemen Lembaga</span></h4>
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
      <li><i class="icon-menu position-left"></i> General</a></li>
      <li class="active"><i class="icon-office"></i> Manajemen Lembaga</li>
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
  <form class="form-horizontal" id="formData" name="formData" enctype="multipart/form-data" method="post" action="<?php echo asset_url(); ?>/company">
    <div class="panel panel-flat border-top-primary">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tambah Lembaga</h5>
        <div class="heading-elements">
          <ul class="icons-list">
            <li><a data-action="collapse"></a></li>
          </ul>
        </div>
      </div>

      <div class="panel-body">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Lembaga</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><strong><i class="fa fa-institution"></i></strong></span>
              <input type="text" class="form-control first-input text-bold" id="prshId" name="prshId" placeholder="ID Lembaga...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Nama Lembaga</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-institution"></i></span>
              <input type="text" class="form-control" id="prshNama" name="prshNama" onkeyup="myFunction(); validHuruf(this)" placeholder="Nama Lembaga...">
            </div>
          </div>
        </div>

         <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Tipe Lembaga</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-institution"></i></span>
              <select class="form-control" id="prshTipe" name="prshTipe">
                <option value="">-- Pilih Tipe --</option>
                <option value="BPR">BPR</option>
                <option value="BPRS">BPRS</option>
                <option value="KJKS">KJKS</option>
                <option value="LKM">LKM</option>
              </select>
              <!-- <input type="text" class="form-control" id="prshTipe" name="prshTipe" placeholder="Tipe Perusahaan..."> -->
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Alamat</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-location4"></i></span>
              <input type="text" class="form-control" id="prshAlamat" name="prshAlamat" onkeyup="this.value = this.value.toUpperCase();" onclick="validasi()" placeholder="Alamat Lembaga...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kota</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-city"></i></span>
              <input type="text" class="form-control" id="prshKota" name="prshKota" onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)" placeholder="Kota...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Penanggung Jawab</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class=" icon-user-tie"></i></span>
              <input type="text" class="form-control" id="prshPICNama" name="prshPICNama" onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)" placeholder="Nama penanggung jawab...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">No Hp</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-phone2"></i></span>
              <input type="text" class="form-control" id="prshPICHp" name="prshPICHp" onkeypress="return hanyaAngka(event)" placeholder="No.telepon penanggung jawab...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">No Telepon</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-phone"></i></span>
              <div class="row">
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="prshPICTelpCode" name="prshPICTelpCode" placeholder="Kode Area" onkeypress="return hanyaAngka(event)">
                </div>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="prshPICTelp" name="prshPICTelp" placeholder="No.telepon Lembaga..." onkeypress="return hanyaAngka(event)">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Max Supervisor</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user-plus"></i></span>
              <select id="maxSupervisor" name="maxSupervisor" class="js-example-basic-single">
                <?php for ($i=0; $i < 21; $i++) { ?>
                   <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
              </select>
              <script type="text/javascript">
                $(document).ready(function() {
                  $('.js-example-basic-single').select2();
              });
              </script>
              <!-- <input type="text" class="form-control" id="maxSupervisor" name="maxSupervisor" placeholder="Max Input Supervisor"> -->
            </div>
          </div>
        </div>

       <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Max Collector</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user-minus"></i></span>
              <select id="maxCollector" name="maxCollector" class="js-example-basic-single">
                <?php for ($u=0; $u < 500; $u++) { ?>
                   <option value="<?php echo $u; ?>"><?php echo $u; ?></option>
                <?php } ?>
              </select>
              <!-- <script type="text/javascript">
                $(document).ready(function() {
                  $('.js-example-basic-single').select2();
              });
              </script> -->
              <!-- <input type="text" class="form-control" id="maxCollector" name="maxCollector" placeholder="Max Input Collector"> -->
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Logo Lembaga</label>
          <div class="col-lg-4">
            <input type="file" class="file-input-img" data-show-preview="false" data-show-upload="false" id="imgFile" name="imgFile" placeholder="File format JPG atau PNG...">
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-2">
            <button class="btn bg-blue-theme btn-block btn-labeled" onClick="saveData()"><b><i class="fa fa-check position-right"></i></b> Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-flat border-top-primary">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Data Perusahaan</h5>
        <div class="heading-elements">
          <ul class="icons-list">
            <li><a data-action="collapse"></a></li>
          </ul>
        </div>
      </div>
      <div class="panel-body">
        <table class="table datatable-basic">
          <thead>
            <tr>
              <th style="text-align:center" width="15%">ID</th>
              <th style="text-align:center" width="20%">Nama Perusahaan</th>
              <th style="text-align:center" width="13%">Aktif</th>
              <th style="text-align:center" width="15%">Jumlah Supervisor</th>
              <th style="text-align:center" width="15%">Jumlah Collector</th>
              <th style="text-align:center" width="15%">Data</th>
              <th style="text-align:center" width="15%">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(isset($ctlPrsh) && count($ctlPrsh) > 0) {
              foreach ($ctlPrsh as $aData) {
                if($aData->{"PRSH_STATUS_AKTIF"} == "Y")  $btnClass = "btn-success";
                if($aData->{"PRSH_STATUS_AKTIF"} == "T")  $btnClass = "btn-danger";
                ?>
                <tr>
                  <td><b><?php echo str_replace('PIN', '', $aData->{"PRSH_ID"}); ?></b></td>
                  <td><b><?php echo $aData->{"PRSH_NAMA"}; ?></b></td>
                  <td style="text-align:center">
                    <div id="divAktif_<?php echo $aData->{"PRSH_ID"}; ?>_Label" style="display:none"><span class="clickable" data-id="<?php echo $aData->{"PRSH_ID"}; ?>"><?php echo getReferenceInfo("DEFAULT_YES_NO",$aData->{"PRSH_STATUS_AKTIF"}); ?></span></div>
                    <div id="divAktif_<?php echo $aData->{"PRSH_ID"}; ?>_Edit" style="display:block">
                      <div class="btn-group">
                        <button type="button" class="btn <?php echo $btnClass; ?> btn-icon dropdown-toggle" data-toggle="dropdown">
                          <?php echo strtoupper(getReferenceInfo("DEFAULT_YES_NO",$aData->{"PRSH_STATUS_AKTIF"})); ?> &nbsp;<span class="caret"></span>
                        </button>
                        <ul class="custom dropdown-menu dropdown-menu-right" style="background-color:#4caf50;">
                          <li><a href="javascript:setStatus('AKTIF','<?php echo $aData->{"PRSH_ID"}; ?>')"><i class="fa fa-check"></i> Aktif</a></li>
                          <li><a href="javascript:setStatus('NON_AKTIF','<?php echo $aData->{"PRSH_ID"}; ?>')"><i class="fa fa-remove"></i> Non-Aktif</a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td style="text-align:center"><?php echo $aData->{"JUMLAH_SPV"}; ?></td>
                  <td style="text-align:center"><?php echo $aData->{"JUMLAH_COLL"}; ?></td>
                  <td style="text-align:center"><?php echo $aData->{"JUMLAH_DATA"}; ?></td>
                  <td style="text-align:center">
                    <div class="btn-group" >
                      <button type="button" class="btn btn-icon dropdown-toggle" data-toggle="dropdown" style="background-color: #bb0a0a !important; color: #fff">
                        <i class="icon-menu7"></i> &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right" style="background-color:#bb0a0a;">
                        <li><a href="javascript:editData('<?php echo $aData->{"PRSH_ID"}; ?>')" style="color: #fff;"><i class="fa fa-edit"></i> Ubah</a></li>
                        <li><a href="javascript:deleteData('<?php echo $aData->{"PRSH_ID"}; ?>')" style="color: #fff;"><i class="fa fa-remove"></i> Hapus</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
                <?php
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </form>
  <div class="footer text-muted"></div>

  <div id="mdlCompany_Edit" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #bb0a0a !important">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h6 class="modal-title" style="color: #fff"><i class="icon-user"></i> &nbsp; Edit Perusahaan</h6>
        </div>

        <div class="modal-body">
          <form class="form-horizontal" method="post" id="formPerusahaan" name="formPerusahaan" action="<?php echo asset_url(); ?>/company-update">
            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">ID Perusahaan</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-office"></i></span>
                  <input type="text" class="form-control first-input text-bold" name="prshId" id="prshIdEdit" placeholder="ID perusahaan..." readonly>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Nama Perusahaan</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                  <input type="text" class="form-control" id="prshNamaEdit" placeholder="Nama perusahaan..." onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Tipe Perusahaan</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                  <select class="form-control" id="prshTipeEdit">
                    <option value="BPR">BPR</option>
                    <option value="BPRS">BPRS</option>
                    <option value="KJKS">KJKS</option>
                    <option value="LKM">LKM</option>
                  </select>
                  <!-- <input type="text" class="form-control" id="prshTipeEdit" placeholder="Tipe perusahaan..."> -->
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Alamat</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-location4"></i></span>
                  <input type="text" class="form-control" id="prshAlamatEdit" placeholder="Alamat perusahaan..." onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Kota</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-city"></i></span>
                  <input type="text" class="form-control" id="prshKotaEdit" placeholder="Kota..." onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Penanggung Jawab</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class=" icon-user-tie"></i></span>
                  <input type="text" class="form-control" id="prshPICNamaEdit" placeholder="Nama penanggung jawab..." onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">No Hp</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class=" icon-phone"></i></span>
                  <input type="text" class="form-control" id="prshPICHpEdit" name="prshPICHp" placeholder="No.telepon penanggung jawab..." onkeypress="return hanyaAngka(event)">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Telepon</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-phone"></i></span>
                  <div class="row">
                    <div class="col-sm-4">
                      <input type="text" class="form-control" id="prshPICTelpCodeEdit" placeholder="Kode Area" onkeypress="return hanyaAngka(event)">
                    </div>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="prshPICTelpEdit" placeholder="No.telepon Lembaga..." onkeypress="return hanyaAngka(event)">
                    </div>
                  </div>
                </div>
                {{-- <div class="input-group">
                  <span class="input-group-addon">(0274)</span>
                  <input type="text" class="form-control" id="prshPICTelpEdit" placeholder="No.telepon Perusahaan..." onkeypress="return hanyaAngka(event)">
                </div> --}}
              </div>
            </div>
            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Max Supervisor</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-user-plus"></i></span>
                 <!--  <input type="number" class="form-control" id="maxSupervisorE"  placeholder="Max Input Supervisor...."> -->
                  <select id="maxSupervisorE" name="maxSupervisor" class="js-example-basic-single">
                    <option></option>
                     <?php foreach($maxSpve as $cth) { ?>
                      <option value="<?php $cth->{"PRSH_MAX_SPV"}; ?>"<?php if($cth->{"PRSH_MAX_SPV"}) {echo "selected";} ?>><?php echo $cth->{"PRSH_MAX_SPV"}; ?></option>
                    <?php for ($i=1; $i < 21; $i++) { ?>
                       <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
                </div>
              </div>
            </div>

           <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Max Collector</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-user-minus"></i></span>
                  <!-- <input type="number" class="form-control" id="maxCollectorE" placeholder="Max Input Collector...."> -->
                  <select id="maxCollectorE" name="maxCollector" class="js-example-basic-single">
                    <option></option>
                    <?php foreach($maxSpve as $cth) { ?>
                      <option value="<?php $cth->{"PRSH_MAX_COLLECT"}; ?>"<?php if($cth->{"PRSH_MAX_COLLECT"}) {echo "selected";} ?>><?php echo $cth->{"PRSH_MAX_COLLECT"}; ?></option>
                    <?php for ($u=1; $u < 500; $u++) { ?>
                       <option value="<?php echo $u; ?>"><?php echo $u; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
          <button type="button" class="btn btn-primary btn-labeled btn-xs" onClick="updateData();" id="btnUpdateCompany" data-prsh-id=""><b><i class="icon-checkmark2"></i></b> Simpan </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/form_select2.js"></script>
<script type="text/javascript">
  // Select with search
  $('.select-search').select2();
</script>
<script type="text/javascript">
  function required(target) {
    toastr.options = {
      "positionClass": "toast-top-right"
    }
    toastr.error('Harus diisi', target);
    return false;
  }

  function hanyaAngka(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;
    return true;
    if(charCode.length != 10) {
      alert("Nomer Telpon harus 11 digit");
    }
  }

  function myFunction() {
    var x = document.getElementById("prshNama");
    x.value = x.value.toUpperCase();
  }

  function cekuser(a) {
    valid = /^[A-Za-z]{1,}$/;
    return valid.test(a);
  }
function validasi(){
   var nama = document.getElementById("prshAlamat").value;
   if(nama == ""){
   // alert("Alamat harus diisi");
   // }else if (!cekuser(nama)) {
   // alert("Isi Dengan Huruf");
   nama.focus();
   return false;
   }else{
   }
}
  function validHuruf(a) {
    if(!/^[A-Za-z ]+$/.test(a.value)) {
      a.value = a.value.substring(0,a.value.length-1000)
    }
  }
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
            search: '<span>Cari &nbsp;</span> _INPUT_',
            lengthMenu: '<span>Tampil &nbsp;</span> _MENU_',
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
        "order": [[ 0, "desc" ]]
    });

    // External table additions
    // ------------------------------
    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder','Kata kunci...');

    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: "-1"
    });

    $('.file-input-img').fileinput({
      browseLabel: '',
      browseClass: 'btn btn-primary btn-icon',
      removeLabel: '',
      uploadLabel: '',
      uploadClass: 'btn btn-default btn-icon',
      browseIcon: '<i class="icon-plus22"></i> ',
      uploadIcon: '<i class="icon-file-upload"></i> ',
      removeClass: 'btn btn-danger btn-icon',
      removeIcon: '<i class="icon-cancel-square"></i> ',
      layoutTemplates: {
          caption: '<div tabindex="-1" class="form-control file-caption {class}">\n' + '<span class="icon-file-plus kv-caption-icon"></span><div class="file-caption-name"></div>\n' + '</div>'
      },
      initialCaption: "Pilih file JPG atau PNG",
      /*initialCaption: "Pilih file",*/
      allowedFileExtensions: ["jpg", "jpeg", "png"]
    });
  });

  var gBusy = false;

  $(".first-input").focus();
  $("#prshId").val("<?php echo $ctlUrutan ?>");

  /*Cek kode lembaga*/
  var checkLembaga = function () {
      $.ajax({
        type: 'POST',
        url: "<?= asset_url(); ?>/company/check",
        data: {
          prshId: $('#prshId').val()
        },
        success: function (res) {
          toastr.options = {
            "positionClass": "toast-top-right"
          }
          var data = $.parseJSON(res);
          if (data.STATUS == 'SUCCESS') {
            toastr.success(data.MESSAGE, 'Berhasil');
          }
          if (data.STATUS == 'ERROR') {
            toastr.error(data.MESSAGE, 'Perhatian');
          }
        },
        error: function (res) {
          console.log(res);
        }
      });
  }
  $('#prshId').load(function () {
    if ($(this).val().length > 0) {
        checkLembaga();
    }
  });
  $('#prshId').blur(function () {
    if ($(this).val().length > 0) {
        checkLembaga();
    }
  });

  var editData = function(prshId) {
    $("#btnUpdateCompany").attr("data-prsh-id", "");
    createOverlay("Mohon Tunggu...");
    $.ajax({
      type  : "GET",
      url   : "<?php echo asset_url(); ?>/company/detail",
      data  : "id=" + prshId,
      success : function(result) {
        gOverlay.hide();
        var data = JSON.parse(result);

        if(data["STATUS"] == "SUCCESS") {
          var prsh = data["PAYLOAD"]["PRSH_ID"];
          $('#mdlCompany_Edit').on('shown.bs.modal', function() {
            $("#prshIdEdit").val(prsh.replace('PIN', ''));
            $("#prshNamaEdit").val(data["PAYLOAD"]["PRSH_NAMA"]);
            $("#prshTipeEdit").val(data["PAYLOAD"]["PRSH_JENIS_TIPE"]);
            $("#prshAlamatEdit").val(data["PAYLOAD"]["PRSH_ALAMAT"]);
            $("#prshKotaEdit").val(data["PAYLOAD"]["PRSH_KOTA"]);
            $("#prshPICNamaEdit").val(data["PAYLOAD"]["PRSH_PIC_NAMA"]);
            var telp = data["PAYLOAD"]["PRSH_PIC_TELP"];
            var dataTelp = telp.split('-');
            // $("#prshPICTelpEdit").val(data["PAYLOAD"]["PRSH_PIC_TELP"]);
            $("#prshPICTelpCodeEdit").val(dataTelp[0]);
            $("#prshPICTelpEdit").val(dataTelp[1]);
            $("#prshPICHpEdit").val(data["PAYLOAD"]["PRSH_TELP"]);
            $("#maxSupervisorE").val(data["PAYLOAD"]["PRSH_MAX_SPV"]).trigger("change");
            $("#maxCollectorE").val(data["PAYLOAD"]["PRSH_MAX_COLLECT"]).trigger("change");

            $("#btnUpdateCompany").attr("data-prsh-id", data["PAYLOAD"]["PRSH_ID"]);

            $("#prshNamaEdit").focus();
          })

          $("#mdlCompany_Edit").modal("show");
          console.log(data);
        }
        else {
          //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
          toastr.error(data["MESSAGE"]);
        }
      },
      error : function(error) {
        gOverlay.hide();
        alert("Network/server error\r\n" + error);
      }
    });
  }

  var updateData = function() {
    //console.log();
    var prshId = $("#btnUpdateCompany").attr("data-prsh-id");

    if(prshId != "") {
        if (!$('#prshNamaEdit').val().length) {
          required('Kolom Perusahaan');
          $('#prshNamaEdit').focus();
          return false;
        }
        if (!$('#prshTipeEdit').val().length) {
          required('Kolom Tipe');
          $('#prshTipeEdit').focus();
          return false;
        }
        if (!$('#prshAlamatEdit').val().length) {
          required('Kolom Alamat');
          $('#prshAlamatEdit').focus();
          return false;
        }
        if (!$('#prshKotaEdit').val().length) {
          required('Kolom Kota');
          $('#prshKotaEdit').focus();
          return false;
        }
        if (!$('#prshPICNamaEdit').val().length) {
          required('Kolom Penanggung Jawab');
          $('#prshPICNamaEdit').focus();
          return false;
        }
        if (!$('#prshPICHpEdit').val().length) {
          required('Kolom Hp');
          $('#prshPICHpEdit').focus();
          return false;
        } else if ($('#prshPICHpEdit').val().length < 10) {
          toastr.error('Minimal Nomor harus 10, Periksa nomor anda', 'Kolom Hp');
          $('#prshPICHpEdit').focus();
          return false;
        }
        if (!$('#prshPICTelpCodeEdit').val().length) {
          required('Kolom Kode Area Telepon');
          $('#prshPICTelpCodeEdit').focus();
          return false;
        } else if ($('#prshPICTelpCodeEdit').val().length < 3 || $('#prshPICTelpCodeEdit').val().length > 4) {
          toastr.error('Minimal Nomor harus 3 maximal 4, Periksa nomor anda', 'Kolom Kode Area Telepon');
          $('#prshPICTelpCodeEdit').focus();
          return false;
        }
        if (!$('#prshPICTelpEdit').val().length) {
          required('Kolom Telepon');
          $('#prshPICTelpEdit').focus();
          return false;
        } else if ($('#prshPICTelpEdit').val().length < 6) {
          toastr.error('Minimal Nomor harus 6, Periksa nomor anda', 'Kolom Telepon');
          $('#prshPICTelpEdit').focus();
          return false;
        }
      var prshIdX = $("#prshIdEdit").val();
      var prshNamaX = $("#prshNamaEdit").val();
      var prshTipeX = $("#prshTipeEdit").val();
      var prshAlamatX = $("#prshAlamatEdit").val();
      var prshKotaX = $("#prshKotaEdit").val();
      var prshPICNamaX = $("#prshPICNamaEdit").val();
      var prshPICTelpX = [$("#prshPICTelpCodeEdit").val(), $("#prshPICTelpEdit").val()].join('-');
      var prshTelpX = $("#prshPICHpEdit").val();
      var prshMaxSupervisorX = $("#maxSupervisorE").val();
      var prshMaxCollectorX = $("#maxCollectorE").val();

      //console.log("data " + prshTelpEditX);
      if(prshNamaX != "") {
        createOverlay("Mohon Tunggu...");
        $.ajax({
          type  : "POST",
          url   : "<?php echo asset_url(); ?>/company-update",
          data  : "prshId=PIN" + encodeURI(prshIdX) + "&prshNama=" + encodeURI(prshNamaX) + "&prshTipe=" + encodeURI(prshTipeX) + "&prshAlamat=" + encodeURI(prshAlamatX) + "&prshKota=" + encodeURI(prshKotaX) + "&prshPICNama=" + encodeURI(prshPICNamaX) + "&prshPICTelp=" + encodeURI(prshPICTelpX) + "&prshMaxSupervisor=" + encodeURI(prshMaxSupervisorX) + "&prshMaxCollector=" + encodeURI(prshMaxCollectorX) + "&prshTelp=" + encodeURI(prshTelpX),
            success : function(result) {
            gOverlay.hide();
            var data = JSON.parse(result);
            //console.log(prsh);

            if(data["STATUS"] == "SUCCESS") {
              toastr.success(data["MESSAGE"]);
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/company";
              }, 500);
            }
            else {
              //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
              //toastr.error(data["MESSAGE"]);
              swal({
                title: "GAGAL",
                text: data["MESSAGE"],
                type: "error",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "OK",
                closeOnConfirm: false,
                html: true
              },
              function(){
                setTimeout(function(){
                  window.location = "<?php echo asset_url(); ?>/company";
                }, 500);
              });
            }
          },
          error : function(error) {
            gOverlay.hide();
            alert("Gangguan pada server/jaringan\r\n" + error);
          }
        });
      }
      else {
        toastr.error("Isikan data dengan lengkap");
      }
    }
  }
  /*
  var saveData = function(){
    var prshId = $("#prshId").val();
    var prshNama = $("#prshNama").val();
    var prshAlamat = $("#prshAlamat").val();
    var prshKota = $("#prshKota").val();
    var prshPICNama = $("#prshPICNama").val();
    var prshPICTelp = $("#prshPICTelp").val();

    if(prshId != "" && prshNama != "" && prshAlamat != "" && prshKota != "" && prshPICNama != "" && prshPICTelp != "") {
      createOverlay("Proses...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/company",
        data  : "prshId=" + encodeURI(prshId) + "&prshNama=" + encodeURI(prshNama) + "&prshAlamat=" + encodeURI(prshAlamat) + "&prshKota=" + encodeURI(prshKota) + "&prshPICNama=" + encodeURI(prshPICNama) + "&prshPICTelp=" + encodeURI(prshPICTelp),
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/company";
            }, 500);
          }
          else {
            //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
            //toastr.error(data["MESSAGE"]);
            swal({
              title: "GAGAL",
              text: data["MESSAGE"],
              type: "error",
              showCancelButton: false,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "OK",
              closeOnConfirm: false,
              html: true
            },
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/company";
              }, 500);
            });
          }
        },
        error : function(error) {
          gOverlay.hide();
          alert("Gangguan pada server/jaringan\r\n" + error);
        }
      });
    }
    else {
      toastr.error("Harap isi data dengan lengkap");
    }
  }
  */
  var deleteData = function(slug){
    //console.log(slug)
    swal({
      title: "Konfirmasi",
      text: "Hapus data perusahaan ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya",
      cancelButtonText : "Tidak",
      closeOnConfirm: true,
      html: true
    },
    function(){
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/company/delete",
        data  : "id=" + slug,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/company";
            }, 300);
          }
          else {
            //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
            //toastr.error(data["MESSAGE"]);
            swal({
              title: "GAGAL",
              text: data["MESSAGE"],
              type: "error",
              showCancelButton: false,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "OK",
              closeOnConfirm: false,
              html: true
            },
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/company";
              }, 500);
            });
          }
        },
        error : function(error) {
          gOverlay.hide();
          alert("Gangguan pada server/jaringan\r\n" + error);
        }
      });
    });
  }

  var setStatus = function(prshStatus,slug){
    swal({
      title: "Konfirmasi",
      text: "Ubah status perusahaan ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya",
      cancelButtonText : "Tidak",
      closeOnConfirm: true,
      html: true
    },
    function(){
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/company/update-status",
        data  : "id=" + slug + "&prshStatus=" + encodeURI(prshStatus) ,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/company";
            }, 300);
          }
          else {
            //sweetAlert("Pesan Kesalahan", data["MESSAGE"], "error");
            //toastr.error(data["MESSAGE"]);
            swal({
              title: "GAGAL",
              text: data["MESSAGE"],
              type: "error",
              showCancelButton: false,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "OK",
              closeOnConfirm: false,
              html: true
            },
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/company";
              }, 500);
            });
          }
        },
        error : function(error) {
          gOverlay.hide();
          alert("Gangguan pada server/jaringan\r\n" + error);
        }
      });
    });
  }

  var saveData = function() {
    if(!gBusy) {
      gBusy = true;
      //- content action form
      var optFormContentUpdate = {
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
              window.location = "<?php echo asset_url(); ?>/company";
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
                window.location = "<?php echo asset_url(); ?>/company";
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
            }/*,
            function(){
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/company";
              }, 500);
            }*/);
          }
          //$(".btn").removeAttr("disabled");
          //window.location = "<?php echo asset_url(); ?>/company";
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          // window.location = "<?php echo asset_url(); ?>/company";
        }
      };
      $('#formData').submit(function() {
        if (!$('#prshNama').val().length) {
          required('Kolom Perusahaan');
          $('#prshNama').focus();
          return false;
        }
        if (!$('#prshTipe').val().length) {
          required('Kolom Tipe');
          $('#prshTipe').focus();
          return false;
        }
        if (!$('#prshAlamat').val().length) {
          required('Kolom Alamat');
          $('#prshAlamat').focus();
          return false;
        }
        if (!$('#prshKota').val().length) {
          required('Kolom Kota');
          $('#prshKota').focus();
          return false;
        }
        if (!$('#prshPICNama').val().length) {
          required('Kolom Penanggung Jawab');
          $('#prshPICNama').focus();
          return false;
        }
        if (!$('#prshPICHp').val().length) {
          required('Kolom Hp');
          $('#prshPICHp').focus();
          return false;
        } else if ($('#prshPICHp').val().length < 10) {
          toastr.error('Minimal Nomor harus 10, Periksa nomor anda', 'Kolom Hp');
          $('#prshPICHp').focus();
          return false;
        }
        if (!$('#prshPICTelpCode').val().length) {
          required('Kolom Kode Area Telepon');
          $('#prshPICTelpCode').focus();
          return false;
        } else if ($('#prshPICTelpCode').val().length < 3 || $('#prshPICTelpCode').val().length > 4) {
          toastr.error('Minimal Nomor harus 3 maximal 4, Periksa nomor anda', 'Kolom Kode Area Telepon');
          $('#prshPICTelpCode').focus();
          return false;
        }
        if (!$('#prshPICTelp').val().length) {
          required('Kolom Telepon');
          $('#prshPICTelp').focus();
          return false;
        } else if ($('#prshPICTelp').val().length < 6) {
          toastr.error('Minimal Nomor harus 6, Periksa nomor anda', 'Kolom Telepon');
          $('#prshPICTelp').focus();
          return false;
        }
        $(this).ajaxSubmit(optFormContentUpdate);
        // return false to prevent standard browser submit and page navigation
        return false;
      });
    }
  }
</script>
@stop
