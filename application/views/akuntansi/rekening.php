<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<div class="kegiatan">
    <script type="text/javascript">
    
    $(function() {
        get_list_rekening(1, null);
        $('#simpan').button({
            icons: {
                secondary: 'ui-icon-circle-check'
            }
        });
        $('#cari').button({
            icons: {
                secondary: 'ui-icon-search'
            }
        });
        $('#reset').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        });
        $('#reset').click(function() {
            $('#loaddata').load('<?= base_url('akuntansi/rekening') ?>');
            //get_list_rekening(1, null);
        });
        $('#simpan').click(function() {
            $('#save_ss_rekening').submit();
        });
        $('#save_ss_rekening').submit(function() {
        var rek = $('input[name=id_sub_sub_sub_sub_reks]').val();
        if (rek === '') {
            var url = $(this).attr('action');
        } else {
            var url = '<?= base_url('akuntansi/save_edit_sub_sub_sub_sub_rekening') ?>';
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                $('#list_rekening').html(data);
                //$('#simpan').hide();
                $('#kode_subsubsubsub').val(parseInt($('#kode_subsubsubsub').val())+1);
                //$('input').attr('disabled','disabled');
                $('#pairing, input[name=id_pairing]').val('');
                $('#simpan').hide();
                if (rek === '') {
                    alert_tambah();
                } else {
                    alert_edit();
                }
                $("#example-advanced").treetable({ expandable: true });
                $('#example-advanced').treetable('expandAll');
            },
            error: function() {
                alert_tambah_failed();
            }
        });
        return false;
    });
        $('#cari').click(function() {
            $.ajax({
                url: '<?= base_url('akuntansi/manage_rekening') ?>/search/',
                cache: false,
                data: $('#save_ss_rekening').serialize(),
                type: 'POST',
                success: function(data) {
                    $('#list_rekening').html(data);
                    $('#example-advanced').treetable('expandAll');
                }
            });
        });
        $('#pairing').autocomplete("<?= base_url('akuntansi/get_sub_sub_sub_sub_rekening/extra') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_pairing]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+' <br/> '+data.nama+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('input[name=id_pairing]').val(data.id_sub_sub_sub_sub_rekening);
        });
    });
    function get_list_rekening(page, search) {
        $.ajax({
            url: '<?= base_url('akuntansi/manage_rekening') ?>/list/'+page,
            cache: false,
            success: function(data) {
                $('#list_rekening').html(data);
            }
        });
    }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Atribut</legend>
        <?= form_open('akuntansi/save_sub_sub_sub_sub_rekening', 'id=save_ss_rekening') ?>
            <?= form_hidden('id_sub_sub_sub_sub_reks') ?>
            <label style="min-width: 200px;">Kode Sub Sub Sub Sub:</td><td><?= form_input('kode_subsubsubsub', get_last_id('sub_sub_sub_sub_rekening', 'id'), 'size=30 onKeyup="Angka(this)" id=kode_subsubsubsub') ?>
            <label style="min-width: 200px;">Rekening:</td><td><span class="label" id="nama_rekening">-</span>
            <label style="min-width: 200px;">Sub Rekening:</td><td><span class="label" id="nama_sub_rekening">-</span>
            <label style="min-width: 200px;">Sub Sub Rekening:</td><td><span class="label" id="nama_sub_sub_rekening">-</span>
            <label style="min-width: 200px;">Sub Sub Sub Rekening:</td><td><span class="label" id="nama_sub_sub_sub_rekening">-</span>
            <label style="min-width: 200px;">Nama Sub Sub Sub Sub:</td><td><?= form_input('nama_ssss', NULL, 'id=nama_ssss size=40') ?>
            <label style="min-width: 200px;"></td><td><?= form_button(null, 'Simpan', 'id=simpan') ?> <!-- <?= form_button(null, 'Cari','id=cari') ?> --> <?= form_button(null, 'Reset', 'id=reset') ?>
        <?= form_close() ?>
        </table>
    </div>
    <div id="list_rekening"></div>
</div>