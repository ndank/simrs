<?= $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        function loading() {
            var url = '<?= base_url('referensi/setting_kas') ?>';
            $('#loaddata').load(url);
        }
        $(function() {
            $('#tanggal').datetimepicker();
            $('#form_setting_kas').submit(function() {
                if ($('#saldo').val() == '') {
                    custom_message('Peringatan','Akhir saldo tidak boleh kosong!');
                    $('#saldo').focus();
                    return false;
                }
                var url = $(this).attr('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        $('input').attr('disabled', 'disabled');
                        $('button[type=submit]').hide();
                        alert_tambah();
                    }
                })
                return false;
            })
            $('button[id=reset]').click(function() {
                loading();
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
        })
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('referensi/setting_kas_save', 'id=form_setting_kas') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
        <tr><td>Waktu</td><td><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <?= form_hidden('transaksi', 'Posisi Kas Awal', 'size=40') ?>
        <tr><td>Akhir Saldo</td><td><?= form_input('akhir_saldo', null, 'onkeyup=FormNum(this) id=saldo style="text-align:right"') ?>
        <tr><td></td><td><?= form_submit('simpan', 'Simpan', 'id=simpan') ?> <?= form_button('reset', 'Reset', 'id=reset') ?>
        </table>
    </div>
    <?= form_close() ?>
</div>