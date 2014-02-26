<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <?php $this->load->view('message') ?>
    <script type="text/javascript">
        function get_last_tagihan() {
            var jml = $('.dinamis_asuransi').length-1;
            var sum_re = 0;
            for (i = 0; i <= jml; i++) {
                var sum_re = sum_re + parseInt(currencyToNumber($('#rupiah'+i).html()));
            }
            $('#sum_re').html(numberToCurrency(sum_re));
            $('#sumre').val(sum_re);
            var sisa = parseInt(currencyToNumber($('#sisa_tagihan'+jml).html()));
            if (jml < 0) {
                $('#bayar').val(numberToCurrency(sisa));
            } else {
                $('#label_bayar').html('Bayar Sendiri(Rp.):');
                $('#bayar').val(numberToCeurrency(sisa));
            }
        }
        function hitung_pembulatan() {
            var jumlah = $('.dinamis_asuransi').length-1;
            var total  = pembulatan_seratus(currencyToNumber($('#total-pembayaran').html()));
            var asuransi = 0;
            if (jumlah >= 0) {
                for (i = 0; i <= jumlah; i++) {
                    var percent = $('#percent'+i).html();
                    var rupiah  = currencyToNumber($('#rupiah'+i).html());
                    if (percent !== '0') {
                        var asuransi = asuransi+((percent/100)*total);
                    }
                    if (rupiah !== '0') {
                        var asuransi = asuransi+rupiah;
                    }
                    //$('#nominal_reimburse'+i).val(asuransi);
                }
            }
            var bulat_sisa = total-asuransi;
            if (bulat_sisa < 0) {
                var hasil = 0;
            } else {
                var hasil = Math.ceil(bulat_sisa);
            }
            $('#adding').html('');
            if (asuransi > 0) {
                $('#adding').html('<tr><td>Sisa Total (Rp.):</td><td><span class=label>'+numberToCurrency(hasil)+'</span>');
            }
            $('#bayar').val(numberToCurrency(hasil));

            $('#bulat').html(numberToCurrency(pembulatan_seratus(hasil)));

        }
        function eliminate_asuransi(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
            var jumlah = $('.dinamis_asuransi').length-1;
            
            if (jumlah+1 === 0) {
                $('#asuransi').html('');
            }
            for (i = 0; i <= jumlah; i++) {
                var deletion = '<span class="link_button" onclick=eliminate_asuransi(this)>Hapus</span>';
                if (i === jumlah) {
                    $('.dinamis_asuransi:eq('+i+')').children('td:eq(8)').html(deletion);
                }
            }
            get_last_tagihan();
            hitung_pembulatan();
            hitung_kembalian();
        }
        
        function get_data_asuransi(no_daftar) {
            var head = '<tr>'+
                        '<th width=10%>Tagihan</th>'+
                        '<th width=10%>Pembulatan</th>'+
                        '<th width=20%>Produk Asuransi</th>'+
                        '<th width=20%>No. Polish</th>'+
                        '<th width=5%>RE (%)</th>'+
                        '<th width=10%>RE (Rp.)</th>'+
                        '<th width=10%>Sisa Tagihan</th>'+
                        '<th width=5%>#</th>'+
                    '</tr>';
            $('#asuransi').html(head);
            var total = $('input[name=totallica]').val();
            var jumlah= 0;
            $.getJSON('<?= base_url('billing/load_data_asuransi_by_nodaftar') ?>/'+no_daftar, function(data){
                $.each(data, function (i, val) {
                    var total_running = '';
                    if (i === 0) {
                        total_running = parseInt(total);
                    }
                    if (i > 0) {
                        total_running = parseInt(currencyToNumber($('#sisa_tagihan'+(i-1)).html()));
                    }
                    var jml = $('.dinamis_asuransi').length;
                    var deletion = '';
                    if (i === jml) {
                        var deletion = '<span class="link_button" onclick=eliminate_asuransi(this)>Hapus</span>';
                    } 

                    if (i > 0) {
                        for (j = 0; j <= (i-1); j++) {
                            $('.dinamis_asuransi:eq('+j+')').children('td:eq(7)').html('');
                        }
                    } else {
                        $('.dinamis_asuransi:eq('+i+')').children('td:eq(7)').html(deletion);
                    }
                    var str = 
                        '<tr valign=top class=dinamis_asuransi>'+
                        '<td id=total_tagihan'+i+' align=right>'+numberToCurrency(total_running)+'</td>'+
                        '<td id=pembulatan_total'+i+' align=right>'+numberToCurrency(pembulatan_seratus(total_running))+'</td>'+
                        '<td><input type=hidden name=nominal_reimburse[] id="nominal_reimburse'+i+'"  />'+val.nama+'<input type=hidden class=id_asuransi name=id_asuransi[] value="'+val.id+'" id=id_asuransi'+i+' /></td>'+
                        '<td>'+val.no_polis+'<input type=hidden name=nopolis[] id="nopolis'+i+'" value="'+val.no_polis+'" /></td>'+
                        '<td align=center><span class=percent id=percent'+i+'>'+val.reimbursement+'</span></td>'+
                        '<td align=center><span class=rupiah id=rupiah'+i+'>'+numberToCurrency(val.reimbursement_rupiah)+'</span><input type=hidden name=re[] id=rupiahval'+i+' value="'+val.reimbursement_rupiah+'" /></td>'+
                        '<td id=sisa_tagihan'+i+' align=right></td>'+
                        '<td align=center>'+deletion+'</td>'+
                        '</tr>';
                    $('#asuransi').append(str);
                    var pembulatan = currencyToNumber($('#pembulatan_total'+i).html());
                    var re_pr = val.reimbursement;
                    var re_rp = val.reimbursement_rupiah;

                    var nilai = 0;
                    if (re_pr !== '0') {
                        var nilai = pembulatan*(re_pr/100);
                    }
                    if (re_rp !== '0') {
                        var nilai = re_rp;
                    }
                    $('#rupiah'+i).html(numberToCurrency(parseInt(nilai)));
                    $('#rupiahval'+i).val(parseInt(nilai));
                    var sisa = parseInt(pembulatan - nilai);

                    $('#sisa_tagihan'+i).html(numberToCurrency(sisa));
                    $('#serahuang, #bayar').val(numberToCurrency(sisa));
                    get_last_tagihan();
                    hitung_pembulatan();
                    hitung_kembalian();
                    
                    jumlah++;
                });
                if (jumlah === 0) {
                    $('#asuransi').html('');
                }
            });
        }
        function add_asuransi(i) {
            var no_rm = $('#no_rm').val();
            if (no_rm === '') {
                return false;
            }
            if (i === 0) {
                
                var head = '<tr>'+
                                '<th width=10%>Tagihan</th>'+
                                '<th width=10%>Pembulatan</th>'+
                                '<th width=20%>Produk Asuransi</th>'+
                                '<th width=20%>No. Polish</th>'+
                                '<th width=5%>RE (%)</th>'+
                                '<th width=10%>RE (Rp.)</th>'+
                                '<th width=10%>Sisa Tagihan</th>'+
                                '<th width=5%>#</th>'+
                            '</tr>';
                $('#asuransi').append(head);
                var total = $('input[name=totallica]').val();
            }
            var total_running = '';
            if (i === 0) {
                total_running = parseInt(total);
            }
            if (i > 0) {
                total_running = parseInt(currencyToNumber($('#sisa_tagihan'+(i-1)).html()));
            }
            var jml = $('.dinamis_asuransi').length;
            var deletion = '';
            if (i === jml) {
                var deletion = '<span class="link_button" onclick=eliminate_asuransi(this)>Hapus</span>';
            } 
            
            if (i > 0) {
                for (j = 0; j <= (i-1); j++) {
                    $('.dinamis_asuransi:eq('+j+')').children('td:eq(8)').html('');
                }
            } else {
                $('.dinamis_asuransi:eq('+i+')').children('td:eq(8)').html(deletion);
            }
            var str = 
                '<tr valign=top class=dinamis_asuransi>'+
                '<td id=total_tagihan'+i+' align=right>'+numberToCurrency(total_running)+'</td>'+
                '<td id=pembulatan_total'+i+' align=right>'+numberToCurrency(pembulatan_seratus(total_running))+'</td>'+
                '<td><input type=hidden name=nominal_reimburse[] id="nominal_reimburse'+i+'"  /><input type="text" class="asuransi" id="asuransi'+i+'" placeholder="Pilih Produk Asuransi" /><input type=hidden class=id_asuransi name=id_asuransi[] id=id_asuransi'+i+' /></td>'+
                '<td><input type=text name=nopolis[] id="nopolis'+i+'" /></td>'+
                '<td align=center><span class=percent id=percent'+i+'></span></td>'+
                '<td align=center><span class=rupiah id=rupiah'+i+'></span><input type=hidden name=re[] id=rupiahval'+i+' /></td>'+
                '<td id=sisa_tagihan'+i+' align=right></td>'+
                '<td align=center>'+deletion+'</td>'+
                '</tr>';
            $('#asuransi').append(str);
            $('#asuransi'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_produk_asuransi') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].id // nama field yang dicari
                        };
                    }
                    return parsed;

                },
                formatItem: function(data,i,max){
                    if (data.id !== null) {
                        var str = '<div class=result>'+data.nama+'</div>';
                    }
                    return str;
                },
                width: 200, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('#id_asuransi'+i).val(data.id);
                $('#percent'+i).html(data.reimbursement);
                $('#rupiah'+i).html(numberToCurrency(data.reimbursement_rupiah));
                $('#id_ap'+i).html(data.id);
                $('#instansi_ap'+i).html(data.instansi);

                

                $('#nominal_reimburse'+i).val(asuransi*1);
                var pembulatan = currencyToNumber($('#pembulatan_total'+i).html());
                var re_pr = data.reimbursement;
                var re_rp = data.reimbursement_rupiah;
                
                var nilai = 0;
                if (re_pr !== '0') {
                    var nilai = pembulatan*(re_pr/100);
                }
                if (re_rp !== '0') {
                    var nilai = re_rp;
                }
                $('#rupiah'+i).html(numberToCurrency(parseInt(nilai)));
                $('#rupiahval'+i).val(parseInt(nilai));
                var sisa = parseInt(pembulatan - nilai);
                
                $('#sisa_tagihan'+i).html(numberToCurrency(sisa));
                get_last_tagihan();
                hitung_pembulatan();
                hitung_kembalian();
            });
        }
        function load_detail_bayar(id_kunjungan) {
            $.ajax({
                url: '<?= base_url("billing/load_data_pembayaran") ?>/'+id_kunjungan,
                cache: false,
                success: function(data) {
                    $('#result-pembayaran').html(data);
                }
            });
        }

        function update_pembayaran(no_daftar) {
            $.ajax({
                url: '<?= base_url("billing/get_detail_data_pasien") ?>/'+no_daftar,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    fill_field(data);
                }
            })
        }
        $(function() {
            $('#no_rm').focus();
            $('#serahuang').focus(function() {
                //hitung_pembulatan();
            });
            $('#serahuang').keyup(function() {
                //hitung_pembulatan();
            }); 
            $('#addasuransi').click(function() {
                if ($('#no_rm').val() === '') {
                    custom_message('Peringatan','Nomor rekam medik pasien belum dipilih !','#no_rm');
                    return false;
                }
                var jumlah = $('.dinamis_asuransi').length;
                if (jumlah > 0) {
                    if ($('#id_asuransi'+(jumlah-1)) === '0') {
                        custom_message('Peringatan','Pilih terlebih dahulu produk asuransi !');
                        return false;
                    } else {
                        add_asuransi(jumlah);
                    }
                } else {
                    add_asuransi(jumlah);
                }
            });
            var jumlah = $('.tr_rows').length-1;
            //for (i = 0; i <= jumlah; i++) {
            $('button[id=resetan], #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#click').each(function(){$(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('#click').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#cari_pdd').button({icons: {secondary: 'ui-icon-search'}});
        
        
            $('.print').click(function() {
                var id_nota = $(this).attr('title');
                var pembayaran_ke = $(this).attr('name');
                window.open('<?= base_url('billing/cetak') ?>/'+id_nota+'/'+pembayaran_ke, 'cetakbilling', 'location=1,status=1,scrollbars=1,width=820px,height=500px');
            });
            //}
            $('#resetan').click(function() {
                $('#kunjungan').load('<?= base_url('billing/pembayaran_total_kunjungan') ?>');
                //reset_all();
            });
            $('#cetak_kartu').click(function() {
                var id_kunjungan = $('#id_kunjungan').val();
                window.open('<?= base_url('billing/cetak') ?>/'+id_kunjungan, 'cetakbilling', 'location=1,status=1,scrollbars=1,width=820px,height=500px');
            });
            $('#id_kunjungan').autocomplete("<?= base_url('billing/get_data_kunjungan/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    $('#no_rm, #nama_pasien').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_daftar+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.no_daftar);
                fill_field(data);
            });
        
            $('#no_rm').autocomplete("<?= base_url('billing/get_data_pasien/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    $('#id_kunjungan, #nama_pasien, input[name=sum_re]').val('');
                    $('#sum_re').html('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar !== null) {
                        var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength: 0
            }).result(
            function(event,data,formated){
                $(this).val(data.no_rm);
                fill_field(data);
                $('#serahuang').focus();
            });
        
            $('#nama_pasien').autocomplete("<?= base_url('billing/get_data_pasien/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    $('#id_kunjungan, #no_rm').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                fill_field(data);
                $('#serahuang').focus();
            });
        
        
            $('#bulat, #serahuang').keyup(function() {
                hitung_kembalian();
            })
        
        
            $('#formbayar').submit(function(){
                var url = '<?= base_url() ?>billing/cek_pembayaran/';
                var id_kunjungan = $('#id_kunjungan').val();

                if ($('#serahuang').val() == '') {
                    custom_message('Peringatan', 'Kolom uang diserahkan harus diisi !', '#serahuang');
                    return false;
                };

                $.ajax({
                    type: 'POST',
                    url: url+id_kunjungan,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status != true) {
                            bayar(id_kunjungan);
                        }else{
                            custom_message('Pemberitahuan', 'Billing sudah lunas');
                        }
                    }
                });
                
                return false;
            });
        
        });

        function bayar(id_kunjungan){
            var url = $('#formbayar').attr('action');
            $.ajax({
                    type: 'POST',
                    data: $('#formbayar').serialize(),
                    url: url,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            load_detail_bayar(id_kunjungan);
                            $('#')
                            alert_tambah();
                        }
                    }
                });
        }
    
        function reset_all(){
            $('#no_rm, #nama_pasien, #id_kunjungan,#bulat,#serahuang, #bayar').val('');   
            $('#kembalian, #total-pembayaran, #produk, #result-pembayaran').html('');
        }
        function hitung_kembalian() {
            
            var diserahkan = currencyToNumber($('#serahuang').val());
            var total      = $('input[name=totallica]').val();
            var bayar      = currencyToNumber($('#bayar').val());
            var kembalian  = diserahkan - bayar;
            var kekurangan = bayar - total;
            
            if (kekurangan < 0) {
                //$('#adding').html('<tr><td>Sisa tagihan:</td><td><span class=label>'+numberToCurrency(kekurangan)+'</span>');
                
            } else {
                $('#adding').html('');
            }
            if (!isNaN(kembalian)) {
                if (kembalian < 0) {
                    $('#kembalian').html(kembalian);
                    //$('#keterangan').html('Sisa pembayaran:');
                } else {
                    $('#kembalian').html(numberToCurrency(kembalian));
                    //$('#keterangan').html('Kembalian:');
                }
            }
            var baris = $('.tr_rows').length;
            var total_terbayar = 0;
            if (baris >= 1) {
                for (i = 1; i <= baris; i++) {
                    var terbayar = parseInt(currencyToNumber($('#bayar'+i).html()));
                    total_terbayar = total_terbayar + terbayar;
                }
            }
            var jml = $('.dinamis_asuransi').length-1;
            var sisa = parseInt(currencyToNumber($('#sisa_tagihan'+jml).html()));
            
            var sisa_tagihan = sisa-(diserahkan+total_terbayar);
            
            if (isNaN(sisa_tagihan) || sisa_tagihan <= 0) {
                $('#sisa_tagihan').html('0');
            } else {
                $('#sisa_tagihan').html(numberToCurrency(sisa_tagihan));
            }
            
        }
        function pembulatan_seratus(angka) {
            var kelipatan = 100;
            var sisa = angka % kelipatan;
            if (sisa != 0) {
                var kekurangan = kelipatan - sisa;
                var hasilBulat = angka + kekurangan;
                return Math.ceil(hasilBulat);
            } else {
                return Math.ceil(angka);
            }
        }
        function fill_field(data){
            $('input[name=no_daftar]').val(data.id);
            $('#nama_pasien').val(data.nama);  
            $('#no_rm').val(data.no_rm);  
            $('input[name=kunjungan_billing_id]').val(data.no_daftar);
            $('#id_kunjungan').val(data.no_daftar);


            var id = data.id_pasien;
           
            var no_daftar = data.no_daftar;
            $.ajax({
                url: '<?= base_url("billing/total_tagihan") ?>/'+no_daftar,
                data: '',
                cache: false,
                dataType: 'json',
                success: function(msg) {
                    if (msg.fuck <= 0) {
                        var fuck = '0';
                    } else {
                        var fuck = msg.fuck;
                    }
                    $('#total-pembayaran').html(numberToCurrency(pembulatan_seratus(fuck)));
                    $('#bayar').val(numberToCurrency(pembulatan_seratus(fuck)));
                    $('#total_biaya').html(numberToCurrency(pembulatan_seratus(fuck)));
                    $('#bulat').html(numberToCurrency(pembulatan_seratus(fuck)));
                    $('input[name=totallica]').val(pembulatan_seratus(fuck));
                    if (msg.you !== null || msg.you !== 0) {
                        var sisa = msg.fuck - msg.you;
                        if (sisa <= 0) {
                            $('#sisa_tagihan').html('0');
                        }
                    }
                    get_data_asuransi(data.no_daftar);
                }
            });
            var id_kunjungan = data.no_daftar;
            load_detail_bayar(id_kunjungan);
        }
    </script>
    

        <?= form_open('billing/pembayaran_save', 'id=formbayar') ?>
            <table width="100%" class="inputan">
                <tr><td>Tanggal:</td><td><?= indo_tgl(date("Y-m-d")) ?></td></tr>
                <tr><td>No. RM:</td><td><?= form_input('norm', isset($pasien->no_rm) ? $pasien->no_rm : null, 'id=no_rm size=40') ?></td></tr>
                <tr><td>Nama Pasien:</td><td><?= form_input('nama_pasien', isset($pasien->nama) ? $pasien->nama : null, 'id=nama_pasien size=40') ?></td></tr>
                <tr><td>Nomor Kunjungan:</td><td><?= form_input('id_kunjungan', isset($pasien->no_daftar) ? $pasien->no_daftar : null, 'id=id_kunjungan size=40') ?>
                <?= form_hidden('kunjungan_billing_id', isset($pasien->no_daftar) ? $pasien->no_daftar : null) ?></td></tr>
                <tr><td></td><td>
                        <table width="100%" id="asuransi" class="inputan"></table>
                        <span id="addasuransi" class="link_button" style="margin-top: 5px; cursor: pointer;"><u>Tambah Asuransi</u></span>
                </td></tr>
                <tr><td>Total Bayar Asuransi:</td><td><span class="label" id="sum_re"></span><input type="hidden" name="sumre" id="sumre" /></td></tr>
                <tr><td>Total Biaya:</td><td id="total_biaya"><?= isset($sisa)?inttocur(pembulatan_seratus($sisa)):null ?></td></tr>
                <tr><td id="label_bayar">Bayar (Rp.):</td><td><?= form_input('bayar', isset($sisa)?inttocur(pembulatan_seratus($sisa)):null, 'id=bayar onblur=FormNum(this) size=40') ?></td></tr>
                <tr><td>Tunai (Rp.):</td><td><?= form_input('serahuang', NULL, 'id=serahuang onblur=FormNum(this) size=40') ?></td></tr>
                <tr><td id="keterangan">Kembalian (Rp.):</td><td id="kembalian"></td></tr>
                <tr><td>Sisa Tagihan:</td><td><span class="label" id="sisa_tagihan"></span></td></tr>

                <tr><td></td><td><?= form_hidden('totallica', isset($sisa)?pembulatan_seratus($sisa):null) ?> 
                <?= isset($attribute) ? '' : form_submit('data', 'Simpan', 'id=click') ?> 
                <?= form_button(null, 'Reset', 'id=resetan') ?></td></tr>
            </table>
        <?= form_close() ?>

         <?php if (isset($pasien->no_daftar)): ?>
            <script type="text/javascript">
                get_data_asuransi('<?= $pasien->no_daftar ?>');
                load_detail_bayar('<?= $pasien->no_daftar ?>');
            </script>
        <?php endif; ?>
    
    <div id="result-pembayaran">
                <div class="data-list">

                    <table class="list-data" width="100%">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Pembulatan Bayar</th>
                            <th>Sisa</th>
                            <th>Cetak</th>
                        </tr>
                            <tr>
                                <td align="center">&nbsp</td>
                                <td align="center"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="center"></td>
                            </tr>
                            <tr>
                                <td align="center">&nbsp</td>
                                <td align="center"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="right"></td>
                                <td align="center"></td>
                            </tr>
                            
                    </table>
                </div>
        </div>
