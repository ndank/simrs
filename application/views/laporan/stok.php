<title><?= $title ?></title>
<?php $this->load->view('message') ?>
<?php
    $sorter = array('History','Terakhir','Kosong','ED','Death Stock');
?>

<div id="result_detail" style="display: none"></div>
<div id="stelling_cetak" style="display: none"></div>
<div class="kegiatan">
    
    <script type="text/javascript">
        $(function() {
            $(".wmd-view-topscroll").scroll(function(){
                $(".data-list").scrollLeft($(".wmd-view-topscroll").scrollLeft());
            });
            $(".data-list").scroll(function(){
                $(".wmd-view-topscroll").scrollLeft($(".data-list").scrollLeft());
            });
            $("#table").tablesorter();
            $('#excelpsi,#cetakabc').hide();
            $("a[href='#top']").click(function() {
                $("#loaddata").animate({ scrollTop: 0 }, "slow");
                return false;
            });
            <?php if (isset($_GET['perundangan']) and $_GET['perundangan'] == 'Psikotropika') { ?>
                $('#excelpsi').show();
            <?php } ?>
            <?php if (isset($_GET['sort']) and $_GET['sort'] == 'History' and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                $('#cetakabc').show();
            <?php } ?>
                
                $('button[id=reset]').button({
                    icons: {
                        secondary: 'ui-icon-circle-check'
                    }
                });
                $('#cetakrl').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    if (awal == '') {
                        custom_message('Peringatan','Tanggal tidak boleh kosong !');
                        $('#awal').focus();
                        return false;
                    } 
                    if (akhir == '') {
                        custom_message('Peringatan','Tanggal tidak boleh kosong !');
                        $('#akhir').focus();
                        return false;
                    }
                    location.href='<?= base_url('laporan/rekap_laporan') ?>?awal='+awal+'&akhir='+akhir;
            
                })
                $('button[id=reset]').click(function() {
                    $('#hasil').html('');
                })
                $('input[type=submit]').each(function(){
                    $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
                });
                $('button[type=submit]').button({
                    icons: {
                        secondary: 'ui-icon-circle-check'
                    }
                });
                $('button[id=stelling], button[id=excelpsi], button[id=excel], button[id=cetakrl], button[id=cetakabc]').button({
                    icons: {
                        secondary: 'ui-icon-print'
                    }
                })
                $('#excel').click(function() {
                    var perundangan = '<?= (isset($_GET['perundangan']) and $_GET['perundangan'] != '') ? $_GET['perundangan'] : NULL ?>';
                    if (perundangan != 'Narkotika') {
                        location.href='<?= base_url('laporan/print_stok') ?>?<?= generate_get_parameter($_GET) ?>';
                    } else {
                        var awal = $('#awal').val();
                        var akhir= $('#akhir').val();
                        window.open('<?= base_url('laporan/narkotika') ?>?awal='+awal+'&akhir='+akhir,'mywindow','location=1,status=1,scrollbars=1,width=730px,height=500px');
                    }
                })
                $('#cetakabc').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    location.href='<?= base_url('laporan/laporan_abc') ?>?awal='+awal+'&akhir='+akhir;
                })
                $('#stelling').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    var id = $('input[name=id_pb]').val();
                    if (id != '') {
                        $.ajax({
                            url: '<?= base_url('laporan/stelling') ?>',
                            data: 'id_pb='+id+'&awal='+awal+'&akhir='+akhir,
                            cache: false,
                            success: function(data) {
                                $('#stelling_cetak').html(data);
                                $('#stelling_cetak').dialog({
                                    autoOpen: true,
                                    height: 400,
                                    width: 870,
                                    modal: true
                                });
                            }
                        })
                        //window.open('<?= base_url('cetak/inventory/stelling') ?>?id_pb='+id+'&awal='+awal+'&akhir='+akhir,'mywindow','location=1,status=1,scrollbars=1,width=830px,height=500px');
                    } else {
                        custom_message('Peringatan','Pilih terlebih dahulu nama Kemasan!');
                    }
                })
                $('#excelpsi').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    var id = $('input[name=id_pb]').val();
                    $.ajax({
                        url: '<?= base_url('laporan/psikotropika') ?>',
                        data: '<?= generate_get_parameter($_GET) ?>',
                        cache: false,
                        success: function(data) {
                            $('#stelling_cetak').html(data);
                            $('#stelling_cetak').dialog({
                                autoOpen: true,
                                height: 400,
                                width: 870,
                                modal: true
                            });
                        }
                    });
                });
                $('#awal, #akhir').datetimepicker({
                    changeYear: true,
                    changeMonth: true
                });
                $('#sort').change(function() {
                    if ($(this).val() === 'History') {
                        $('#awal').val('<?= date("d/m/Y 00:00") ?>').removeAttr('disabled', 'disabled');
                        $('#akhir').val('<?= date("d/m/Y H:i") ?>').removeAttr('disabled', 'disabled');
                    }
                    if ($(this).val() === 'Terakhir' || $(this).val() === 'Kosong' || $(this).val() === 'ED') {
                        $('#awal,#akhir').val('').attr('disabled','disabled');
                    }
                });
                $('#history').click(function() {
                    if ($('#history').is(':checked') === true) {
                        $('#awal,#akhir').removeAttr('disabled', 'disabled');
                    }
                });
                $('#pb').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
                {
                    parse: function(data){
                        var parsed = [];
                        for (var i=0; i < data.length; i++) {
                            parsed[i] = {
                                data: data[i],
                                value: data[i].nama // nama field yang dicari
                            };
                        }
                        $('input[name=id_pb]').val('');
                        return parsed;
                
                    },
                    formatItem: function(data,i,max){
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = ''; var kemasan = '';
                        if (data.isi !== '1') { var isi = '@ '+data.isi; }
                        if (data.kekuatan !== null) { var kekuatan = data.kekuatan; }
                        if (data.satuan !== null) { var satuan = data.satuan; }
                        if (data.sediaan !== null) { var sediaan = data.sediaan; }
                        if (data.pabrik !== null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.satuan_terbesasr !== null) { kemasan = data.satuan_terbesar; }
                        if (data.id_obat === null) {
                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            if (data.generik === 'Non Generik') {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+kemasan+' '+isi+' '+satuan_terkecil+'</div>';
                            } else {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil+'</div>';
                            }
                        }
                        return str;
                    },
                    width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                    cacheLength: 0
                }).result(
                function(event,data,formated){
                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = ''; var kemasan = '';
                    if (data.isi !== '1') { var isi = '@ '+data.isi; }
                    if (data.kekuatan !== null) { var kekuatan = data.kekuatan; }
                    if (data.satuan !== null) { var satuan = data.satuan; }
                    if (data.sediaan !== null) { var sediaan = data.sediaan; }
                    if (data.pabrik !== null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.satuan_terbesasr !== null) { kemasan = data.satuan_terbesar; }
                    if (data.id_obat === null) {
                        $(this).val(data.nama+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil);
                    } else {
                        if (data.generik === 'Non Generik') {
                            $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+kemasan+' '+isi+' '+satuan_terkecil);
                        } else {
                            $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil);
                        }
                    }
                    $('input[name=id_pb]').val(data.id);           
                });
                $('#reset').click(function() {
                    $('#loaddata').empty();
                    <?php if ($kat_barang === 'Farmasi') { ?>
                    var url = '<?= base_url('laporan/stok') ?>';
                    <?php } if ($kat_barang === 'Gizi') { ?>
                    var url = '<?= base_url('laporan/stok_gizi') ?>';    
                    <?php } if ($kat_barang === 'Rumah Tangga') { ?>
                    var url = '<?= base_url('laporan/stok_rt') ?>';
                    <?php } ?>
                    $('#loaddata').load(url)
                });
                $('#form_stok').submit(function() {
                    var url = $(this).attr('action');
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: $(this).serialize(),
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(data) {
                            $('#loaddata').html(data);
                        }
                    });
                    return false;
                });
                $('.view_transaction').click(function() {
                    var url = $(this).attr('href');
                    //var title = $(this).attr('id');
                    $.get(url, function(data) {
                        $('#loaddata').html(data);
                        /*$('#result_detail').dialog({
                            title: title,
                            autoOpen: true,
                            height: 500,
                            width: 900,
                            modal: true
                        });*/
                    });
                    return false;
                });
            });
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Filter Inputan</legend>
            <?php
            $url = '';
                if ($kat_barang === 'Farmasi') {
                    $url = 'laporan/stok';
                }
                if ($kat_barang === 'Gizi') {
                    $url = 'laporan/stok_gizi';
                }
                if ($kat_barang === 'Rumah Tangga') {
                    $url = 'laporan/stok_rt';
                }
            ?>
            <?= form_open($url, 'id=form_stok') ?>
            <?php
            $disabled = null;
            if (isset($_GET['sort'])) {
                if ($_GET['sort'] == 'Terakhir') {
                    $disabled = "disabled";
                }
            }
            $iya = FALSE; $tidak = FALSE;
            if (isset($_GET['formularium'])) {
                if ($_GET['formularium'] == 'Ya') {
                    $iya = TRUE;
                }
                if ($_GET['formularium'] == 'Tidak') {
                    $tidak = TRUE;
                }
            }
            $yes = FALSE; $no = FALSE;
            if (isset($_GET['generik'])) {
                if ($_GET['generik'] == 'Ya') {
                    $yes = TRUE;
                }
                if ($_GET['generik'] == 'Tidak') {
                    $no = TRUE;
                }
            }
            ?>
            <tr><td>Urutkan</td><td><select name="sort" id="sort"><?php foreach ($sorter as $data) { ?><option value="<?= $data ?>" <?php if (isset($_GET['sort']) and $_GET['sort'] === $data) { echo 'selected'; } ?>><?= $data ?></option> <?php } ?></select>
            <tr><td>Waktu:</td><td><?= form_input('awal', isset($_GET['awal']) ? $_GET['awal'] : NULL, 'id=awal size=15 ' . $disabled) ?> <span class="label"> s . d </span><?= form_input('akhir', isset($_GET['akhir']) ? $_GET['akhir'] : NULL, 'id=akhir size=15 ' . $disabled) ?>
            <tr><td>Jenis Transaksi:</td><td><?= form_dropdown('transaksi_jenis', $jenis_transaksi, isset($_GET['transaksi_jenis']) ? $_GET['transaksi_jenis'] : null) ?></td></tr>
            <tr><td>Unit:</td><td><?= form_dropdown('unit', $unit, isset($_GET['unit'])?$_GET['unit']:$this->session->userdata('id_unit')) ?>
            <tr><td>Kemasan:</td><td><?= form_input('pb', isset($_GET['pb']) ? $_GET['pb'] : null, 'id=pb style="width: 256px;"') ?> <?= form_hidden('id_pb', isset($_GET['id_pb']) ? $_GET['id_pb'] : null) ?>
            <?php if ($kat_barang == 'Farmasi') { ?>
            <tr><td>Sediaan:</td><td><?= form_dropdown('sediaan', $sediaan, isset($_GET['pb']) ? $_GET['sediaan'] : null) ?>
            <tr><td>Ven:</td><td><?= form_dropdown('ven', array('' => 'Semua Ven ...','Vital' => 'Vital','Esensial' => 'Esensial','Non' => 'Non'),isset($_GET['ven'])?$_GET['ven']:NULL,'id=ven') ?>
            <tr><td></td><td><span class="label"><?= form_radio('formularium', 'Ya', $iya, 'id=ya') ?>Formularium</span><span class="label"> <?= form_radio('formularium', 'Tidak', $tidak, 'id=tidak') ?>Non Formularium</span>
            <tr><td>High Alert:</td><td><span class="label"><?= form_radio('ha', 'Ya', (isset($_GET['ha']) and $_GET['ha'] == 'Ya')?TRUE:FALSE, 'id=ya') ?> Ya</span> <span class="label"><?= form_radio('ha', 'Tidak', (isset($_GET['ha']) and $_GET['ha'] == 'Tidak')?TRUE:FALSE, 'id=tidak') ?> Tidak</span>
            <tr><td>Perundangan:</td><td><?= form_dropdown('perundangan', $perundangan, isset($_GET['perundangan']) ? $_GET['perundangan'] : null) ?>
            <tr><td>Generik:</td><td><span class="label"><?= form_radio('generik', 'Generik', $yes, 'id=ya') ?>Generik</span><span class="label"> <?= form_radio('generik', 'Non Generik', $no, 'id=tidak') ?>Non Generik</span>
            <?php } ?>
            <tr><td></td><td>
            <?= form_hidden('kategori',$kat_barang) ?>
            <?= form_submit('cari', 'Cari', null) ?>  
            <?= form_button('Cetak ', 'Cetak Psikotropika', 'id=excelpsi') ?>    
            <?= form_button('Cetak ', 'Cetak', 'id=excel') ?>
            <?= form_button(null, 'Cetak Kartu Stelling', 'id=stelling') ?>
            <?= form_button('Reset', 'Reset', 'id=reset') ?>
            </table>
            <?= form_close() ?>
            
        </table>
    </div>
    <div class="wmd-view-topscroll">
        <div class="scroll-div1"></div>
    </div>
    <div class="data-list" style="overflow: auto; max-width: 100%;">
        <table class="tabel" id="table" width="100%">
            <thead>
            <tr>
                <?php if(isset($_GET['sort']) and $_GET['sort'] === 'History'): ?>
                <th width="10%">Waktu</th>
                <?php endif; ?>
                <?php if (isset($_GET['sort']) and $_GET['sort'] === 'History') { ?>
                <th width="5%">No.</th>
                <th width="7%">Jenis</th>
                <?php } ?>
                <th width="35%">Kemasan Barang</th>
                <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                <th width="5%">H. Jual</th>
                <?php } ?>
                <?php if (isset($_GET['sort']) and $_GET['sort'] === 'History') { ?>
                <th width="5%">ED</th>
                <th width="5%">Awal</th>
                <th width="5%">Masuk</th>
                <th width="5%">Keluar</th>
                <?php } ?>
                <?php if (isset($_GET['sort']) and ($_GET['sort'] === 'ED' or $_GET['sort'] === 'Death Stock')) { ?>
                <th width="5%">ED</th>
                <?php } ?>
                <th width="5%">Sisa</th>
                <th width="5%">HPP</th>
                <th width="5%">HNA</th>
                <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                <th width="5%">Omset</th>
                <?php } ?>
                <?php if (isset($_GET['sort']) and $_GET['sort'] === 'Terakhir') { ?>
                <th width="5%">Aset</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($_GET['sort'])) {
                $hpp = 0; $sisa = 0; $asset = 0; $omset = 0; $omsetDlmHpp = 0;
                //$stok = stok_barang_muat_data(isset($_GET->awal)?$_GET->awal:NULL, isset($_GET->akhir)?$_GET->akhir:NULL, isset($_GET->id_pb)?$_GET->id_pb:NULL, isset($_GET->atc)?$_GET->atc:NULL, isset($_GET->ddd)?$_GET->ddd:NULL, isset($_GET->perundangan)?$_GET->perundangan:NULL, isset($_GET->generik)?$_GET->generik:NULL, isset($_GET->transaksi_jenis)?$_GET->transaksi_jenis:NULL, isset($_GET->sort)?$_GET->sort:NULL, isset($_GET->unit)?$_GET->unit:NULL);
                foreach ($list_data as $key => $data) {
                    $hna_margin = $data->hna+($data->hna*$data->margin/100);
                    $var_hjual  = $hna_margin - ($hna_margin*($data->diskon/100));
                    $harga_jual = ($var_hjual+($var_hjual*($data->ppn_jual/100)));
                    $extra = NULL;
                    $link = NULL;
                    $jenis = $data->transaksi_jenis;
                    if ($data->transaksi_jenis == 'Pemesanan') {
                        $link = "inventory/pemesanan";
                        $jenis = "S.P";
                    } else if ($data->transaksi_jenis == 'Pembelian') {
                        $link = "inventory/pembelian";
                        $jenis = "Penerimaan";
                    } else if ($data->transaksi_jenis == 'Stok Opname') {
                        $link = "inventory/stok_opname";
                        $jenis = "S.O";
                    } else if ($data->transaksi_jenis == 'Repackage') {
                        $link = "inventory/repackage";
                    } else if ($data->transaksi_jenis == 'Retur Pembelian') {
                        $link = "inventory/retur_pembelian/null";
                        $jenis = "R. Beli";
                    } else if ($data->transaksi_jenis == 'Penjualan') {
                        $jns = $this->db->query("select resep_id from penjualan where id = '".$data->transaksi_id."'")->row();
                        if ($jns->resep_id === NULL) {
                            $jenis = "Penyerahan Non R/";
                            $link = "pelayanan/penjualan_nr";
                        } else {
                            $jenis = "Penyerahan R/";
                            $link = "inventory/penjualan";
                        }
                    } else if ($data->transaksi_jenis == 'Pemusnahan') {
                        $link = "inventory/pemusnahan";
                    } else if ($data->transaksi_jenis == 'Retur Penjualan') {
                        $link = "inventory/retur_penjualan/null";
                        $jenis = "R. Jual";
                    } else if ($data->transaksi_jenis == 'Penerimaan Retur Pembelian') {
                        $link = "inventory/reretur_pembelian";
                        $jenis = "Ret. Beli Penerimaan";
                    } else if ($data->transaksi_jenis == 'Pengeluaran Retur Penjualan') {
                        $link = "inventory/reretur_penjualan";
                        $jenis = "P.R. Jual";
                    } else if ($data->transaksi_jenis == 'Distribusi') {
                        $link = "inventory/distribusi";
                    } else if ($data->transaksi_jenis == 'Pemakaian') {
                        $link = "inventory/pemakaian";
                    } else if ($data->transaksi_jenis == 'Retur Distribusi') {
                        $link = "inventory/retur_distribusi";
                    } else if ($data->transaksi_jenis == 'Penerimaan Distribusi') {
                        $jenis= "P. Dist";
                        $link = "inventory/penerimaan_distribusi";
                    } else if ($data->transaksi_jenis == 'Penerimaan Retur Distribusi') {
                        $link = "inventory/penerimaan_retur_distribusi";
                        $jenis= "P. R. Dist";
                    }

                    $time = mktime(0, 0, 0, date("m")+6, date("d"), date("Y"));
                    $new = date("Y-m-d", $time);
                    
                    if ($data->transaksi_jenis != 'Pemesanan') {
                        if ($data->ed < date("Y-m-d")) {
                            $class = "class=alertred";
                        } else if ($data->ed >= date("Y-m-d") and $data->ed <= $new) {
                            $class = "class=alertyellow";
                        } else {
                            $class = "class=" . (($key % 2 == 0) ? 'odd' : 'even') . "";
                        }
                    } else {
                        $class = "class=" . (($key % 2 == 0) ? 'odd' : 'even') . "";
                    }
                    $sisa = 0;
                    if ($_GET['sort'] === 'History') {
                        if ($data->transaksi_jenis !== 'Stok Opname') {
                            $awalnya = $this->db->query("select (sum(masuk)-sum(keluar)) as awal from transaksi_detail where transaksi_jenis != 'Pemesanan' and waktu < '".$data->waktu."' and barang_packing_id = '".$data->barang_packing_id."' and ed = '".$data->ed."' and unit_id = '".$_GET['unit']."'")->row(); // ngarah gampang
                            $sisanya = $this->db->query("select (sum(masuk)-sum(keluar)) as sisa from transaksi_detail where transaksi_jenis != 'Pemesanan' and waktu <= '".$data->waktu."' and barang_packing_id = '".$data->barang_packing_id."' and ed = '".$data->ed."' and unit_id = '".$_GET['unit']."'")->row();  // ngarah gampang
                            $awal = isset($awalnya->awal)?$awalnya->awal:'0';
                            $sisa = isset($sisanya->sisa)?$sisanya->sisa:'0';
                        } else {
                            //$awalnya = $this->db->query("select (sum(masuk)-sum(keluar)) as awal from transaksi_detail where transaksi_jenis != 'Pemesanan' and waktu < '".$data->waktu."' and barang_packing_id = '".$data->barang_packing_id."' and ed = '".$data->ed."' and unit_id = '".$_GET['unit']."'")->row(); // ngarah gampang
                            $sisanya = $this->db->query("select masuk as sisa from transaksi_detail where transaksi_jenis = 'Stok Opname' and barang_packing_id = '".$data->barang_packing_id."' and ed = '".$data->ed."' and unit_id = '".$_GET['unit']."' order by waktu desc limit 1")->row();  // ngarah gampang
                            $awal = 0;
                            $sisa = $sisanya->sisa;
                        }
                    } else {
                        $awal = 0;
                        $sisa = $data->sisa;
                    }
                    $ss = isset($sisanya->sisa)?$sisanya->sisa:0;
                    $omsetDlmHpp = $omsetDlmHpp + ($ss*$data->hpp);
                    ?>
                    <tr <?= $class ?>>
                        <?php if(isset($_GET['sort']) and $_GET['sort'] === 'History'): ?>
                        <td align="center" style="white-space: nowrap;"><?= (($no !== $data->transaksi_id) or ($jns !== $data->transaksi_jenis))?datetime($data->waktu):NULL ?></td>
                        <?php endif; ?>

                        <?php if (isset($_GET['sort']) and $_GET['sort'] === 'History') { ?>
                        <td align="center"><?= (($no !== $data->transaksi_id) or ($jns !== $data->transaksi_jenis))?'<a class="view_transaction" title="Klik untuk melihat detail transaksi '.$data->transaksi_jenis.'" id="Detail '.$data->transaksi_jenis .'" href="'.base_url($link . '/' . $data->transaksi_id).'">'.$data->transaksi_id.'</a>':'' ?></td>
                        <td style="white-space: nowrap;"><?= (($no !== $data->transaksi_id) or ($jns !== $data->transaksi_jenis))?$jenis:NULL ?></td>
                        <?php } ?>
                        <td style="white-space: nowrap;"><?= $data->barang ?> <?= ($data->kekuatan != '1') ? $data->kekuatan : null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik') ? '' : $data->pabrik) ?> <?= ($data->satuan_terbesar === $data->satuan_terkecil)?$data->satuan_terkecil:$data->satuan_terbesar.' @ '.(($data->isi == 1) ? '' : $data->isi).' '.$data->satuan_terkecil ?></td>
                        <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                        <td align="right"><?= rupiah($harga_jual) ?></td>
                        <?php } ?>
                        <?php if (isset($_GET['sort']) and $_GET['sort'] === 'History') { ?>
                        <td align="center"><?= datefmysql($data->ed) ?></td>
                        <td align="center"><?= $awal ?></td>
                        <td align="center"><?= $data->masuk ?></td>
                        <td align="center"><?= $data->keluar ?></td>
                        <?php } ?>
                        <?php if (isset($_GET['sort']) and ($_GET['sort'] === 'ED' or $_GET['sort'] === 'Death Stock')) { ?>
                        <td align="center"><?= datefmysql($data->ed) ?></td>
                        <?php } ?>
                        <td align="center"><?= $sisa ?></td>
                        <td align="right"><?= inttocur($data->hpp) ?></td>
                        <td align="right"><?= inttocur($data->hna) ?></td>
                        <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                        <td align="right"><?= rupiah($data->keluar*$harga_jual) ?></td>
                        <?php } ?>
                        <?php if (isset($_GET['sort']) and $_GET['sort'] === 'Terakhir') { ?>
                        <td align="right"><?= rupiah($sisa*$data->hpp) ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $hpp = $hpp+$data->hpp;
                    //$sisa = $sisa+$data->sisa;
                    $asset = $asset+($data->hpp*$sisa);
                    $omset = $omset+($data->keluar*$harga_jual);
                    
                    $no = $data->transaksi_id;
                    $jns= $data->transaksi_jenis;
                }
                if ($_GET['sort'] === 'Terakhir') { ?>
                    <tr>
                        <td colspan="4" align="right"><b>TOTAL ASET</b></td>
                        <td align="right"><b style="font-size: 14px;"><?= rupiah($asset) ?></b></td>
                    </tr>
                <?php }
            } else {
                for ($i = 1; $i <= 2; $i++) {
                    ?>
                    <tr class="<?= ($i % 2 == 1) ? 'odd' : 'even' ?>">
                        <td align="center">&nbsp;</td>
                        <td align="center"></td>
                        <td align="center"></td>
<!--                        <td align="center"></td>
                        <td align="center"></td>-->
                        <td align="center"></td>
                    </tr>
    <?php }
}
?>
        </tbody>
        </table>
    <br/>
    <?php
    if ((isset($_GET['sort']) and $_GET['sort'] == 'History') and count($list_data) > 0) { 
        if ($_GET['transaksi_jenis'] == 'Penjualan') { ?>
        <b>TOR: <?= (($hpp/count($list_data))/($omsetDlmHpp/count($list_data))) ?></b><br/>
        <?php 
        //echo $hpp." / ".count($list_data)." - ".$omsetDlmHpp." / ".count($list_data);
        } ?>
    <?php } ?>
    <?php if (isset($_GET['sort']) and $_GET['sort'] == 'Terakhir') { ?>
    <!--<b>Nilai Asset: <?= rupiah($asset) ?></b>-->
    <?php } ?>
        <a href="#top" title="Back to top">Kembali ke atas</a>
    </div>
</div>