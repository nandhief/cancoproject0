@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Manajemen User</span></h4>
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
      <li class="active"><i class="icon-users"></i> Manajemen User</li>
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
  <form class="form-horizontal" method="post"  id="formData" name="formData" onSubmit="alert('Submitted.');return false;">
    <div class="panel panel-flat border-top-primary">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important;">Tambah User</h5>
        <div class="heading-elements">
          <ul class="icons-list">
            <li><a data-action="collapse"></a></li>
          </ul>
        </div>
      </div>

      <div class="panel-body">
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Perusahaan</label>
          <div class="col-lg-4">
            <select class="js-example-basic-single" name="userPrsh" id="userPrsh">
              <option value="-">[Non-perusahaan]</option>
              <?php
              if(isset($ctlPrsh) && count($ctlPrsh) > 0) {
                foreach ($ctlPrsh as $aData) {
                  ?>
                  <option value="<?php echo $aData->{"PRSH_ID"}; ?>"><?php echo $aData->{"PRSH_NAMA"}; ?></option>
                  <?php
                }
              }
              ?>
            </select>
          </div>
        </div>

         <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">User ID</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" class="form-control first-input" id="userBigId" name="userBigId" onkeypress="return hanyaAngka(event)" placeholder="User ID...">
            </div>
            <p id="nomor" class="invalid" style="color: red;"><span>* Isian berupa angka</span></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Username</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" class="form-control first-input" id="userId" name="userId" onkeyup="this.value = this.value.toUpperCase()" placeholder="Username...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Password</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
              <input type="text" class="form-control" id="userPass" name="userPass" value="12345678" readonly required>
              <!-- <input type="password" class="form-control" id="userPass" name="userPass" placeholder="Password...">
              <span class="input-group-addon"><input type="checkbox" id="showPass"> Show</span> -->
              <div id="message">
              <style type="text/css">
                #message { display: none; background: #f1f1f1; color: #000; position: relative; padding: 5px; margin-top: 5px; } #message p { padding: 5px 10px; font-size: 10px; } /* Add a green text color and a checkmark when the requirements are right */ .valid { color: green; display: none; } .valid:before { position: relative; left: -35px; } /* Add a red text color and an "x" icon when the requirements are wrong */ .invalid { color: red; } .invalid:before { position: relative; left: -35px;
                    }
              </style>
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
          <!-- <p id="complete" class="invalid">* Password harus mengandung <span id="letter" class="invalid">huruf kecil, </span><span id="capital" class="invalid">huruf besar, </span><span id="number" class="invalid">angka, </span><span id="length" class="invalid">min. 6 karakter</span></p> -->
        </div>
      </div>
        <!-- <br> -->
        <!-- <br> -->
