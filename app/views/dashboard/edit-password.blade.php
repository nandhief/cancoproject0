<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <?php if ($ctlUserData->U_GANTIPASS): ?>
        <meta http-equiv="refresh" content="0;url=<?= asset_url() ?>" />
    <?php endif ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= asset_url(); ?>/assets/images/pintech.png">
    <title><?= getSetting("APP_NAME"); ?></title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="<?= asset_url(); ?>/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="<?= asset_url(); ?>/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?= asset_url(); ?>/assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="<?= asset_url(); ?>/assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="<?= asset_url(); ?>/assets/css/colors.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/loaders/blockui.min.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/core/app.js"></script>
    <script type="text/javascript" src="<?= asset_url(); ?>/assets/js/pages/login.js"></script>
    <!-- /theme JS files -->

    <!-- sweet alert-->
    <script src="<?= asset_url(); ?>/assets/js/sweetalert/sweetalert.min.js"></script>
  <link rel="stylesheet" href="<?= asset_url(); ?>/assets/js/sweetalert/sweetalert.css">
    <style type="text/css">
        #message {display: none; background: #f1f1f1; color: #000; position: relative; padding: 5px; margin-top: 5px; } #message p {padding: 5px 10px; font-size: 10px; } .valid {color: green; display: none; } .valid:before {position: relative; left: -35px; } .invalid {color: red; } .invalid:before {position: relative; left: -35px; }
    </style>
</head>

<body>
    <div class="navbar navbar-inverse" style="background-color: #bb0a0a !important">
    </div>
    <!-- Page container -->
    <div class="page-container login-container">
        <div class="page-content">
            <div class="content-wrapper">
                <div class="content">
                    <form id="form">
                        <div class="panel panel-body login-form">
                            <div class="text-center">
                                <img src="<?= asset_url(); ?>/assets/images/pintech.png" style="width: 270px;"></i>
                                <h4 class="content-group">Ganti Password</h4>
                            </div>
                            <input type="hidden" name="user_id" id="user_id" value="<?= $ctlUserData->U_ID ?>">
                            <div class="form-group has-feedback has-feedback-left">
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Password" name="password" id="password" autofocus>
                                    <span class="input-group-addon"><i class="icon-eye" id="showPass"></i></span>
                                </div>
                                <p class="invalid password">* Password harus mengandung <span class="invalid lower">huruf kecil, </span><span class="invalid capital">huruf besar, </span><span class="invalid number">angka, </span><span class="invalid length">min. 6 karakter</span></p>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-block submit" style="background-color: #bb0a0a; color: #fff" disabled>Update Password </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
     <script>
        function ganti() {
            $.ajax({
                type: 'POST',
                url: '<?= asset_url() ?>/user/change-password',
                data: {
                    user_id: $('#user_id').val(),
                    password: $('#password').val()
                },
                beforeSend: function () {
                    $('.submit').attr('disabled', true);
                },
                success: (result) => {
                    swal({
                        type: 'success',
                        title: 'Berhasil'
                    }, function () {
                        window.location = "<?= asset_url(); ?>";
                    });
                    $('.submit').attr('disabled', false);
                },
                error: (error) => {
                    console.log(error.responseJSON);
                    $('.submit').attr('disabled', false);
                }
            });
        }
        $('#password').keyup(function () {
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
                $('.password').removeClass('invalid');
                $('.password').addClass('valid');
                $('.submit').attr('disabled', false);
            } else {
                $('.password').removeClass('valid');
                $('.password').addClass('invalid');
                $('.submit').attr('disabled', true);

            }
        });
        $('.submit').click(function () {
            return ganti();
        });
        $('#form').submit(function () {
            event.preventDefault();
            return ganti();
        });
        $('#showPass').click(function() {
            if ($(this).hasClass('icon-eye')) {
                $('#password').attr('type', 'text');
                $(this).removeClass('icon-eye');
                $(this).addClass('icon-eye-blocked');
            } else {
                $('#password').attr('type', 'password');
                $(this).removeClass('icon-eye-blocked');
                $(this).addClass('icon-eye');
            }
        });

      </script>
</body>
</html>

