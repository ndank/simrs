<script type="text/javascript" src="<?= base_url() ?>assets/js/library.js"></script>
<script>
    $(function(){
         $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
         $('input[type=submit]').each(function(){
         $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
          });
         $('#cari_antri').button({icons: {secondary: 'ui-icon-search'}});

        $("#form_tab2").submit(function(){            
            get_tab2_list(1);
            return false;
        });
        $(".tanggal").datepicker({
            changeYear : true,
            changeMonth : true
        });
    
    });
    
    function get_tab2_list(p){  
        $.ajax({
            url: '<?= base_url('pendaftaran/search_by_no_antri_post/') ?>/'+p,
            data: $('#form_tab2').serialize(),
            cache: false,
            success: function(msg) {
                $('#result2').html(msg);                       
            }
        });    
    }
    
    function paging(page, tab,search){
        get_tab2_list(page);
    }
</script>
<?= form_open("pendaftaran/search",'id=form_tab2') ?>
    <table class="inputan" width="100%">
        <tr><td>Tanggal Antrian:</td><td><?= form_input('tanggal2',date('d/m/Y'),'class=tanggal size=10')?></td></tr>
        <tr><td>Jenis Layanan:</td><td><?= form_dropdown('unit', $layanan, '', 'id = unit_layan') ?></td></tr>
        <tr><td>No. Antrian:</td><td><?= form_input('antri', null, 'id = no_antri onkeyup="Angka(this)" size=40') ?></td></tr>
        <tr><td></td><td><?= form_submit('cari', 'Cari', 'id=cari_antri class=cari') ?>
        <?= form_button('', 'Reset', 'class=resetan onClick=reset_all(1)') ?></td></tr>
    </table>


<?= form_close() ?>
<div id="result2">

</div>
<?php die ?>