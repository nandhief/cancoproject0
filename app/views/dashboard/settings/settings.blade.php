@extends('dashboard.layout-dashboard')
@section('content')
<div class="page-header">
  <div class="page-header-content">
    <div class="page-title">
      <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold" style="color: #bb0a0a !important">Pengaturan</span></h4>
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
      <li class="active"><i class="icon-wrench3"></i> Pengaturan</li>
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
      <h5 class="panel-title text-semibold" style="color: #bb0a0a !important">Unggah File Logo Perusahaan</h5>
      <div class="heading-elements">
        <ul class="icons-list">  
          <li><a href="<?php echo asset_url(); ?>/assets/images/placeholder.jpg" download><i class="  icon-download4"></i> Download File Contoh</a></li>
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
      <form class="form-horizontal" enctype="multipart/form-data" method="post" id="formUploadData" name="formUploadData" action="<?php echo asset_url(); ?>/settings">
        <div class="form-group">
          <div class="col-lg-8">
            <?php
            if($ctlPrshData->{"PRSH_IMG_PATH"} == "-" || $ctlPrshData->{"PRSH_IMG_PATH"} == "") {
              ?>
              <p class="content-group">
                Tidak ada file logo perusahaan Anda.
              </p>
              <?php
            }
            else {
              ?>
              <p class="content-group">
                Berikut adalah file gambar logo perusahaan yang aktif.
              </p>
              <img src="<?php echo asset_url(); ?>/<?php echo $ctlPrshData->{"PRSH_IMG_PATH"}; ?>">
              <?php
            }
            ?>
          </div>            
        </div>

        <p class="content-group">
          Untuk mengganti logo, silahkan unggah file gambar logo perusahaan Anda dalam format JPEG atau PNG dengan ukuran 500x500 pixel.
        </p>

        <div class="form-group">
          <div class="col-lg-4">
            <input type="file" class="file-input-img" data-show-preview="false" data-show-upload="false" id="imgFile" name="imgFile" placeholder="File format JPG atau PNG...">
          </div>            
        </div>

        <div class="form-group">
          <div class="col-lg-4">
            <button onClick="saveContent()" class="btn bg-blue-theme btn-block">Unggah File<i class="fa fa-upload position-right"></i></button>
          </div>            
        </div>
      </form>
    </div>
  </div>
</div>  

<div class="footer text-muted"></div>

<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/pages/uploader_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>/assets/js/jquery.form.js"></script>
<script type="text/javascript">
  $(function() {
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

  function saveContent() {    
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
              window.location = "<?php echo asset_url(); ?>/settings";
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
                window.location = "<?php echo asset_url(); ?>/settings";
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
                window.location = "<?php echo asset_url(); ?>/settings";
              }, 500);              
            });
          }        
          //$(".btn").removeAttr("disabled");
          //window.location = "<?php echo asset_url(); ?>/settings";
        },
        error : function(e) {
          gBusy = false;
          gOverlay.hide();

          toastr.error("Proses <b>GAGAL</b>");     
          $(".btn").removeAttr("disabled");   
          window.location = "<?php echo asset_url(); ?>/settings";
        }
      };  
      $('#formUploadData').submit(function() {
        $(this).ajaxSubmit(optFormContentUpdate); 
        // return false to prevent standard browser submit and page navigation
        return false;
      });
    }
  }
</script>
@stop