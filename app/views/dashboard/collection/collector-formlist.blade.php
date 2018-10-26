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
      <li class="active"><i class="icon-users4"></i> Data Collector</li>
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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Unggah File Data Collector</h5>
      <div class="heading-elements">
        <ul class="icons-list">
          <li><a href="<?php echo asset_url(); ?>/assets/documents/sample_collector.xlsx"><i class="  icon-download4"></i> Unduh File Contoh</a></li>
          <li><a data-action="collapse"></a></li>
        </ul>
      </div>
      <!--
      <div class="heading-elements">
        <ul class="icons-list">
          <li><a data-action="collapse"></a></li>
          <li><a data-action="reload"></a></li>
          <li><a data-action="close"></a></li>
        </ul>
      </div>
      //-->
    </div>

    <div class="panel-body">
      <p class="content-group">
        Unggah file Excel berisi petugas collector.
      </p>

      <p class="content-group">
        Harap pastikan isi dan format file sudah benar. Setelah diunggah, petugas akan bisa login menggunakan user ID dan password default.
      </p>

      <form class="form-horizontal" enctype="multipart/form-data" method="post" id="formUploadData" name="formUploadData" action="<?php echo asset_url(); ?>/collection/collector">
        <div class="form-group">
          <!--<label class="col-lg-2 control-label text-semibold">File Data Format Excel</label>-->
          <div class="col-lg-4">
            <input type="file" class="file-input" data-show-preview="false" data-show-upload="false" id="collFile" name="collFile" placeholder="File format Excel...">
          </div>
        </div>

        <div class="form-group">
          <!--label class="col-lg-2 control-label text-semibold">&nbsp;</label-->
          <div class="col-lg-4">
            <!-- onClick="saveContent()" -->
            <button onclick="saveContent()" class="btn bg-blue-theme btn-block">Unggah File<i class="fa fa-upload position-right"></i></button>
          </div>
        </div>
      </form>
      <!--
      <div class="form-group">
        <label class="col-lg-2 control-label text-semibold">File Data Format .xls/.xlsx</label>
        <div class="col-lg-4">
          <input type="file" class="file-input" multiple="multiple" data-show-upload="false" name="input001_multi[]" id="input001_multi">
          <span class="help-block">Anda bisa memilih beberapa file sekaligus untuk diunggah.</span>
          <button type="submit" class="btn btn-primary" id="btnSubmit" disabled><i class="icon-arrow-right14"></i> Simpan</button>
        </div>
      </div>
      //-->
    </div>
  </div>

  <div class="panel panel-flat border-top-primary">
    <form class="form-horizontal" method="post" id="formData" name="formData">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Tambah Collector</h5>
        <div class="heading-elements">
          <ul class="icons-list">
            <li><a data-action="collapse"></a></li>
          </ul>
        </div>
      </div>

      <div class="panel-body">
        <input type="hidden" id="userPrsh" value="<?php echo $ctlUserData->{"PRSH_ID"}; ?>">
        <input type="hidden" id="userGroup" value="GR_COLLECTOR">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">User ID</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" class="form-control first-input" id="userBigId" name="userBigId" onkeypress="return hanyaAngka(event)" placeholder="User ID..." value="">

            </div>
            <p id="nomor" class="invalid" style="color: red;"><span>* Isian berupa angka</span></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Username</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" class="form-control first-input" onkeyup="this.value = this.value.toUpperCase();" id="userId" name="userId" placeholder="Username...">

            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Password</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
              <input type="text" class="form-control" id="userPass" name="userPass" value="12345678" readonly required>
              {{-- <span class="input-group-addon"><input type="checkbox" id="showPass"> Show</span> --}}
              <style type="text/css">
                #message {display: none; background: #f1f1f1; color: #000; position: relative; padding: 5px; margin-top: 5px; } #message p {padding: 5px 10px; font-size: 10px; } /* Add a green text color and a checkmark when the requirements are right */ .valid {display: none; color: green; } .valid:before {position: relative; left: -35px; content: "&#10004;"; } /* Add a red text color and an "x" icon when the requirements are wrong */ .invalid {color: red; } .invalid:before {position: relative; left: -35px; }
              </style>
            </div>
            {{-- <p id="complete" class="invalid">* Password harus mengandung <span id="letter" class="invalid">huruf kecil, </span><span id="capital" class="invalid">huruf besar, </span><span id="number" class="invalid">angka, </span><span id="length" class="invalid">min. 6 karakter</span></p> --}}
            <script type="text/javascript">
              $(function(){
                  $("#showPass").click(function(){ // #showPass -> id Checkbox
                    if($("[name=userPass]").attr('type')=='password'){
                      $("[name=userPass]").attr('type','text');
                    }else{
                      $("[name=userPass]").attr('type','password');
                    }
                  });
              });
            </script>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Nama</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-profile"></i></span>
              <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)" id="userNama" name="userNama" placeholder="Nama user...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">No.Ponsel</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-phone2"></i></span>
              <input type="text" class="form-control" id="userPonsel" min="13" name="userPonsel" onkeypress="return hanyaAngka(event)" placeholder="Nomor ponsel user...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Email</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-mention"></i></span>
              <input type="email" class="form-control" id="userEmail" name="userEmail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" placeholder="Email user...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Group Pinjaman</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-mention"></i></span>
              <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase();" id="userKode" name="userKode" placeholder="Kode group user pinjaman...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Group Tabungan</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-gear"></i></span>
              <input type="text" class="form-control" id="userKodeTabungan" name="userKodeTabungan" placeholder="Kode Group Tabungan...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Collect Pinjaman</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-gear"></i></span>
              <select class="form-control" id="status_collect" name="status_collect">
                <option value="0">-- Pilih Tipe --</option>
                <option value="1">Collect Pinjaman</option>
              </select>
              <!-- <input type="text" class="form-control" id="prshTipe" name="prshTipe" placeholder="Tipe Perusahaan..."> -->
            </div>
          </div>
        </div>

         <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Collect Tabungan</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-gear"></i></span>
              <select class="form-control" id="status_tab" name="status_tab">
                <option value="0">-- Pilih Tipe --</option>
                <option value="1">Collect Tabungan</option>
              </select>
              <!-- <input type="text" class="form-control" id="prshTipe" name="prshTipe" placeholder="Tipe Perusahaan..."> -->
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-2">
            <button type="button" name="submit" class="btn bg-blue-theme btn-block btn-labeled" onClick="saveData()"><b><i class="fa fa-check position-right"></i></b> Simpan</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="panel panel-flat border-top-primary">
    <div class="panel-heading">
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Data Collector</h5>
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
             <th style="text-align:center" width="18%">User BigID</th>
            <th style="text-align:center" width="18%">Username</th>
            <th style="text-align:center" width="20%">Nama</th>
            <th style="text-align:center" width="15%">Status</th>
            <th style="text-align:center;" width="20%">Kode Group Pinjaman</th>
            <th style="text-align:center;" width="20%">Kode Group Tabungan</th>
            <th style="text-align:center" width="15%">Group Role</th>
            <th style="text-align:center" width="13%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if(isset($ctlCollectors) && count($ctlCollectors) > 0) {
            foreach ($ctlCollectors as $aData) {
              if($aData->{"U_STATUS"} == "USER_ACTIVE")    $btnClass = "btn-success";
              if($aData->{"U_STATUS"} == "USER_INACTIVE")  $btnClass = "btn-danger";
              ?>
              <tr>
                <td style="text-align: center;"><b><?php echo $aData->{"USERBIGID"}; ?></b></td>
                <td><b><?php echo $aData->{"U_ID"}; ?></b></td>
                <td><?php echo $aData->{"U_NAMA"}; ?></td>
                <td style="text-align:center">
                  <div id="divAktif_<?php echo $aData->{"U_ID"}; ?>_Label" style="display:none"><span class="clickable" data-id="<?php echo $aData->{"U_ID"}; ?>"><?php echo getReferenceInfo("USER_STATUS",$aData->{"U_STATUS"}); ?></span></div>
                  <div id="divAktif_<?php echo $aData->{"U_ID"}; ?>_Edit" style="display:block">
                    <div class="btn-group">
                      <button type="button" class="btn <?php echo $btnClass; ?> btn-icon dropdown-toggle" data-toggle="dropdown">
                        <?php echo strtoupper(getReferenceInfo("USER_STATUS",$aData->{"U_STATUS"})); ?> &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="custom dropdown-menu dropdown-menu-right" style="background-color:#4caf50;">
                        <li><a href="javascript:setStatus('AKTIF','<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-check"></i> Aktif</a></li>
                        <li><a href="javascript:setStatus('NON_AKTIF','<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-remove"></i> Non-Aktif</a></li>
                        <!--li><a href="javascript:setStatus('SUSPEND','<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-ban"></i> Suspend</a></li-->
                      </ul>
                    </div>
                  </div>
                </td>
                <td style="text-align: center;"><?php echo $aData->{"U_KODE_GROUP"}; ?></td>
                <td style="text-align: center;"><?php echo $aData->{"U_KODE_TABUNGAN"}; ?></td>
                <td style="text-align:center"><?php echo getReferenceInfo("GROUP_ROLE",$aData->{"U_GROUP_ROLE"}); ?></td>
                <td style="text-align:center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-icon dropdown-toggle" data-toggle="dropdown" style="background-color: #bb0a0a !important; color: #fff">
                        <i class="icon-menu7"></i> &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right" style="background-color:#bb0a0a;">
                        <li><a href="javascript:showDetail('<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-ellipsis-h"></i> Detail</a></li>
                        <li><a href="javascript:editData('<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-edit"></i> Ubah</a></li>
                        <li><a href="javascript:resetPassword('<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-retweet"></i> Reset Password</a></li>
                        <li><a href="javascript:gantiPassword('<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-retweet"></i> Ganti Password</a></li>
                        <li><a href="javascript:deleteData('<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-remove"></i> Hapus</a></li>
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
</div>

<div id="mdlUser_Edit" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #bb0a0a !important">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h6 class="modal-title"><i class="icon-user" style="color: #fff"></i> &nbsp; Edit User</h6>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="post" id="formCost" name="formCost" action="">
          <input type="hidden" id="userPrshEdit" value="<?php echo $ctlUserData->{"PRSH_ID"}; ?>">
          <input type="hidden" id="userGroupEdit" value="GR_COLLECTOR">

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">User BigID</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" id="userBigIdEdit" class="form-control" onkeypress="return hanyaAngka(event)" readonly>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Username</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" id="userIdEdit" class="form-control" readonly>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Nama</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-profile"></i></span>
                <input type="text" id="userNamaEdit" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">No.Ponsel</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-phone2"></i></span>
                <input type="text" id="userPonselEdit" class="form-control" onkeypress="return hanyaAngka(event)">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Email</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-mention"></i></span>
                <input type="text" class="form-control" id="userEmailEdit" name="userEmailEdit"  placeholder="Email user...">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Kode Group Pinjaman</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-mention"></i></span>
                <input type="text" class="form-control" id="userKodeEdit" name="userKodeEdit" placeholder="Kode Group user..." data-pinjaman="">
              </div>
            </div>
          </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Kode Group Tabungan</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-mention"></i></span>
                  <input type="text" class="form-control" id="userKodeTabunganEdit" name="userKodeTabunganEdit" placeholder="Kode Group Tabungan..." data-tabungan="">
                </div>
              </div>
            </div>

           <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Collect Pinjaman</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-gear"></i></span>
                <select class="form-control" id="status_collectEdit" name="status_collectEdit">
                  <option value="0">-- Pilih Tipe --</option>
                  <option value="1">Collect Pinjaman</option>
                </select>
                <!-- <input type="text" class="form-control" id="prshTipe" name="prshTipe" placeholder="Tipe Perusahaan..."> -->
              </div>
            </div>
          </div>

           <div class="form-group">
            <label class="col-lg-4 control-label text-semibold">Collect Tabungan</label>
            <div class="col-lg-8">
              <div class="input-group">
                <span class="input-group-addon"><i class="icon-gear"></i></span>
                <select class="form-control" id="status_tabEdit" name="status_tabEdit">
                  <option value="0">-- Pilih Tipe --</option>
                  <option value="1">Collect Tabungan</option>
                </select>
                <!-- <input type="text" class="form-control" id="prshTipe" name="prshTipe" placeholder="Tipe Perusahaan..."> -->
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
        <button type="button" class="btn btn-primary btn-labeled btn-xs" onClick="updateData();" id="btnUpdateUser" data-user-id=""><b><i class="icon-checkmark2"></i></b> Simpan </button>
      </div>
    </div>
  </div>
</div>

<div id="gantiPassword" class="modal fade">
  <div class="modal-dialog modal-sm" style="background-color: #fff !important">
    <div class="modal-header" style="background-color: #bb0a0a !important">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h6 class="modal-title" style="color: #fff"><i class="fa fa-asterisk"></i> &nbsp; Ganti Password <span id="user_name"></span></h6>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label for="">Ganti Password</label>
        <input type="hidden" name="user_id" id="user_id">
        <div class="input-group">
          <input type="password" name="ganti_password" id="ganti_password" class="form-control valIsi valNum valLow valUp">
          <span class="input-group-addon"><input type="checkbox" id="showGantiPassword"> Show</span>
        </div>
        <p class="invalid ganti_password">* Password harus mengandung <span class="invalid lower">huruf kecil, </span><span class="invalid capital">huruf besar, </span><span class="invalid number">angka, </span><span class="invalid length">min. 6 karakter</span></p>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
      <button type="button" class="btn btn-primary btn-labeled btn-xs" onClick="updatePassword();" id="btnUpdatePassword"><b><i class="icon-checkmark2"></i></b> Simpan </button>
    </div>
  </div>
</div>

<div class="footer text-muted"></div>

<!-- Theme JS files -->
<script type="text/javascript">
  // Select with search
  //$('.select').select2();
</script>

<script type="text/javascript">
  function hanyaAngka(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))

      return false;
    return true;
  }

  function cekEmail(nilai, pesan) {
    //console.log(nilai);
    var email = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    if(nilai.value.match(email)) {
      return true;
    }
    else {
      alert(pesan);
      nilai.focus();
      return false;
    }
  }
