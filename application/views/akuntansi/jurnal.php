<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<div class="kegiatan">
    <script type="text/javascript">
    $(function() {
        $('#tanggal').datetimepicker();
        $('#simpan').button({
            icons: {
                secondary: 'ui-icon-circle-check'
            }
        });
        $('#reset').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        });
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('akuntansi/jurnal') ?>');
        });
        $('#simpan').click(function() {
            $('#form_jurnal').submit();
        });
        $('#nilai').blur(function() {
            $('#nilai_label').html($(this).val());
        });
        $('#form_jurnal').submit(function() {
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function() {
                    $('input,select').attr('disabled','disabled');
                    alert_tambah();
                }
            });
            return false;
        });
        $('#pairing').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
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
                var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.rekening+' - '+data.sub_rekening+' - '+data.sub_sub_rekening+' - '+data.sub_sub_sub_rekening+' - '+data.sub_sub_sub_sub_rekening+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
            $('input[name=reference]').val(data.id_sub_sub_sub_sub_rekening);
            $('#rekening').val(data.rekening+' '+data.sub_rekening+' '+data.sub_sub_rekening+' '+data.sub_sub_sub_rekening+' '+data.sub_sub_sub_sub_rekening);
            $('#nama_rekening').html(data.rekening+' '+data.sub_rekening+' '+data.sub_sub_rekening+' '+data.sub_sub_sub_rekening+' '+data.sub_sub_sub_sub_rekening);
            $('#ref').html(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
        });
    });
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('akuntansi/jurnal_save', 'id=form_jurnal') ?>
        <table width="100%" class="inputan">Tambah Jurnal</legend>
        <tr><td>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <tr><td><b>Rekening Debet</b></td><td>
        <tr><td>Nama Rekening:</td><td><?= form_input('rekening', NULL, 'id=rekening size=40') ?>
        <tr><td>Ref.:</td><td><?= form_input('ref', null, 'id=pairing size=40') ?><?= form_hidden('reference') ?>
<!--        <tr><td>Jenis:</td><td><span class="label"><?= form_radio('jenis', 'debet', TRUE, 'id=debet') ?> Debet</span>
        <span class="label"><?= form_radio('jenis', 'kredit', FALSE, 'id=kredit') ?> Kredit</span>-->
        <tr><td>Nilai (Rp.):</td><td><?= form_input('nilai', NULL, 'id=nilai onkeyup="FormNum(this)"') ?>
        <tr><td><b>Rekening Kredit (Pairing)</b></td><td>
        <tr><td>Nama Rekening:</td><td><span class="label" id="nama_rekening"></span>
        <tr><td>Ref:</td><td><span class="label" id="ref"></span>
        <tr><td>Nilai (Rp.):</td><td><span class="label" id="nilai_label"></span>
        
        <tr><td></td><td><?= form_button(NULL, 'Simpan', 'id=simpan') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>
</div>