<script type="text/javascript">
$(function() {
    $('input[type=button]').button();
});
</script>
<?php
    $noo = 1;
    $nom = 0;
    foreach ($list_data as $key => $data) { ?>
    <div style="display: inline-block; width: 100%" class=tr_row>
        <div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">
                <tr><td>No. R/:</td><td><input style="border: none;" type=text name=nr[] id=nr<?= $key ?> value='<?= $noo ?>' class=nr size=20 onkeyup=Angka(this) readonly maxlength=2 />
                <tr><td>Jumlah Permintaan:</td><td><input type=text name=jr[] value="<?= $data->resep_r_jumlah ?>" id=jr<?= $key ?> class=jr size=20 onkeyup=Angka(this) />
                <tr><td>Jumlah Tebus:</td><td><input type=text name=jt[] value="<?= ($data->tebus_r_jumlah != '0')?$data->tebus_r_jumlah:$data->resep_r_jumlah ?>" id=jt<?= $key ?> class=jt onkeyup=Angka(this) size=20 />
                <tr><td>Aturan Pakai:</td><td><input type=text name=ap[] value="<?= $data->pakai_aturan ?>" id=ap<?= $key ?> class=ap size=20 />
                <tr><td>Iterasi:</td><td><input type=text name=it[] value="<?= $data->iter ?>" id=it<?= $key ?> class=it size=10 value="0" onkeyup=Angka(this) />
                <tr><td>Biaya Apoteker</td><td><select onchange="subTotal()" name=ja[] id=ja<?= $key ?>><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option '; if ($value->id == $data->tarif_id) echo 'selected'; echo ' value="'.$value->id.'-'.$value->nominal.'">'.$value->layanan.' '.$value->profesi.' '.$value->nama_jkp.' '.$value->jenis.' '.$value->bobot.' '.$value->kelas.'</option>'; } ?></select>
                <tr><td></td><td><input type=button value="Tambah Kemasan Barang" onclick=add(<?= $key ?>) id="addition<?= $key ?>" />
                <input type=button value="Hapus R/" id="deletion<?= $key ?>" onclick=eliminate(this) /> <input type=button value="Etiket" id="etiket<?= $noo ?>" style="display: none" class="etiket" onclick="cetak_etiket(<?= $noo ?>)" />
        </div>
        <div id=resepno<?= $key ?> style="display: inline-block;width: 100%"></div>
    </div>
    <?php 
    $detail = $this->m_resep->detail_data_resep_muat_data($data->id_rr)->result();
    //$message = "";
    foreach($detail as $no => $val) { 
        //$message.=$val->barang.' '.(($val->kekuatan == '1')?'':$val->kekuatan).' '.$val->satuan.' '.$val->sediaan.' '.$val->pabrik.' '.(($val->isi==1)?'':'@'.$val->isi).' '.$val->satuan_terkecil;
        $cek_stok = $this->m_resep->cek_ketersediaan_stok($val->id_packing)->row();
        $redhot="";
        $alert ="";
        if ($val->pakai_jumlah > $cek_stok->sisa) {
            $redhot="style='background: red; color: white;'";
            $alert = "Stok yang tersedia tinggal ".(isset($cek_stok->sisa)?$cek_stok->sisa:'0');
        }
        ?>
         <div class=tr_rows style="width: 100%; display: block;">
                <table align=right width=95% style="border-bottom: 1px solid #f4f4f4" class="detailobat<?= $key ?>">
                <tr><td width=15%>Barcode:</td><td> <input type=text value="<?= isset($val->barcode)?$val->barcode:NULL ?>" name=bc<?= $key ?>[] id=bc<?= $key ?><?= $no ?> class=bc size=30 readonly /></td></tr>
                <tr><td>Kemasan Barang:</td><td>  <input type=text name=pb<?= $key ?>[] value="<?= $val->barang ?> <?= ($val->kekuatan == '1')?'':$val->kekuatan ?>  <?= $val->satuan ?> <?= $val->sediaan ?> <?= $val->pabrik ?> <?= ($val->isi==1)?'':'@'.$val->isi ?> <?= $val->satuan_terkecil ?>" id=pb<?= $key ?><?= $no ?> class=pb size=60 />
                        <input type=hidden name=id_pb<?= $key ?>[] value="<?= $val->id_packing ?>" id=id_pb<?= $key ?><?= $no ?> class=id_pb />
                        <input type=hidden name=kr<?= $key ?>[] value="<?= $val->kekuatan ?>" id=kr<?= $key ?><?= $no ?> class=kr />
                        <input type=hidden name=jp<?= $key ?>[] value="<?= $val->pakai_jumlah ?>" id=jp<?= $key ?><?= $no ?> class=jp /></td></tr>
                <tr><td>Kekuatan:</td><td><span class=label id=kekuatan<?= $key ?><?= $no ?>><?= $val->kekuatan ?></span></td></tr>
                <tr><td>Dosis Racik:</td><td> <input type=text name=dr<?= $key ?>[] value="<?= ($val->dosis_racik != '0')?$val->dosis_racik:$val->kekuatan ?>" id=dr<?= $key ?><?= $no ?> class=dr onkeyup=jmlPakai(<?= $key ?>,<?= $no ?>) size=10 value="" /></td></tr>
                <tr><td <?=$redhot?>>Jumlah Pakai:</td><td><span class=label id=jmlpakai<?= $key ?><?= $no ?>><?= $val->pakai_jumlah ?></span> <span class="label" style="color: red; font-weight: bold;"><?= $alert ?></span></td></tr>
                <tr><td></td><td><span class=label><input type=button value="Hapus" id="deleting<?= $key ?><?= $no ?>" onclick=eliminatechild(this,<?= $key ?>,<?= $no ?>) /></span></td></tr></table>
        </div>
        <script>
            
            $('#pb<?= $key ?><?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                        if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                        if (data.satuan != null) { var satuan = data.satuan; }
                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.id_obat == null) {
                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            if (data.generik == 'Non Generik') {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                            } else {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                            }
                        }
                        return str;
                    },
                    width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){
                    if (data.kekuatan == null) {
                        custom_message('Peringatan','Kekuatan untuk Kemasan Barang yang dipilih tidak boleh null, silahkan ubah pada bagian master data obat');
                        $(this).val('');
                        $('#id_pb<?= $key ?><?= $no ?>').val('');
                        $('#bc<?= $key ?><?= $no ?>').val('');
                        $('#kekuatan<?= $key ?><?= $no ?>').html('');
                        $('#dr<?= $key ?><?= $no ?>').val('');
                        return false;
                    }
                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                    if (data.isi != '1') { var isi = '@ '+data.isi; }
                    if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                    if (data.satuan != null) { var satuan = data.satuan; }
                    if (data.sediaan != null) { var sediaan = data.sediaan; }
                    if (data.pabrik != null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.id_obat == null) {
                        $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                    } else {
                        if (data.generik == 'Non Generik') {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                        } else {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        }
                    }
                    $('#id_pb<?= $key ?><?= $no ?>').val(data.id);
                    $('#bc<?= $key ?><?= $no ?>').val(data.barcode);
                    $('#kekuatan<?= $key ?><?= $no ?>').html(data.kekuatan);
                    $('#dr<?= $key ?><?= $no ?>').val(data.kekuatan);

                    jmlPakai(<?= $key ?>, <?= $no ?>);

                });
        </script>
    <?php }
    $noo++;
    $nom = $nom + $data->nominal;
    }
?>