</script>


<script type="text/javascript">
  $('#ganti_password').keyup(function () {
    if ($(this).val().match(/[a-z]/g)) {
      $('.lower').removeClass('invalid');
      $('.lower').addClass('valid');
      var lower = true;
    } else {
      $('.lower').removeClass('valid');
      $('.lower').addClass('invalid');
      var lower = false;
    }
    if ($(this).val().match(/[A-Z]/g)) {
      $('.capital').removeClass('invalid');
      $('.capital').addClass('valid');
      var capital = true;
    } else {
      $('.capital').removeClass('valid');
      $('.capital').addClass('invalid');
      var capital = false;
    }
    if ($(this).val().match(/[0-9]/g)) {
      $('.number').removeClass('invalid');
      $('.number').addClass('valid');
      var number = true;
    } else {
      $('.number').removeClass('valid');
      $('.number').addClass('invalid');
      var number = false;
    }
    if ($(this).val().length >= 6) {
      $('.length').removeClass('invalid');
      $('.length').addClass('valid');
      var length = true;
    } else {
      $('.length').removeClass('valid');
      $('.length').addClass('invalid');
      var length = false;
    }
    if (lower && capital && number && length) {
      $('.ganti_password').removeClass('invalid');
      $('.ganti_password').addClass('valid');
    } else {
      $('.ganti_password').removeClass('valid');
      $('.ganti_password').addClass('invalid');
    }
  });
  $('#showGantiPassword').click(function() {
    if ($('#ganti_password').attr('type') == 'password') {
      $('#ganti_password').attr('type', 'text');
    } else {
      $('#ganti_password').attr('type', 'password');
    }
  });
                  var myInput = document.getElementById("userPass");
                  var letter = document.getElementById("letter");
                  var capital = document.getElementById("capital");
                  var number = document.getElementById("number");
                  var length = document.getElementById("length");
                  var complete = document.getElementById("complete");

                  // When the user clicks on the password field, show the message box
                  myInput.onfocus = function() {
                    document.getElementById("message").style.display = "block";
                  }

                  // When the user clicks outside of the password field, hide the message box
                  myInput.onblur = function() {
                    document.getElementById("message").style.display = "none";
                  }

                  // When the user starts to type something inside the password field
                  // var x = document.getElementById("userPass");
                  myInput.onkeyup = function() {
                    // Validate lowercase letters
                    var lowerCaseLetters = /[a-z]/g;
                    if(myInput.value.match(lowerCaseLetters)) {
                      letter.classList.remove("invalid");
                      letter.classList.add("valid");
                      var hurufKecil = true;
                    } else {
                      letter.classList.remove("valid");
                      letter.classList.add("invalid");
                      var hurufKecil = false;
                  }

                    // Validate capital letters
                    var upperCaseLetters = /[A-Z]/g;
                    if(myInput.value.match(upperCaseLetters)) {
                      capital.classList.remove("invalid");
                      capital.classList.add("valid");
                      var hurufBesar = true;
                    } else {
                      capital.classList.remove("valid");
                      capital.classList.add("invalid");
                      var hurufBesar = false;
                    }

                    // Validate numbers
                    var numbers = /[0-9]/g;
                    if(myInput.value.match(numbers)) {
                      number.classList.remove("invalid");
                      number.classList.add("valid");
                      var nomor = true;
                    } else {
                      number.classList.remove("valid");
                      number.classList.add("invalid");
                      var nomor = false;
                    }

                    // Validate length
                    if(myInput.value.length >= 6) {
                      length.classList.remove("invalid");
                      length.classList.add("valid");
                      var panjang = true;
                    } else {
                      length.classList.remove("valid");
                      length.classList.add("invalid");
                      var panjang = false;
                    }

                    // Validate complete
                    if(myInput.value.length >= 6 && myInput.value.match(numbers) && myInput.value.match(upperCaseLetters) && myInput.value.match(lowerCaseLetters)) {
                      complete.classList.remove("invalid");
                      complete.classList.add("valid");
                      var lengkap = true;
                    } else {
                      complete.classList.remove("valid");
                      complete.classList.add("invalid");
                      var lengkap = false;
                    }
                  }
