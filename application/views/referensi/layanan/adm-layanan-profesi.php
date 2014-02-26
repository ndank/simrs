<title><?= $title ?></title>
<div class="kegiatan">
<?= $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    
    $('#reset').click(function() {
        $('#loaddata').load('<?= base_url('referensi/layanan_profesi') ?>');
    })
    $('button[id=reset]').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    });
    $('button[id=addnewrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('#form_layanan_profesi').submit(function() {
        if ($('input[name=id_layanan]').val() == '') {
            custom_message('Peringatan','Layanan tidak boleh kosong!');
            $('#layanan').focus()
            return false;
        }
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#simpan').hide();
                alert_tambah();
                $.ajax({
                    url: '<?= base_url('referensi/layanan_profesi_load_table') ?>/'+$('input[name=id_layanan]').val(),
                    cache: false,
                    success: function(msg) {
                        $('.form-inputan tbody').html(msg)
                    }
                })
            }
        })
        return false;
    })
    for(x = 0; x <= 1; x++) {
        add(x);
    }
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        add(row);
        i++;
    });
    $('#layanan').autocomplete("<?= base_url('inv_autocomplete/load_data_layanan_profesi') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+' - '+((data.bobot == null)?'':data.bobot)+' - '+((data.kelas == null)?'':data.kelas)+'</div>';
            return str;
        },
        width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' '+((data.bobot == null)?'':' - '+data.bobot)+' '+((data.kelas == null)?'':' - '+data.kelas));
        $('input[name=id_layanan]').val(data.id);
        $.ajax({
            url: '<?= base_url('referensi/layanan_profesi_load_table') ?>/'+data.id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg)
            }
        })
    });
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=profesi[] id=profesi'+i+' class=profesi size=45 />'+
                    '<input type=hidden name=id_profesi[] id=id_profesi'+i+' class=id_profesi /></td>'+
                    '<td><select name="posisi[]" style="width: 100%"><option value="Operator">Operator</option>><option value="Anestesi">Anestesi</option>><option value="Asisten">Asisten</option></select></td>'+
                '<td><input type=text name=nominal[] id=nominal'+i+' class=nominal size=5 onKeyup=FormNum(this) /></td>'+
                '<td class=aksi><a class=delete onClick="eliminate(this)"></a></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
        changeYear: true,
        changeMonth: true
    })
    $('#profesi'+i).autocomplete("<?= base_url('referensi/load_data_profesi') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_profesi'+i).val(data.id);
        
    });
}

</script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('referensi/layanan_profesi_save', 'id=form_layanan_profesi') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Summary</legend>
            <tr><td>Layanan</td><td><?= form_input('layanan', null, 'id=layanan size=40') ?><?= form_hidden('id_layanan') ?>
            <tr><td>Total</td><td><span class="label" id="total"></span>
            <tr><td></td><td><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </table>
    </div>
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="60%">Profesi</th>
                <th width="20%">Posisi</th>
                <th width="15%">Nominal</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['id'])) { 
                $stok_opname = adm_layanan_profesi($_GET['id']);
                foreach ($stok_opname as $key => $data) {
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td><?= $data['nama'] ?></td>
                    <td align="center"><?= $data['posisi'] ?></td>
                    <td align="right"><?= rupiah($data['nominal']) ?></td>
                    <td align="center">#</td>
                </tr>
                <?php 
                }
                }
                ?>
            </tbody>
        </table><br/>
        <?= form_submit('submit', 'Simpan', 'id=simpan') ?>
        <?= form_button(null, 'Reset', 'id=reset') ?>
    </div>
    <?= form_close() ?>
</div>