<script type="text/javascript">
    $(function() {
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#batal').button({icons: {secondary: 'ui-icon-refresh'}});
       
        get_privileges_list();
        $('#all').button({
           icons: {
               secondary: 'ui-icon-circlesmall-plus'
           } 
        }).click(function(){
            $(".check").each( function() {
                $(this).attr("checked",'checked');
            });
        });
        $('#uncek').button({
           icons: {
               secondary: 'ui-icon-circlesmall-minus'
           } 
        }).click(function(){
            $(".check").each( function() {
                $(this).removeAttr('checked');
            });
        });
        
        
        $('#batal').click(function(){
            $('#privform').dialog("close");
        });
        
        
       
    });

    function form_submit(){
            var Url = '<?= base_url("referensi/manage_privileges") ?>/add/';
            $.ajax({
                type : 'POST',
                url: Url,              
                data: $('#form_priv').serialize(),
                cache: false,
                success: function(data) {
                    $('#list').html(data);
                    alert_edit_akun();
                
                }
            });
             
        }
    function alert_edit_akun() {
        $( "#edit_akun" ).dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog( "close" );
                }
            }
        });
    }
    
    
</script>
<?= form_open('', 'id = form_priv') ?>
<table width="100%" class="inputan">User Account Permission</legend>
    <div class='msg' id="pesan"></div>
    <table width="100%">
        <tr><td width="15%">ID:</td><td><?= $id ?><?= form_hidden('id_group', $id) ?></td> </tr>
        <tr><td>Nama Profesi:</td><td><?= $nama ?></td> </tr>
    </table>

</table>

<div class="data-list">
    <?= form_button('', 'Check All', 'id=all') ?>
    <?= form_button('', ' Uncheck All', 'id=uncek') ?>
    <div id="list" style="padding-top: 10px;padding-bottom: 10px"></div>
</div>
<?= form_hidden('id_penduduk') ?>

<?= form_close() ?>
<div id="edit_akun" style="display: none" title="Information Alert">
    <p>
        <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
        Data Telah Berhasil di Update
    </p>
</div>