<!--         <div>
         <table>
           <center>
             <tr>
              <td><p style="color: red;">password mengandung : </p></td>
              <td><p id="letter" class="invalid"> huruf kecil ,</p></td>
              <td><p id="capital" class="invalid"> huruf besar , </p></td>
              <td><p id="number" class="invalid"> angka, </p></td>
              <td><p id="length" class="invalid"> min. 6 karakter</p></td>
            </tr>
           </center>
          </table>
        </div> -->
        <!-- <br> -->
        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Nama</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-profile"></i></span>
              <input type="text" class="form-control" id="userNama" name="userNama" onkeyup="this.value = this.value.toUpperCase(); validHuruf(this)" placeholder="Nama user...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">No.Ponsel</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-phone2"></i></span>
              <input type="text" class="form-control" id="userPonsel" name="userPonsel" onkeypress="return hanyaAngka(event)" placeholder="Nomor ponsel user...">
            </div>
            <p id="nomor" class="invalid" style="color: red;"><span>* Isian berupa angka</span></p>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Email</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-mention"></i></span>
              <input type="email" class="form-control" id="userEmail" name="userEmail" placeholder="Email user...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Group Role</label>
          <div class="col-lg-4">
            <select class="form-control" name="userGroup" id="userGroup">
                <option value="GR_DIREKSI">Direksi</option>
                <option value="GR_SUPERVISOR">Supervisor</option>
                <option value="GR_COLLECTOR">Collector</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="coba1">
          <label class="col-lg-2 control-label text-semibold">Group Pinjaman</label>
          <div class="col-lg-4">
            <select class="form-control" name="status_collect" id="status_collect">
                <option value="0">--  Tidak ada Tipe --</option>
                <option value="1">Collect Pinjaman</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="coba2">
          <label class="col-lg-2 control-label text-semibold">Group Tabungan</label>
          <div class="col-lg-4">
            <select class="form-control" name="status_tab" id="status_tab">
                <option value="0">-- Tidak ada Tipe --</option>
                <option value="1">Collect Tabungan</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Group Pinjaman</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-gear"></i></span>
              <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase();" id="userKodeGroup" name="userKodeGroup" placeholder="Kode Group Pinjaman...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 control-label text-semibold">Kode Group Tabungan</label>
          <div class="col-lg-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-gear"></i></span>
              <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase();" id="userKodeTabungan" name="userKodeTabungan" placeholder="Kode Group Tabungan...">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-4 control-label text-semibold">&nbsp;</label>
          <div class="col-lg-2">
            <button type="button" class="btn bg-blue-theme btn-block btn-labeled" onClick="saveData()"><b><i class="fa fa-check position-right"></i></b> Simpan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-flat border-top-primary">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color:#bb0a0a !important">Data User</h5>
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
              <th style="text-align:center" width="50%">Perusahaan</th>
              <th style="text-align:center" width="16%">User ID</th>
              <th style="text-align:center" width="16%">Kode Group Pinjaman</th>
              <th style="text-align:center" width="16%">Kode Group Tabungan</th>
              <th style="text-align:center" width="16%">Username</th>
              <th style="text-align:center" width="40%">Nama Pegawai</th>
              <th style="text-align:center" width="15%">Status</th>
              <th style="text-align:center" width="15%">Group Role</th>
              <th style="text-align:center" width="13%">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(isset($ctlAppUsers) && count($ctlAppUsers) > 0) {
              foreach ($ctlAppUsers as $aData) {
                if($aData->{"U_STATUS"} == "USER_ACTIVE")    $btnClass = "btn-success";
                if($aData->{"U_STATUS"} == "USER_INACTIVE")  $btnClass = "btn-danger";
                ?>
                <tr>
                  <td><b><?php echo $aData->{"PRSH_NAMA"}; ?></b></td>
                  <td><b><?php echo $aData->{"USERBIGID"}; ?></b></td>
                  <td><b><?php echo $aData->{"U_KODE_GROUP"}; ?></b></td>
                  <td><b><?php echo $aData->{"U_KODE_TABUNGAN"}; ?></b></td>
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
                          <li><a href="javascript:setStatus('AKTIF','<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-check"></i> Aktif</a></li>
                          <li><a href="javascript:setStatus('NON_AKTIF','<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-remove"></i> Non-Aktif</a></li>
                          <!--li><a href="javascript:setStatus('SUSPEND','<?php echo $aData->{"U_ID"}; ?>')"><i class="fa fa-ban"></i> Suspend</a></li-->
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td style="text-align:center"><?php echo $aData->{"R_INFO"}; ?></td>
                  <td style="text-align:center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary btn-icon dropdown-toggle" data-toggle="dropdown" style="background-color: #bb0a0a !important; color: #fff">
                        <i class="icon-menu7"></i> &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right" style="background-color:#bb0a0a;">
                        <li><a href="javascript:editData('<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-edit"></i> Ubah</a></li>
                        <li><a href="javascript:resetPassword('<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-retweet"></i> Reset Password</a></li>
                        <li><a href="javascript:gantiPassword('<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-retweet"></i> Ganti Password</a></li>
                        <li><a href="javascript:deleteData('<?php echo $aData->{"U_ID"}; ?>')" style="color: #fff;"><i class="fa fa-remove"></i> Hapus</a></li>
                        <?php
                        if($aData->{"U_GROUP_ROLE"} == "GR_COLLECTOR" && $aData->{"U_LOGIN_TOKEN"} != "" && $aData->{"U_LOGIN_TOKEN"} != "-") {
                          ?>
                          <!--li><a href="javascript:deleteToken('<?php echo $aData->{"U_ID"}; ?>')"><i class="icon-exit2"></i> Logout Aplikasi Mobile</a></li-->
                          <?php
                        }
                        ?>
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

  <div id="mdlUser_Edit" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #bb0a0a !important">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h6 class="modal-title" style="color: #fff"><i class="icon-user"></i> &nbsp; Edit User</h6>
        </div>

        <div class="modal-body">
          <form class="form-horizontal" method="post" id="formCost" name="formCost" action="<?php echo asset_url(); ?>/user">
            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Perusahaan</label>
              <div class="col-lg-8">
                <select class="form-control" name="userPrshEdit" id="userPrshEdit">
                  <option value="-">[Non-perusahaan]</option>
                  <?php
                  if(isset($ctlPrsh) && count($ctlPrsh) > 0) {
                    foreach ($ctlPrsh as $aData) {
                      ?>
                      <option value="<?php echo $aData->{"PRSH_ID"}; ?>"><?php echo $aData->{"PRSH_NAMA"}; ?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <input type="hidden" id="roleEdit">

             <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">User ID</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-user"></i></span>
                  <input type="text" id="userBigIdEdit" class="form-control" onkeypress="return hanyaAngka(event)" data-bigid="">
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
                  <input type="text" id="userPonselEdit" class="form-control" onkeypress="return hanyaAngka(event)" data-ponsel="">
                </div>
                <p style="color: red;">* isikan cuma bernilai angka.</p>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Email</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-mention"></i></span>
                  <input type="text" class="form-control" id="userEmailEdit" name="userEmailEdit" placeholder="Email user..." data-email="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Kode Group Pinjaman</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-gear"></i></span>
                  <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase();" id="userKodeEdit" name="userKodeEdit" placeholder="Kode Group Pinjaman..." data-pinjaman="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Kode Group Tabungan</label>
              <div class="col-lg-8">
                <div class="input-group">
                  <span class="input-group-addon"><i class="icon-gear"></i></span>
                  <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase();" id="userKodeTabunganEdit" name="userKodeTabunganEdit" placeholder="Kode Group Tabungan..." data-tabungan="">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-4 control-label text-semibold">Group Role</label>
              <div class="col-lg-8">
                <select class="form-control" id="userGroupEdit">
                  <option value="GR_DIREKSI">Direksi</option>
                  <option value="GR_SUPERVISOR">Supervisor</option>
                  <option value="GR_COLLECTOR">Collector</option>
                </select>
              </div>
            </div>
            <div class="form-group" id="cobaEdit1">
              <label class="col-lg-4 control-label text-semibold">Status Collect</label>
              <div class="col-lg-8">
                <select class="form-control" name="status_collect" id="status_collectE">
                    <option value="0">--  Tidak ada Tipe --</option>
                     <?php
                  if(isset($ctlAppUsers) && count($ctlAppUsers) > 0) {
                    foreach ($ctlAppUsers as $aData) {
                      ?>
                    <option value="1" <?php if($aData->{"U_STATUS_COLLECT"} == 1) echo "selected"; ?>>Collect Pinjaman</option>
                      <?php
                      $i = 1;
                      if($i) break;
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group" id="cobaEdit2">
              <label class="col-lg-4 control-label text-semibold">Status Tab</label>
              <div class="col-lg-8">
                <select class="form-control" name="status_tab" id="status_tabE">
                    <option value="0">-- Tidak ada Tipe --</option>
                  <?php
                  if(isset($ctlAppUsers) && count($ctlAppUsers) > 0) {
                    foreach ($ctlAppUsers as $aData) {
                      ?>
                    <option value="1" <?php if($aData->{"U_STATUS_TAB"} == 1) echo "selected"; ?>>Collect Tabungan</option>
                      <?php
                       $i = 1;
                      if($i) break;
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-warning btn-labeled btn-xs" data-dismiss="modal"><b><i class="icon-cross3"></i></b> Tutup </button>
          <button type="button" class="btn btn-primary btn-labeled btn-xs" onClick="updateData();" id="btnUpdateUser" data-user-id="userId"><b><i class="icon-checkmark2"></i></b> Simpan </button>
        </div>
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

<script type="text/javascript">
  window.onload = function() {
      var input = document.getElementById('coba1');
      input.style.visibility = 'hidden';
      var input1 = document.getElementById('coba2');
      input1.style.visibility = 'hidden';
      var city = document.getElementById('userGroup');
      city.onchange = function () {
          console.log('change');
        if (city.options[city.selectedIndex].value == 'GR_COLLECTOR') {
        input.style.visibility = 'visible'; input1.style.visibility = 'visible';
         } else {
             console.log('else');
        input.style.visibility = 'hidden'; input1.style.visibility = 'hidden';
     }
    }
    }
</script>
<!-- <script type="text/javascript">
  window.onload = func1() {
      var input2 = document.getElementById('cobaEdit1');
      input2.style.visibility = 'hidden';
      var input3 = document.getElementById('cobaEdit2');
      input3.style.visibility = 'hidden';
      var city1 = document.getElementById('userGroupEdit');
      city.onchange = function () {
          console.log('change');
        if (city1.options[city1.selectedIndex].value == 'GR_COLLECTOR') {
        input2.style.visibility = 'visible'; input3.style.visibility = 'visible';
         } else {
             console.log('else');
        input2.style.visibility = 'hidden'; input3.style.visibility = 'hidden';
     }
    }
    }
</script> -->
<script type="text/javascript">
  function validHuruf(a) {
    if(!/^[A-Za-z ]+$/.test(a.value)) {
      a.value = a.value.substring(0,a.value.length-1000)
    }
  }
</script>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/form_select2.js"></script>
<script type="text/javascript">
  // Select with search
  $('.select-search').select2();
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
});
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

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/tables/datatables/datatables.min.js"></script>
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
  });

  $(".first-input").focus();
  // $("#userBigId").val("<?php echo $ctrlUrutan ?>");

  function required(target) {
    toastr.options = {
      "positionClass": "toast-top-right"
    }
    toastr.error('Harus diisi', target);
    return false;
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

        console.log(data)
        if(data["STATUS"] == "SUCCESS") {
          $('#mdlUser_Edit').on('shown.bs.modal', function() {
            $("#userIdEdit").val(data["PAYLOAD"]["U_ID"]);
            $("#roleEdit").val(data["PAYLOAD"]["U_GROUP_ROLE"]);
            $("#userBigIdEdit").val(data["PAYLOAD"]["USERBIGID"]);
            $("#userBigIdEdit").data('bigid', data["PAYLOAD"]["USERBIGID"]);
            $("#userNamaEdit").val(data["PAYLOAD"]["U_NAMA"]);
            $("#userPonselEdit").val(data["PAYLOAD"]["U_TELPON"]);
            $("#userPonselEdit").data('ponsel', data["PAYLOAD"]["U_TELPON"]);
            $("#userGroupEdit").val(data["PAYLOAD"]["U_GROUP_ROLE"]);
            $("#userPrshEdit").val(data["PAYLOAD"]["PRSH_ID"]);
            $("#userEmailEdit").val(data["PAYLOAD"]["U_EMAIL"]);
            $("#userEmailEdit").data('email', data["PAYLOAD"]["U_EMAIL"]);
            $("#userKodeEdit").val(data["PAYLOAD"]["U_KODE_GROUP"]);
            $('#userKodeEdit').data('pinjaman', data["PAYLOAD"]["U_KODE_GROUP"]);
            $("#userKodeTabunganEdit").val(data["PAYLOAD"]["U_KODE_TABUNGAN"]);
            $('#userKodeTabunganEdit').data('tabungan', data["PAYLOAD"]["U_KODE_TABUNGAN"]);
            $("#status_collectE").val(data["PAYLOAD"]["U_STATUS_COLLECT"]);
            $("#status_tabE").val(data["PAYLOAD"]["U_STATUS_TAB"]);
            //console.log($("#status_collectE").val(data["PAYLOAD"]["U_STATUS_COLLECT"]));

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
      var userKodeGroup = $("#userKodeEdit").val();
      var userKodeTabungan = $("#userKodeTabunganEdit").val();
      var userCollect = $("#status_collectE").val();
      var userTab = $("#status_tabE").val();

      /* Validation */
      if ((!$('#userBigIdEdit').val().length) && ($('#roleEdit').val() != 'GR_DIREKSI')) {
        required('Kolom User ID');
        $('#userBigIdEdit').focus();
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
      if ((!$('#userKodeEdit').val().length) && ($('#status_collectE').val() == 1) && ($('#roleEdit').val() != 'GR_DIREKSI')) {
        required('Kolom Kode Group Pinjaman');
        $('#userKodeEdit').focus();
        return false;
      }
      if ((!$('#userKodeTabunganEdit').val().length) && ($('#status_tabE').val() == 1) && ($('#roleEdit').val() != 'GR_DIREKSI')) {
        required('Kolom Kode Group Tabungan');
        $('#userKodeTabunganEdit').focus();
        return false;
      }
      if (!$('#userGroupEdit').val().length) {
        required('Kolom Kode Group');
        $('#userGroupEdit').focus();
        return false;
      }

      //console.log(userKodeGroup);

      if(userPonsel != "" && userEmail != "") {
        createOverlay("Mohon Tunggu...");
        $.ajax({
          type  : "PUT",
          url   : "<?php echo asset_url(); ?>/user",
          data  : "userId=" + encodeURI(userId) + "&userBigId=" + encodeURI(userBigId) + "&userNama=" + encodeURI(userNama) + "&userPonsel=" + encodeURI(userPonsel) + "&userGroup=" + userGroup + "&userPrsh=" + userPrsh + "&userEmail=" + encodeURI(userEmail) + "&userKode=" + encodeURI(userKodeGroup) + "&userKodeTabungan=" + encodeURI(userKodeTabungan) + "&status_collect=" + encodeURI(userCollect) + "&status_tab=" + encodeURI(userTab),
          success : function(result) {
            gOverlay.hide();
            var data = JSON.parse(result);

            if(data["STATUS"] == "SUCCESS") {
              toastr.success(data["MESSAGE"]);
              setTimeout(function(){
                window.location = "<?php echo asset_url(); ?>/user";
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
                  // window.location = "<?php echo asset_url(); ?>/user";
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
        var data = res;
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
          userPrsh: $('#userPrsh').val(),
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
          console.log(res);
        }
      });
    }

    $('#userBigId').blur(function () {
      if ($(this).val().length > 0) {
        checkUser();
      }
    });
    $('#userPrsh').change(function () {
      if ($('#userBigId').val().length > 0) {
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
    $("#userKodeGroup").blur(function () {
      if ($(this).val().length > 0) {
        checkKode({
          userPrsh: $('#userPrsh').val(),
          key: 'userKodeGroup',
          value: $(this).val()
        });
      }
    });
    $("#userKodeTabungan").blur(function () {
      if ($(this).val().length > 0) {
        checkKode({
          userPrsh: $('#userPrsh').val(),
          key: 'userKodeTabungan',
          value: $(this).val()
        });
      }
    });
    $('#userBigIdEdit').blur(function () {
        if ($(this).val().length > 0 && $(this).val() != $(this).data('bigid')) {
            checkKode({
            userPrsh: $("#userPrshEdit").val(),
            key: 'userBigId',
            value: $(this).val()
            });
        }
    });
    $('#userBigIdEdit').blur(function () {
      if ($(this).val().length > 0) {
        checkKode();
      }
    });
    $('#userPonselEdit').blur(function () {
      if ($(this).val().length >= 10 && $(this).val() != $(this).data('ponsel')) {
        check({
          key: 'userPonsel',
          value: $(this).val()
        });
      }
    });
    $('#userEmailEdit').blur(function () {
      if ($(this).val().length > 0 && $(this).val() != $(this).data('email')) {
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
    $("#userKodeEdit").blur(function () {
      if ($(this).val().length > 0 && $(this).val() != $(this).data('pinjaman')) {
        checkKode({
          userPrsh: $("#userPrshEdit").val(),
          key: 'userKodeGroup',
          value: $(this).val()
        });
      }
    });
    $("#userKodeTabunganEdit").blur(function () {
      if ($(this).val().length > 0 && $(this).val() != $(this).data('tabungan')) {
        checkKode({
          userPrsh: $("#userPrshEdit").val(),
          key: 'userKodeTabungan',
          value: $(this).val()
        });
      }
    });

   var saveData = function(){
    var userBigIdX = $("#userBigId").val();
    var userIdX = $("#userId").val();
    var userPassX = $('#userPass').val();
    var userNamaX = $("#userNama").val();
    var userPonselX = $("#userPonsel").val();
    var userGroupX = $("#userGroup").val();
    var userPrshX = $("#userPrsh").val();
    var userEmailX = $("#userEmail").val();
    var userKodeGroupX = $("#userKodeGroup").val();
    var userKodeTabunganX = $("#userKodeTabungan").val();
    var status_collectX = $("#status_collect").val();
    var status_tabX = $("#status_tab").val();

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
    if (!$('#userGroup').val().length) {
      required('Kolom Group Role');
      $('#userGroup').focus();
      return false;
    } else {
      if ($('#userGroup').val() == 'GR_COLLECTOR') {
        if ($("#status_collect").val() != 0 && !$('#userKodeGroup').val().length) {
          required('Kolom Kode Group');
            $('#userKodeGroup').focus();
            return false;
        }
        if ($("#status_tab").val() != 0 && !('#userKodeTabungan').val().length) {
          required('Kolom Kode Tabungan');
            $('#userKodeTabungan').focus();
            return false;
        }
      }
    }

    //console.log(userIdX);

    var myEmail = document.getElementById("userEmail");
    var myNumber = document.getElementById("userPonsel");
    var myInput = document.getElementById("userPass");
    var lowerCaseLetters = /[a-z]/g;
    var upperCaseLetters = /[A-Z]/g;
    var numbers = /[0-9]/g;
    var emailValidate = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
      createOverlay("Mohon Tunggu...");
      $.ajax({
        type  : "POST",
        url   : "<?php echo asset_url(); ?>/tester",
        data  : "userId=" + encodeURI(userIdX) + "&userBigId=" + encodeURI(userBigIdX) + "&userPass=" + encodeURI(userPassX) + "&userNama=" + encodeURI(userNamaX) + "&userPonsel=" + encodeURI(userPonselX) + "&userGroup=" + userGroupX + "&userPrsh=" + userPrshX + "&userEmail=" + encodeURI(userEmailX) + "&userKode=" + encodeURI(userKodeGroupX) + "&userKodeTabungan=" + encodeURI(userKodeTabunganX) + "&status_collect=" + encodeURI(status_collectX) + "&status_tab=" + encodeURI(status_tabX),
        success : function(result) {
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){
              window.location = "<?php echo asset_url(); ?>/user";
            }, 500);
          } else {
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
                window.location = "<?php echo asset_url(); ?>/user";
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
              window.location = "<?php echo asset_url(); ?>/user";
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
                window.location = "<?php echo asset_url(); ?>/user";
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
              window.location = "<?php echo asset_url(); ?>/user";
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
                window.location = "<?php echo asset_url(); ?>/user";
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
              window.location = "<?php echo asset_url(); ?>/user";
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
                window.location = "<?php echo asset_url(); ?>/user";
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

  var deleteToken = function(userId) {

  }
</script>
@stop
