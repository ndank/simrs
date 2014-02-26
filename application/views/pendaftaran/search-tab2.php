<script>
    $(function(){
        $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
         $('input[type=submit]').each(function(){
         $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
          });

        $(".tanggal").datepicker({
            changeYear : true,
            changeMonth : true
        });

        $("#cari_no_rm").button({icons: {secondary: 'ui-icon-search'}});

        $("#cari_no_rm").click(function(){
            var Dno_rm = $("#no_rm").val();
            if (Dno_rm == '') {
                custom_message('Peringatan','Nomor RM tidak boleh kosong !');
                $("#no_rm").focus();
            } else {
               $.ajax({
                    url: '<?= base_url('pendaftaran/search_by_no_rm_post/') ?>/',
                    data: $('#form_tab3').serialize(),
                    cache: false,
                    success: function(msg) {
                       $("#result3").html(msg);               
                    }
                });
            }

        });
    
    });


</script>

<?= form_open("pendaftaran/search",'id=form_tab3') ?>
<div class="data-input">
    <fieldset>
        <tr><td>Tanggal Antrian:</td><td><?= form_input('tanggal3',date('d/m/Y'),'class=tanggal size=10')?>
        <tr><td>No. RM:</td><td><?= form_input('no_rm', null, 'id = no_rm size=40') ?>
        <tr><td></td><td><?= form_button('cari', 'Cari','id =cari_no_rm class=cari') ?>
        <?= form_button('', 'Reset', 'class=resetan onClick=reset_all(2)') ?>
    </table>
</div>

<?= form_close() ?>
<div id="result3"></div>
<?php die ?>