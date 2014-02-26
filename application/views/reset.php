<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('button[id=reset]').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    })
    $('#reset').click(function() {
         $('<div></div>')
              .html("Anda yakin akan me reset data transaksi & data referensi ?")
              .dialog({
                 title : "Hapus Data",
                 modal: true,
                 closeOnEscape: true,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        id : "btOk",
                        click: function() { 
                             $.ajax({
                                url: '<?= base_url('inisialisasi/delete_data') ?>',
                                cache: false,           
                                dataType: 'json',
                                success: function(data) {
                                    if (data.status == true) {
                                        alert_resets();
                                    }
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
           });
    })
})
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <fieldset>
            Menu ini digunakan untuk menghapus data transaksi & referensi
        </table>
        <?= form_button(NULL, 'Reset Data', 'id=reset') ?>
    </div>
</div>