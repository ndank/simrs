<script type="text/javascript">
$(function() {
   $('.print').click(function() {
        var id_nota = $(this).attr('title');
        var pembayaran_ke = $(this).attr('id');
        var wWidth = $(window).width();
        var dWidth = wWidth * 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url('billing/cetak') ?>/'+id_nota+'/'+pembayaran_ke, 'cetakbilling', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
});
$(function() {
    $('.print-simple').click(function() {
        var id_nota = $(this).attr('title');
        var pembayaran_ke = $(this).attr('id');
        var wWidth = $(window).width();
        var dWidth = wWidth* 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url('billing/cetak_simple') ?>/'+id_nota+'/'+pembayaran_ke, 'cetakbilling', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    })
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);      
}

function hapus_pembayaran(obj, id_nota, no_daftar){
    $('<div></div>')
          .html("Anda yakin akan menghapus data ini ?")
          .dialog({
             title : "Hapus Data",
             modal: true,
             buttons: [ 
                { 
                    text: "Ok", 
                    click: function() { 
                       $.ajax({
                            type : 'GET',
                            url: '<?= base_url("billing/hapus_pembayaran") ?>/'+id_nota,
                            cache: false,
                            success: function(data) {
                                eliminate(obj);
                                update_pembayaran(no_daftar);
                                alert_delete();
                            },
                            error : function(){
                                alert_delete_failed();
                            }
                        });
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
            ]
        });
}
</script>
<div class="data-list">
    <?php if ($total_data == 0) { ?>
        Belum ada data pembayaran 
    <?php } else { ?>
        <?php 
        $total = 0;
        foreach ($list_data as $key => $data) { 
            $total = $total+$data->total;
        } ?>
        <table class="list-data" width="100%">
            <tr>
                <th width="3%">No.</th>
                <th width="17%">Waktu</th>
                <th width="20%">Tagihan</th>
                <th width="20%">Bayar</th>
                <th width="15%">Sisa Tagihan</th>
                <th width="25%">#</th>
            </tr>
    <?php 
    $run_total = $total;
    foreach ($list_data as $key => $data) { 
        ?>
            <tr class="tr_rows <?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= ++$key ?></td>
                <td align="center"><?= datetime($data->waktu) ?></td>
                <td align="right"><?= rupiah($data->total) ?></td>
               <!-- <td align="right"><?= rupiah($run_total) ?></td> -->
                <td align="right" id="bayar<?= $key ?>"><?= rupiah($data->bayar) ?></td>
                <td align="right" id="sisa<?= $key ?>"><?= rupiah($data->sisa) ?></td>
              <!--  <td align="right" id="sisa<?= $key ?>"><?= ((($run_total-$data->bayar) <= 0)?'0':rupiah($run_total-$data->bayar)) ?></td>-->
                <td align="center" class="aksi">
                    <!--<?= form_button($key . '/' . $rows->no_daftar . '/' . $rows->no_daftar, 'Cetak Simple', 'title="' . $data->id_nota . '" class="print-simple" id=cetak' . $key) ?>
                    <?= form_button($key . '/' . $rows->no_daftar . '/' . $rows->no_daftar, 'Cetak', 'title="' . $data->id_nota . '" class="print" id=cetak' . $key) ?>
                    <?php $js = 'onclick="hapus_pembayaran(this, '.$data->id_nota.' , '. $rows->no_daftar .')"' ?>
                    <?= form_button('','Hapus','class=hapus '.$js) ?>-->
                    <?php $js = 'onclick="hapus_pembayaran(this, '.$data->id_nota.' , '. $rows->no_daftar .')"' ?>
                    <span class="link_button print-simple" title="<?= $data->id_nota ?>" id="<?= $key . '/' . $rows->no_daftar . '/' . $rows->no_daftar ?>" >Nota Simple</span>
                    <span class="link_button print" title="<?= $data->id_nota ?>" id="<?= $key . '/' . $rows->no_daftar . '/' . $rows->no_daftar ?>" >Nota Rincian</span>
                    <span class="link_button hapus" title="<?= $data->id_nota ?>" <?= $js ?> >Hapus</span>
                </td>
            </tr>
            <script>
                $(function() {

                    $('button[id=cetak<?= $key ?>]').button({
                        icons: {
                            secondary: 'ui-icon-print'
                        }
                    });
                });
            </script>
    <?php 
    $run_total = $run_total-$data->bayar; 
    } ?>
        </table>
    <?php } ?>
</div>

<?php die ?>