</script>
<script type="text/javascript">
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
        "order": [[ 0, "desc" ]]/*,
        scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        fixedColumns:   {
          leftColumns: 1,
          rightColumns: 1
        }*/
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
  $(".first-input").focus();
  // $("#userBigId").val("<?php echo $ctrlUrutan ?>");

  function required(target) {
    toastr.options = {
      "positionClass": "toast-top-right"
    }
    toastr.error('Harus diisi', target);
    return false;
  }

  function saveContent() {
    //console.log();
    if(!gBusy) {
      console.log();
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
          //console.log("success");
          gBusy = false;
          gOverlay.hide();
          var data = JSON.parse(result);
          $(".btn").removeAttr("disabled");
          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
              }, 500);
            });
          }
          //$(".btn").removeAttr("disabled");
          //window.location = "<?php echo asset_url(); ?>/collection/collector";
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");
          $(".btn").removeAttr("disabled");
          window.location = "<?php echo asset_url(); ?>/collection/collector";
        }
      };
      $('#formUploadData').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate);
        // return false to prevent standard browser submit and page navigation
        return false;
      });
    }
  }

  function showDetail(uId) {
    window.location = "<?php echo asset_url(); ?>/collection/collector/detail?id=" + uId;
  }

  var setStatus = function(userStatus,slug){
    swal({
      title: "Konfirmasi",
      text: "Ubah status user ?",
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
        url   : "<?php echo asset_url(); ?>/user/update-status",
        data  : "id=" + slug + "&userStatus=" + encodeURI(userStatus) ,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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

  var check = function (data) {
    $.ajax({
      type: 'POST',
      url: "<?= asset_url() ?>/user/checkuser",
      data: data,
      dataType: 'JSON',
      success: function (res) {
        toastr.options = {
          "positionClass": "toast-top-right"
        }
        if (res.STATUS == 'SUCCESS') {
          toastr.success(res.MESSAGE, 'Berhasil');
        }
        if (res.STATUS == 'ERROR') {
          toastr.error(res.MESSAGE, 'Perhatian');
        }
      },
      error: function (res) {
        console.log(res)
      }
    });
  }

  var checkKode = function (data) {
    $.ajax({
      type: 'POST',
      url: '<?= asset_url() ?>/user/kodecheck',
      data: data,
      dataType: 'JSON',
      success: function (res) {
        toastr.options = {
          "positionClass": "toast-top-right"
        }
        if (res.STATUS == 'SUCCESS') {
          toastr.success(res.MESSAGE, 'Berhasil');
        }
        if (res.STATUS == 'ERROR') {
          toastr.error(res.MESSAGE, 'Perhatian');
        }
      },
      error: function (res) {
        console.log(res);
      }
    });
  }

    var checkUser = function () {
      $.ajax({
        type: 'POST',
        url: "<?= asset_url(); ?>/user/check",
        data: {
          userPrsh: '<?= $ctlUserData->PRSH_ID; ?>',
          userBigId: $('#userBigId').val()
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
          console.log(res)
        }
      });
    }

  $('#userBigId').blur(function () {
    if ($(this).val().length > 0) {
        checkUser();
    }
  });
  $('#userId').blur(function () {
    if ($(this).val().length > 0) {
      check({
        key: 'userId',
        value: $(this).val()
      });
    }
  });
  $('#userPonsel').blur(function () {
    if ($(this).val().length >= 10) {
      check({
        key: 'userPonsel',
        value: $(this).val()
      });
    }
  });
  $('#userPonselEdit').blur(function () {
    if ($(this).val().length >= 10) {
      check({
        key: 'userPonselEdit',
        value: $(this).val()
      });
    }
  });
  $('#userEmail').blur(function () {
    if ($(this).val().length > 0) {
      if (!$(this).val().match(/^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.([a-zA-Z0-9]{2,4}))+$/)) {
        toastr.error('Pastikan email sudah benar', 'Kolom Email');
        return false;
      }
      check({
        key: 'userEmail',
        value: $(this).val()
      });
    }
  });
  $('#userEmailEdit').blur(function () {
    if ($(this).val().length > 0) {
      if (!$(this).val().match(/^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.([a-zA-Z0-9]{2,4}))+$/)) {
        toastr.error('Pastikan email sudah benar', 'Kolom Email');
        return false;
      }
      check({
        key: 'userEmail',
        value: $(this).val()
      });
    }
  });
    $("#userKode").blur(function () {
      if ($(this).val().length > 0) {
        checkKode({
          userPrsh: '<?= $ctlUserData->PRSH_ID; ?>',
          key: 'userKodeGroup',
          value: $(this).val()
        });
      }
    });
    $("#userKodeTabungan").blur(function () {
      if ($(this).val().length > 0) {
        checkKode({
          userPrsh: '<?= $ctlUserData->PRSH_ID; ?>',
          key: 'userKodeTabungan',
          value: $(this).val()
        });
      }
    });
    $("#userKodeEdit").blur(function () {
      if ($(this).val().length > 0 && $(this).val() != $(this).data('pinjaman')) {
        checkKode({
          userPrsh: '<?= $ctlUserData->PRSH_ID; ?>',
          key: 'userKodeGroup',
          value: $(this).val()
        });
      }
    });
    $("#userKodeTabunganEdit").blur(function () {
      if ($(this).val().length > 0 && $(this).val() != $(this).data('tabungan')) {
        checkKode({
          userPrsh: '<?= $ctlUserData->PRSH_ID; ?>',
          key: 'userKodeTabungan',
          value: $(this).val()
        });
      }
    });

  var saveData = function(){
    var userIdX = $("#userId").val();
    var userBigIdX = $("#userBigId").val();
    var userPassX = $('#userPass').val();
    var userNamaX = $("#userNama").val();
    var userPonselX = $("#userPonsel").val();
    var userGroupX = $("#userGroup").val();
    var userPrshX = $("#userPrsh").val();
    var userEmailX = $("#userEmail").val();
    var userKodeX = $("#userKode").val();
    var userKodeTabunganX = $("#userKodeTabungan").val();
    var userStatusCollectX = $("#status_collect").val();
    var userStatusTabX = $("#status_tab").val();

    /* Validation */
    if (!$('#userBigId').val().length) {
      required('Kolom User ID');
      $('#userBigId').focus();
      return false;
    }
    if (!$('#userId').val().length) {
      required('Kolom Username');
      $('#userId').focus();
      return false;
    }
    if (!$('#userPass').val().length) {
      required('Kolom Password');
      $('#userPass').focus();
      return false;
    }
    if (!$('#userNama').val().length) {
      required('Kolom Nama');
      $('#userNama').focus();
      return false;
    }
    if (!$('#userPonsel').val().length) {
      required('Kolom No Ponsel');
      $('#userPonsel').focus();
      return false;
    } else if ($('#userPonsel').val().length < 10) {
      toastr.error('Minimal Nomor harus 10, Periksa nomor anda', 'Kolom No Ponsel');
      $('#userPonsel').focus();
      return false;
    }
    if (!$('#userEmail').val().length) {
      required('Kolom Email');
      $('#userEmail').focus();
      return false;
    } else if (!$('#userEmail').val().match(/^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.([a-zA-Z0-9]{2,4}))+$/)) {
      toastr.error('Pastikan email sudah benar', 'Kolom Email');
      $('#userEmail').focus();
      return false;
    }
    if (!$('#userKode').val().length) {
      required('Kolom Kode Group Pinjaman');
      $('#userKode').focus();
      return false;
    }
    if (!$('#userKodeTabungan').val().length) {
      required('Kolom Kode Group Tabungan');
      $('#userKodeTabungan').focus();
      return false;
    }

    //console.log(userBigIdX);

    if(userIdX != "" && userBigIdX != "" && userPassX != "" && userNamaX != "" && userPonselX != "" && userGroupX != "" && userPrshX != "" && userEmailX != "" && userKodeX != "" && userStatusCollectX != "" && userStatusTabX != "") {
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/tester",
        data  : "userId=" + encodeURI(userIdX) + "&userBigId=" + encodeURI(userBigIdX) + "&userPass=" + encodeURI(userPassX) + "&userNama=" + encodeURI(userNamaX) + "&userPonsel=" + encodeURI(userPonselX) + "&userGroup=" + userGroupX + "&userPrsh=" + userPrshX + "&userEmail=" + encodeURI(userEmailX) + "&userKode=" + encodeURI(userKodeX) + "&userKodeTabungan=" + encodeURI(userKodeTabunganX) + "&status_collect=" + encodeURI(userStatusCollectX) + "&status_tab=" + encodeURI(userStatusTabX),
        success : function(result) {
          gOverlay.hide();
          console.log();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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

  var deleteData = function(slug){
    //console.log(slug)
    swal({
      title: "Konfirmasi",
      text: "Hapus data user ?",
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
        url   : "<?php echo asset_url(); ?>/user/delete",
        data  : "id=" + slug,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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

  var gantiPassword = function (id) {
    $('#ganti_password').val('');
    createOverlay("Mohon Tunggu...");
    $.getJSON("<?php echo asset_url(); ?>/user/detail?id="+id, function (data) {
      $('#user_name').text(data.PAYLOAD.U_ID);
      $('#user_id').val(data.PAYLOAD.U_ID);
      gOverlay.hide();
      $("#gantiPassword").modal("show");
    })
  }

  var updatePassword = function () {
    if (!$('#ganti_password').val().length) {
      toastr.options = {
        "positionClass": "toast-top-right"
      }
      toastr.error('Harus diisi', 'Kolom password');
      return false;
    }
    $.ajax({
      url: '<?php echo asset_url(); ?>/user/change-password',
      type: 'POST',
      data: {
        user_id : $('#user_id').val(),
        password : $('#ganti_password').val()
      },
      beforeSend: function () {
        $('#btnUpdatePassword').attr('disabled', true);
      },
      success: function (data) {
        $('#btnUpdatePassword').attr('disabled', false);
        swal({
          type: 'success',
          title: 'Berhasil mengganti password'
        });
        $("#gantiPassword").modal("hide");
      },
      error: function (data) {
        console.log(data);
        $('#btnUpdatePassword').attr('disabled', false);
        swal({
          type: 'error',
          title: 'Gagal mengganti password',
          text: data.status
        });
      }
    });
  }

  var resetPassword = function(slug){
    swal({
      title: "Konfirmasi",
      text: "Reset password user menjadi <i><?php echo getSetting('DEFAULT_PASSWORD'); ?></i> ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya",
      cancelButtonText : "Tidak",
      closeOnConfirm: true,
      html: true
    },
    function(){
      //console.log(slug)
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/user/reset-password",
        data  : "id=" + slug,
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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

  var editData = function(userId) {
    $("#btnUpdateUser").attr("data-user-id", "");
    createOverlay("Mohon Tunggu...");
    $.ajax({
      type  : "GET",
      url   : "<?php echo asset_url(); ?>/user/detail",
      data  : "id=" + userId,
      success : function(result) {
        gOverlay.hide();
        var data = JSON.parse(result);

        if(data["STATUS"] == "SUCCESS") {
          $('#mdlUser_Edit').on('shown.bs.modal', function() {
            $("#userIdEdit").val(data["PAYLOAD"]["U_ID"]);
            $("#userBigIdEdit").val(data["PAYLOAD"]["USERBIGID"]);
            $("#userNamaEdit").val(data["PAYLOAD"]["U_NAMA"]);
            $("#userPonselEdit").val(data["PAYLOAD"]["U_TELPON"]);
            $("#userEmailEdit").val(data["PAYLOAD"]["U_EMAIL"]);
            $("#userKodeEdit").val(data["PAYLOAD"]["U_KODE_GROUP"]);
            $('#userKodeEdit').data('pinjaman', data["PAYLOAD"]["U_KODE_GROUP"]);
            $("#userKodeTabunganEdit").val(data["PAYLOAD"]["U_KODE_TABUNGAN"]);
            $('#userKodeTabunganEdit').data('tabungan', data["PAYLOAD"]["U_KODE_TABUNGAN"]);
            $("#status_collectEdit").val(data["PAYLOAD"]["U_STATUS_COLLECT"]);
            $("#status_tabEdit").val(data["PAYLOAD"]["U_STATUS_TAB"]);

            $("#btnUpdateUser").attr("data-user-id", data["PAYLOAD"]["U_ID"]);

            $("#userNamaEdit").focus();
          })

          $("#mdlUser_Edit").modal("show");
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
    var userId = $("#btnUpdateUser").attr("data-user-id");
    if(userId != "") {
      var userBigId = $("#userBigIdEdit").val();
      var userNama = $("#userNamaEdit").val();
      var userPonsel = $("#userPonselEdit").val();
      var userGroup = $("#userGroupEdit").val();
      var userPrsh = $("#userPrshEdit").val();
      var userEmail = $("#userEmailEdit").val();
      var userKode = $("#userKodeEdit").val();
      var userKodeTabungan = $("#userKodeTabunganEdit").val();
      var status_collect = $("#status_collectEdit").val();
      var status_tab = $("#status_tabEdit").val();

    /* Validation */
    if (!$('#userBigIdEdit').val().length) {
      required('Kolom User ID');
      $('#userBigIdEdit').focus();
      return false;
    }
    if (!$('#userIdEdit').val().length) {
      required('Kolom Username');
      $('#userIdEdit').focus();
      return false;
    }
    if (!$('#userNamaEdit').val().length) {
      required('Kolom Nama');
      $('#userNamaEdit').focus();
      return false;
    }
    if (!$('#userPonselEdit').val().length) {
      required('Kolom No Ponsel');
      $('#userPonselEdit').focus();
      return false;
    } else if ($('#userPonselEdit').val().length < 10) {
      toastr.error('Minimal Nomor harus 10, Periksa nomor anda', 'Kolom No Ponsel');
      $('#userPonselEdit').focus();
      return false;
    }
    if (!$('#userEmailEdit').val().length) {
      required('Kolom Email');
      $('#userEmailEdit').focus();
      return false;
    } else if (!$('#userEmailEdit').val().match(/^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.([a-zA-Z0-9]{2,4}))+$/)) {
      toastr.error('Pastikan email sudah benar', 'Kolom Email');
      $('#userEmailEdit').focus();
      return false;
    }
    if (!$('#userKodeEdit').val().length) {
      required('Kolom Kode Group');
      $('#userKodeEdit').focus();
      return false;
    }

      if(userPonsel != "" && userEmail != "") {
        createOverlay("Mohon Tunggu...");
        $.ajax({
          type  : "PUT",
          url   : "<?php echo asset_url(); ?>/user",
          data  : "userId=" + encodeURI(userId) + "&userBigId=" + encodeURI(userBigId) + "&userNama=" + encodeURI(userNama) + "&userPonsel=" + encodeURI(userPonsel) + "&userGroup=" + userGroup + "&userPrsh=" + userPrsh + "&userEmail=" + encodeURI(userEmail) + "&userKode=" + encodeURI(userKode) + "&userKodeTabungan=" + encodeURI(userKodeTabungan) + "&status_collect=" + encodeURI(status_collect) + "&status_tab=" + encodeURI(status_tab),
          success : function(result) {
            gOverlay.hide();
            var data = JSON.parse(result);

            if(data["STATUS"] == "SUCCESS") {
              toastr.success(data["MESSAGE"]);
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/collection/collector";
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
                  // window.location = "<?php echo asset_url(); ?>/collection/collector";
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
        toastr.error("Nomor ponsel dan email HARUS diisi");
      }
    }
  }
</script>
@stop