</div>

 <div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
            <?= form_open('','id=formcari')?>
            <fieldset>
                <tr><td>Nama Penduduk</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class=input-text') ?>
                <tr><td>Alamat</td><td><?= form_textarea('alamat','','id=alamat_cari class=standar')?>
                <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?>
            <?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian()') ?>
            </table>
            <?= form_close() ?>

            <div id="list_penduduk"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#formcari').submit(function(){
                cari_penduduk(1);
                return false;
            });

            $('#bt_cari').click(function(){
                $('#form_cari').dialog('open');
            });

            $('#form_cari').dialog({
                autoOpen: false,
                height: 550,
                width: 700,
                title : 'Pencarian Penduduk',
                modal: true,
                resizable : false,
                close : function(){
                    reset_pencarian();
                },
                open : function(){
                    cari_penduduk(1);
                }
            });
        });

        function cari_penduduk(page){
            $.ajax({
                url: '<?= base_url("pelayanan/search_pendaftaran_penduduk") ?>/'+page,
                cache: false,
                data : $('#formcari').serialize(),
                success: function(data) {
                   $('#list_penduduk').html(data);
                }
            });
        }

        function reset_pencarian(){
            $('#nama_cari, #alamat_cari').val('');
            $('#list_penduduk').html('');
        }

    

        function pilih_penduduk(id, id_daftar){
            $('#id_kunjungan').val(id_daftar);
            var data = {'id':id, 'no_daftar':id_daftar};
            $.ajax({
                url: '<?= base_url("demografi/get_penduduk") ?>/'+id,
                cache: false,
                dataType :'json',
                success: function(data) {
                    $('#nama_pasien').val(data.nama);
                }
            });
            fill_field(data);
            $('#form_cari').dialog('close');
                
        }

        function get_kelurahan(kel_id){
            if(kel_id != ''){
                $.ajax({
                    url: '<?= base_url("demografi/detail_kelurahan") ?>/'+kel_id,
                    cache: false,
                    dataType :'json',
                    success: function(data) {
                        $('#wilayah').html(data.nama);
                    }
                });
            }
        }

        function paging(page, tab, cari){
            cari_penduduk(page);
        }

    </script>
<?php die; ?>