<script>
    $(function(){
        $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
         $('input[type=submit]').each(function(){
         $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
          });
        $("#cari_nama").button({icons: {secondary: 'ui-icon-search'}});
        $("#form_tab1").submit(function(){           
            get_tab1_list(1);
            return false;
        });

        $(".tanggal").datepicker({
            changeYear : true,
            changeMonth : true
        });

        $('#kelurahan').autocomplete("<?= base_url('demografi/get_kelurahan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $("input[name=id_kelurahan]").val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $("input[name=id_kelurahan]").val(data.id);
                $('#addr').html("Kec : "+data.kecamatan+", Kab : "+data.kabupaten+", Prov : "+data.provinsi);
            });
    
    });
    
    function get_tab1_list(p){
        $.ajax({
            url: '<?= base_url('pendaftaran/search_by_nama_post/') ?>/'+p,
            data: $('#form_tab1').serialize(),
            cache: false,
            success: function(msg) {
                $('#result').html(msg);                       
            }
        })
    
    }
    
    function paging(page, tab,search){
        get_tab1_list(page);
    }
</script>
<?= form_open("pendaftaran/search",'id=form_tab1') ?>
<div class="data-input">
<fieldset>
    <tr><td>Tanggal Antrian:</td><td><?= form_input('tanggal',date('d/m/Y'),'class=tanggal size=10')?>
    <tr><td>Nama Pasien:</td><td><?= form_input('nama', null, 'id = nama size=40') ?>
    <tr><td>Alamat Jalan:</td><td><?= form_textarea('alamat','','id=alamat')?>
    <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', '', 'id=kelurahan size=40') ?> 
    <?= form_hidden('id_kelurahan') ?>    
    <tr><td></td><td><span class="label" id="addr"></span>
    <tr><td></td><td><?= form_submit('cari', 'Cari', 'id = cari_nama class=cari') ?>
    <?= form_button('', 'Reset', 'class=resetan onClick=reset_all(3)') ?>
</table>
</div>

<?= form_close() ?>
<div id="result"></div>
<?php die ?>