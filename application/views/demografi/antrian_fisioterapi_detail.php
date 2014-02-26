<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function(){
            $( "#entry" ).button({icons: {secondary: "ui-icon-circle-check"}}).focus();
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            
            }).hide();
        });

        function cetak_antrian(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("demografi/antrian_fisioterapi_confirm") ?>/'+id,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#entry').hide();
                    $('#reset').show();
                    var dWidth = $(window).width();
                    var dHeight= $(window).height();
                    var x = screen.width/2 - dWidth/2;
                    var y = screen.height/2 - dHeight/2;
                    window.open('<?= base_url() ?>pendaftaran/cetak_no_antri/'+data.id_pelayanan_kunjungan, 'Cetak Nomor Antri Pendaftaran', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
                }
            });
        }
    
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?php if ($pasien != null): ?>
            <div class="data-input">
                <fieldset>
                    <tr><td>Nama:</td><td><span class="label"><?= $pasien->nama ?></span>
                    <tr><td>Tgl&nbsp;Pelayanan:</td><td><span class="label"><?= datetimefmysql($pasien->tanggal) ?></span>
                    <tr><td>Unit Layanan:</td><td><span class="label">Fisioterapist</span>
                    <tr><td>No. Antrian:</td><td><span class="label"><span style="font-size:25px;"><?= $pasien->no_antri ?></span></span>
                    <tr><td></td><td><?= form_button('', 'Konfirmasi Antrian Fisioterapi', "id=entry onclick=cetak_antrian('". $pasien->id_kunjungan ."')") ?>
                    <?= form_button('','Reset', 'id=reset') ?>
                </table>
            </div>

            <div class="data-input">
                <fieldset>
                    <tr><td>Jenis kelamin:</td><td><span class="label"><?= ($pasien->gender == 'L') ? 'Laki-laki' : '' ?><?= ($pasien->gender == 'P') ? 'Perempuan' : '' ?></span>         
                    <tr><td>Umur:</td><td><span class="label"><?= ($pasien->lahir_tanggal != '')?hitungUmur($pasien->lahir_tanggal):'' ?></span>
                    <tr><td>Telepon:</td><td><span class="label"><?= $pasien->telp ?></span>
                    <tr><td>Golongan darah:</td><td><span class="label"><?= ($pasien->darah_gol == '') ? '-' : $pasien->darah_gol ?></span>
                    <tr><td></td><td>
                    
                    
                    <tr><td>Alamat Jalan:</td><td><?= form_textarea('alamat', isset($pasien)?$pasien->alamat:NULL, 'id=alamat readonly style="border:none;color:black;overflow: hidden;resize:none;width:18em"')?>
                    <tr><td>Desa/kelurahan:</td><td><span class="label"><?= $pasien->kelurahan ?></span>
                    <tr><td>Kecamatan:</td><td><span class="label"><?= $pasien->kecamatan ?></span>
                    <tr><td>Kabupaten/kota:</td><td><span class="label"><?= $pasien->kabupaten ?></span>
                    <tr><td>Provinsi:</td><td><span class="label"><?= $pasien->provinsi ?></span>                


                </table>
            </div>
        </div>
<?php else: ?>

    <div class="circle">
        <center>
            <h1>Data Tidak Ditemukan</h1>
        </center>
    </div>
<?php endif; ?>
<?php die; ?>