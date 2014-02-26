<script type="text/javascript" src="<?= base_url('assets/js/jquery.cookies.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        
        $('#logout').click(function() {
            localStorage.setItem("menu", null);
            localStorage.setItem("nama", null);
            localStorage.setItem("modul", null);
            localStorage.setItem("login", null);
            location.href="<?= base_url('user/logout') ?>";
        });

        $("#menuutama").click(function(){
            location.reload();
        });
    });
    renderTime();
    function ganti_pwd(){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/ganti_password') ?>',
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }
</script>
<div class="main-menu-user">
    <div id="useractive"><span><?= $this->session->userdata('nama') ?> ( <?= $this->session->userdata('unit') ?> )</span>  <div id="jam" style="color: #fff;"></div> </div>

    <img src="<?= base_url('assets/images/icons/home.png') ?>" align="left" id="image-home" /> 
    <div class="menu-root">
        <span id="menuutama">SIM Pelayanan Rumah Sakit </span>
        <span class="logoutbutton" onclick="ganti_pwd();"> Ganti Password </span>
        <span class="logoutbutton" id="logout"> Logout </span>
    </div>
   
</div>

