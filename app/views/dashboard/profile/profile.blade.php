@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4 style="color: #bb0a0a !important"><i class="icon-arrow-left52 position-left"></i> Profile</h4>
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
      <li class="active"><i class="icon-profile"></i> Profil</li>
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
  <form class="form-horizontal" method="post" id="formData" name="formData"> 
    <div class="panel panel-flat">
      <div class="panel-heading">
        <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Profil</h5>
      </div>

      <div class="panel-body">
        <div class="form-group">
          <label class="control-label col-lg-3">User ID</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon text-semibold"><i class="icon-user"></i></span>
              <input type="text" id="userID" name="userID" class="form-control text-semibold" value="<?php echo $ctlUserData->{"USERBIGID"}; ?>" readonly>
            </div>                        
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">Nama</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-profile"></i></span>
              <input type="text" id="nama" name="nama" class="form-control" value="<?php echo $ctlUserData->{"U_NAMA"}; ?>">
            </div>                        
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">Email</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-mention"></i></span>
              <input type="text" id="email" name="email" class="form-control" value="<?php echo $ctlUserData->{"U_EMAIL"}; ?>">
            </div>                        
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">No. Ponsel</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="icon-phone2"></i></span>
              <input type="text" id="ponsel" name="ponsel" class="form-control" value="<?php echo $ctlUserData->{"U_TELPON"}; ?>"  onkeypress="return hanyaAngka(event)">
            </div>                        
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">Password Lama</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
              <input type="password" id="passwordLama" name="passwordLama" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
               <span class="input-group-addon"><input type="checkbox" id="showPass"> Show</span>
            </div>                                   
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">Password Baru</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
              <input type="password" id="passwordBaru" name="passwordBaru" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required >
               <span class="input-group-addon"><input type="checkbox" id="showPass1"> Show</span>
               <div id="message"></div>
            </div>
            <p id="complete" class="invalid">* Password harus mengandung <span id="letter" class="invalid">huruf kecil, </span><span id="capital" class="invalid">huruf besar, </span><span id="number" class="invalid">angka, </span><span id="length" class="invalid">min. 6 karakter</span></p>                                                
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-3">Konfirmasi Password Baru</label>
          <div class="col-lg-5">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
              <input type="password" id="passwordKonfirmasi" name="passwordKonfirmasi" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
               <span class="input-group-addon"><input type="checkbox" id="showPass2"> Show</span>
               <div id="message"></div>
            </div>  
            <p id="complete1" class="invalid">* Password harus mengandung <span id="letter1" class="invalid">huruf kecil, </span><span id="capital1" class="invalid">huruf besar, </span><span id="number1" class="invalid">angka, </span><span id="length1" class="invalid">min. 6 karakter</span></p>          
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-6 control-label text-semibold">&nbsp;</label>            
          <div class="col-lg-2">
            <button type="button" class="btn bg-blue-theme btn-block btn-labeled" onClick="saveData()"><b><i class="fa fa-check position-right"></i></b> Simpan</button>
          </div>            
        </div>
      </div>
    </div>
  </form>   
  <style type="text/css">
    #message {
            display: none;
            background: #f1f1f1;
            color: #000;
            position: relative;
            padding: 5px;
            margin-top: 5px;
        }

        #message p {
            padding: 5px 10px;
            font-size: 10px;
        }

        /* Add a green text color and a checkmark when the requirements are right */
        .valid {
            color: green;
            display: none;
        }

        .valid:before {
            position: relative;
            left: -35px;
        }

        /* Add a red text color and an "x" icon when the requirements are wrong */
        .invalid {
            color: red;
        }

        .invalid:before {
            position: relative;
            left: -35px;
        }
  </style>
  <div class="footer text-muted"></div>

  <div class="footer text-muted"></div>
</div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/form_select2.js"></script>
<script type="text/javascript">
  // Select with search
  $('.select-search').select2();
