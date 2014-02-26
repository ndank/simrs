<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function(){
            $( "#edit_demo" ).button({icons: {secondary: "ui-icon-pencil"}});
            $( "#entry" ).button({icons: {secondary: "ui-icon-circle-check"}});
            $( "#edit_demo" ).click(function(){
                var id = $('input[name=no_rm]').val();
                var id_antri = $('input[name=id_antri]').val();
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('demografi/edit') ?>/'+id+'/'+id_antri,
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                });
            });
        
            $( "#entry" ).click(function(){
                var id = $('input[name=no_rm]').val();
                var id_antri = $('input[name=id_antri]').val();
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('pendaftaran/new_pasien') ?>/'+id+'/'+id_antri,
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html('<div class=kegiatan>'+data+'</div>');
                    }
                });
            });
        });
    
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?php if ($pasien != null): ?>
        <?php foreach ($pasien as $row): ?>
            <?= form_open('demografi/edit_get') ?>

            <?= form_hidden('id_antri', $id_antri)?>
            <div class="data-input">
                <fieldset>
                    <tr><td>Nama pasien:</td><td><span class="label"><?= $row->nama ?></span>
                    <tr><td>No. rekam medik:</td><td><span class="label"><?= $row->no_rm ?><?= form_hidden('no_rm', $row->no_rm) ?></span>
                    <tr><td></td><td><?= form_button('', 'Edit', 'id=edit_demo') ?>
                    <?= form_button('', 'Kunjungan', 'id=entry') ?>
                </table>
            </div>

            <div class="data-input">
                <fieldset>
                    <div class="left_side">
                    <tr><td>Jenis kelamin:</td><td><span class="label"><?= ($row->gender == 'L') ? 'Laki-laki' : '' ?><?= ($row->gender == 'P') ? 'Perempuan' : '' ?></span>         
                    <tr><td>Tempat kelahiran:</td><td><span class="label"><?= $row->tempat_lahir ?></span>
                    <tr><td>Umur:</td><td><span class="label"><?= ($row->lahir_tanggal != '')?hitungUmur($row->lahir_tanggal):'' ?></span>
                    <tr><td>Telepon:</td><td><span class="label"><?= $row->telp ?></span>
                    <tr><td>Golongan darah:</td><td><span class="label"><?= ($row->darah_gol == '') ? '-' : $row->darah_gol ?></span>
                    <tr><td>Agama:</td><td><span class="label"><?= ($row->agama == '') ? '-' :$row->agama ?></span>
                    <tr><td>Pendidikan:</td><td><span class="label"><?= ($row->pendidikan != '') ? $row->pendidikan : '-' ?></span>
                    <tr><td>Pekerjaan:</td><td><span class="label"><?= ($row->pekerjaan != '') ? $row->pekerjaan : '-' ?></span>
                    <tr><td>Status pernikahan:</td><td><span class="label"><?= $row->pernikahan ?></span>
                    <tr><td></td><td>
                    </div>
                    <div class="right_side">
                    <tr><td>Alamat Jalan:</td><td><?= form_textarea('alamat', isset($row)?$row->alamat:NULL, 'id=alamat readonly style="border:none;color:black;overflow: hidden;resize:none;width:18em"')?>
                    <tr><td>Desa/kelurahan:</td><td><span class="label"><?= $row->kelurahan ?></span>
                    <tr><td>Kecamatan:</td><td><span class="label"><?= $row->kecamatan ?></span>
                    <tr><td>Kabupaten/kota:</td><td><span class="label"><?= $row->kabupaten ?></span>
                    <tr><td>Provinsi:</td><td><span class="label"><?= $row->provinsi ?></span>      
                    
                    
                    <tr><td></td><td>
                    <tr><td>No. Identitas:</td><td><span class="label"><?= $row->identitas_no ?></span>             


                </table>
            </div>

            <?= form_close() ?>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="circle">
        <center>
            <h1>Data Tidak Ditemukan</h1>
        </center>
    </div>
<?php endif; ?>
<?php die; ?>