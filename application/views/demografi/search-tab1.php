<script type="text/javascript">
    $(function(){
        $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        $("#cari_no").button({icons: {secondary: 'ui-icon-search'}});
        $("#cari_no").click(function(){
            get_list_no_rm(1);
        });
            
    });

    $('#formnorm').submit(function(){
        return false;
    });



    function get_list_no_rm(page){
        $.ajax({
            url: '<?= base_url() ?>demografi/search_by_no_rm_post/'+page,
            data: 'no_rm='+ $("#no_rm").val(),
            success: function( response ) {
                $("#hasil_no_rm2").html(response);
            }           
        });
    }

    
</script>

<div class="data-input">
    <table width="100%" class="inputan">
        <?= form_open('demografi/search', 'id=formnorm') ?>
        <tr><td>Nomor RM:</td><td><?= form_input('no_rm','', 'id = no_rm size=40 placeholder="Semua pasien ..."') ?>
        <tr><td></td><td><?= form_button('cari', 'Cari', 'id = cari_no') ?>
        <?= form_button('', 'Reset', 'class=resetan onClick=reset_all(1)') ?>
        <?= form_close() ?>
    </table>
</div>
<?= form_close() ?>
<div id="hasil_no_rm2">
        
</div>
<?php die; ?>