</script>
<script type="text/javascript">
    var myInput = document.getElementById("passwordBaru");
    var myInput2 = document.getElementById("passwordKonfirmasi");
    var letter = document.getElementById("letter");
    var letter1 = document.getElementById("letter1");
    var capital = document.getElementById("capital");
    var capital1 = document.getElementById("capital1");
    var number = document.getElementById("number");
    var number1 = document.getElementById("number1");
    var length = document.getElementById("length");
    var length1 = document.getElementById("length1");
    var complete = document.getElementById("complete");
    var complete1 = document.getElementById("complete1");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function() {
      document.getElementById("message").style.display = "block";
    }

    myInput2.onfocus = function() {
      document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function() {
      document.getElementById("message").style.display = "none";
    }

    myInput2.onblur = function() {
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

    myInput2.onkeyup = function() {
      // Validate lowercase letters
      var lowerCaseLetters1 = /[a-z]/g;
      if(myInput2.value.match(lowerCaseLetters1)) { 
        letter1.classList.remove("invalid");
        letter1.classList.add("valid");
        var hurufKecil1 = true;
      } else {
        letter1.classList.remove("valid");
        letter1.classList.add("invalid");
        var hurufKecil1 = false;
    }

      // Validate capital letters
      var upperCaseLetters1 = /[A-Z]/g;
      if(myInput2.value.match(upperCaseLetters1)) { 
        capital1.classList.remove("invalid");
        capital1.classList.add("valid");
        var hurufBesar1 = true;
      } else {
        capital1.classList.remove("valid");
        capital1.classList.add("invalid");
        var hurufBesar1 = false;
      }

      // Validate numbers
      var numbers1 = /[0-9]/g;
      if(myInput2.value.match(numbers1)) { 
        number1.classList.remove("invalid");
        number1.classList.add("valid");
        var nomor1 = true;
      } else {
        number1.classList.remove("valid");
        number1.classList.add("invalid");
        var nomor1 = false;
      }

      // Validate length
      if(myInput2.value.length >= 6) {
        length1.classList.remove("invalid");
        length1.classList.add("valid");
        var panjang1 = true;
      } else {
        length1.classList.remove("valid");
        length1.classList.add("invalid");
        var panjang1 = false;
      }

      // Validate complete
      if(myInput2.value.length1 >= 6 && myInput2.value.match(numbers1) && myInput2.value.match(upperCaseLetters1) && myInput2.value.match(lowerCaseLetters1)) {
        complete1.classList.remove("invalid");
        complete1.classList.add("valid");
        var lengkap1 = true;
      } else {
        complete1.classList.remove("valid");
        complete1.classList.add("invalid");
        var lengkap1 = false;
      }

    }
</script>
 <script type="text/javascript">
      $(function(){
      $("#showPass, #showPass1, #showPass2").click(function(){ // #showPass -> id Checkbox
        if($("[name=passwordLama]").attr('type')=='password'){
          $("[name=passwordLama]").attr('type','text');
        }else if($("[name=passwordBaru]").attr('type')=='password') {
          $("[name=passwordBaru]").attr('type','text');
        } else if($("[name=passwordKonfirmasi]").attr('type')=='password') {
          $("[name=passwordKonfirmasi]").attr('type','text');
        }else{
          $("[name=passwordLama],[name=passwordBaru],[name=passwordKonfirmasi]").attr('type','password');
        }
      });
  });

  function hanyaAngka(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))

      return false;
    return true;
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

  var saveData = function(){
    var id = "<?php echo $ctlUserData->{'U_ID'}; ?>";
    var nama = $("#nama").val();
    var email = $("#email").val();
    var ponsel = $("#ponsel").val();
    var passwordLama = $("#passwordLama").val();
    var passwordBaru = $("#passwordBaru").val();
    var passwordKonfirmasi = $("#passwordKonfirmasi").val();


    var myEmail = document.getElementById("email");
    var myNumber = document.getElementById("ponsel");
    var myInput = document.getElementById("passwordBaru");
    var myInput2 = document.getElementById("passwordKonfirmasi");
    var lowerCaseLetters = /[a-z]/g;
    var upperCaseLetters = /[A-Z]/g;
    var numbers = /[0-9]/g;
    var emailValidate = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    
    if(myInput.value.match(lowerCaseLetters) && myInput.value.match(upperCaseLetters) && myInput.value.match(numbers) && myInput.value.length >= 6 && myInput2.value.match(lowerCaseLetters) && myInput2.value.match(upperCaseLetters) && myInput2.value.match(numbers) && myInput2.value.length >= 6 && myNumber.value.length >= 9 && myEmail.value.match(emailValidate)) {
      createOverlay("Mohon Tunggu...");
      $.ajax({  
        type  : "PUT",
        url   : "<?php echo asset_url(); ?>/profil",
        data  : "id=" + id + "&nama=" + encodeURI(nama) + "&email=" + encodeURI(email) + "&ponsel=" + encodeURI(ponsel) + "&passwordLama=" + passwordLama + "&passwordBaru=" + passwordBaru + "&passwordKonfirmasi=" + passwordKonfirmasi,
        success : function(result) { 
          gOverlay.hide();
          var data = JSON.parse(result);

          if(data["STATUS"] == "SUCCESS") {            
            toastr.success(data["MESSAGE"]);
            setTimeout(function(){ 
              window.location = "<?php echo asset_url(); ?>/profil";
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
                window.location = "<?php echo asset_url(); ?>/profil";
              }, 500);              
            });
          }
        },
        error : function(error) {   
          gOverlay.hide();
          alert("Gangguan pada server/jaringan\r\n" + error);
        }
      });
    } else {
      toastr.error("Harap isi data dengan lengkap, dan pastikan password yang anda masukkan kombinasi karakter,email dengan format email");
    }    
  }
</script>
@stop