<script type="text/javascript">
    $(function(){
        $('.detail_kunjungan').click(function() {
            $('.tr').removeClass('selected');
            var no = $(this).attr('id');
            $('#tr'+no).addClass('selected');
        });
    });

    function load_penjualan_jasa(no_daftar){
         $.ajax({
                type: 'GET',
                url: '<?= base_url ()?>pelayanan/penjualan_jasa/'+no_daftar,
                cache : false,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            });
    }

    function rincian(no_daftar, nama){
         $.ajax({
            url: '<?= base_url("billing/rincian_billing") ?>/'+no_daftar,
            data : 'nama='+nama,
            cache: false,
            success: function(data) {
                
                $('<div>'+data+'</div>').dialog({
                    autoOpen: true,
                    title :'Rincian Billing Pasien',
                    height: $(window).height() - 10,
                    width: $(window).width() - 20,
                    modal: true,
                    resizable : false,
                    buttons: [ 
                        { text: "Ok", click: function() { 
                                $( this ).dialog().remove(); 
                            } 
                        }
                    ]
                });
            }
        });
    }
</script>
<div class="data-list">
    <h2>Hasil Pencarian</h2>
    <table class="list-data" width="100%">
        <tr>
            <th width="3%">No.</th>
            <th width="7%">No. RM</th>
            <th width="30%">Nama Pasien</th>
            <th width="55%">Alamat</th>
            <th width="5%">Aksi</th>
        </tr>
        <?php foreach ($list_data_kunjungan as $key => $data) { ?>
        <tr id="tr<?= $data->no_daftar ?>" class="tr">
            <td align="center"><span class="link_button" title="Klik untuk menambah penjualan jasa" onclick="load_penjualan_jasa('<?= $data->no_daftar ?>')"><?= $data->no_daftar ?></span></td>
            <td align="center"><?= ($data->no_rm !== null)?str_pad($data->no_rm, 6,"0",STR_PAD_LEFT):'-' ?></td>
            <td><?= $data->nama ?></td>
            <td><?= $data->alamat ?> <?= $data->kelurahan ?> <?= $data->kecamatan ?> <?= $data->kabupaten ?> <?= $data->provinsi ?></td>
            <td align="center"><span id="<?= $data->no_daftar ?>" class="link_button detail_kunjungan" title="Klik untuk melihat detail billing" onclick="rincian('<?= $data->no_daftar ?>', '<?= $data->nama ?>')">Detail</span></td>
        </tr>
        <?php } ?>
    </table>
    
    <div><?= $paging ?></div>
    <br/><br/>
</div>
