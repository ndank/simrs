<html>
    <head>
        <title><?= $title ?></title>
        <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-1.8.3.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-1.9.2.custom.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.cookies.js') ?>"></script>
        
        <script type="text/javascript">
        $(document).ready(function(){
            $.cookie('url', null);
            $('#username').focus();
            $('.warning').hide();
            $('input').live('keyup', function(e) {
                if (e.keyCode===13) {
                    loginForm();
                }
            });
        });
        function loginForm() {
            var Url = '<?= base_url('user/login') ?>';
                $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#loginform').serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if(data.id_user !== ''){
                            location.href='<?= base_url('') ?>';
                        } else {
                            $('#username-label').html('Username :');
                            $('#password-label').html('Password :');
                            $('#username').focus().select();
                            $('#username-check .loadingbar,#password-check .loadingbar').fadeOut();
                            $('.loading').hide();
                            $('.warning').show().html('Username atau password yang Anda masukkan salah !');
                        }            
                    }, error: function() {
                        $('.loading').hide();
                        $('.warning').show().html('Username atau password yang Anda masukkan salah !');
                    }
                });
                return false;
        }
        </script>
    </head>
<body class="body-login">
    <div class="container">
	<section id="content">
            <form id="loginform">
			<h1>Login Form</h1>
			<div>
				<input type="text" name="username" placeholder="Username" required="" id="username" />
			</div>
			<div>
				<input type="password" name="password" placeholder="Password" required="" id="password" />
			</div>
			<div>
                            <input type="button" onclick="loginForm();" value="Log in" />
				<a href="#">Lost your password?</a>
				<a href="#">Register</a>
			</div>
            </form><!-- form -->
		<div class="button">
                    <a href="#">&COPY; 2014 MedicalPro</a>
		</div><!-- button -->
	</section><!-- content -->
    </div><!-- container -->
</body>
</html>
