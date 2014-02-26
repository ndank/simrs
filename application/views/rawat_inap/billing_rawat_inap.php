<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script>       
        
        
        $(function(){
            $('#tabs').tabs();
            $('#no_barcode').focus();
            $('#no_barcode').keydown(function(e){
                if ((e.keyCode == 13) && (IsNumeric($(this).val()))) {
                    $.ajax({
                        url: '<?= base_url("rawatinap/get_data_pasien") ?>/'+$(this).val(),
                        cache: false,
                        dataType:'json',
                        success: function(data) {
                            if(data.length !== 0){
                                $('#no_barcode').val(data.no_rm);
                                get_data_bed(data);
                            }else{
                                custom_message('Peringatan!', 'No. Rekam Medik tidak ada atau pasien belum melakukan pendaftaran kunjungan !');
                                $('#no_rm').val('');
                            }
                           
                        }
                    });
                    return false;
                };

            });


            $('#no_rm').autocomplete("<?= base_url('rawatinap/pasien_load_data/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_daftar // nama field yang dicari
                        };
                    }
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.no_rm+' - '+data.nama+' <br/>'+data.alamat+'</div>';
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                cacheLength: 0,
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){              
                get_data_bed(data);
               
            });
            
        });
        
        
        function get_data_bed(data){
            $('input[name=id_pasien]').val(data.id);
            $('#nama_pasien').html(data.nama); 
            $('#no_daftar').html(data.no_daftar);
            $('#no_rm').val(data.no_rm);
            $('#umur').html(ageByBirth(data.lahir_tanggal)); 
            $('#gender').html((data.gender=='L')?'Laki-laki':((data.gender == 'P')?'Perempuan':'')); 
            $('#alamat').html(data.alamat); 
            $('#wilayah').html(((data.kelurahan!=null)?data.kelurahan:'') +' '+ ((data.kecamatan!=null)?data.kecamatan:'')); 
            $('#pj').html(data.pjawab);            
            $('#pj_alamat').html(data.pj_alamat); 
            $('#pj_wilayah').html(((data.pj_kelurahan==null)?'':data.pj_kelurahan) +' '+ ((data.pj_kecamatan==null)?'':data.pj_kecamatan)); 
            $('#pj_telp').html(data.pj_telp); 
            
            
            get_list_bed(data.no_daftar);

            $.ajax({
                url: '<?= base_url("inv_autocomplete/load_data_pelayanan_kunjungan_by_id_penduduk") ?>/'+data.id_penduduk,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#bangsal').html(data.unit);
                    $('#kelas').html((data.kelas !== null)?data.kelas:'-');
                    $('#nott').html((data.no_tt !== '0')?data.no_tt:'-');
                    $('#asuransi').html(data.asuransi);
                }
            });    
        }

        function get_list_bed(no_daftar){
            $.ajax({
                url: '<?= base_url("rawatinap/get_data_rawatinap") ?>/'+no_daftar,
                data: '',
                cache: false,
                success: function(msg) {
                    $('#result').html(msg);                    
                }
            });            
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Stop Rawat Inap</a></li>
        </ul>
        <div id="tabs-1">
            <table width="100%" class="inputan"><tr valign="top"><td width="50%">
                <?= form_hidden('id_pasien') ?>
                    <table width="100%">
                        <tr><td colspan="2"><b>Data Pasien</b></td></tr>
                        <tr><td width="20%">No. RM (Barcode):</td><td><?= form_input('', '', 'id=no_barcode size=40') ?></td></tr>
                        <tr><td>No. RM</td><td><?= form_input('norm', '', 'id=no_rm size=40') ?>
                        <tr><td>Nama Pasien</td><td id="nama_pasien"></td></tr>
                        <tr><td>ID Kunjungan:</td><td id="no_daftar"></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat"></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah"></td></tr>
                        <tr><td>Umur:</td><td id="umur"></td></tr>
                        <tr><td>Sex:</td><td id="gender"></td></tr>
                    </table>
                </td><td width="50%">
                    <table width="100%">
                        <tr><td colspan="2"><b>Penanggung Jawab</b></td></tr>
                        <tr><td width="20%">Nama:</td><td id="pj"><span class="label"></td></tr>
                        <tr><td>Alamat:</td><td id="pj_alamat">
                        <tr><td>Wilayah:</td><td id="pj_wilayah"></td></tr>
                        <tr><td>No. Telp:</td><td id="pj_telp"></td></tr>
                        <tr><td></td><td>
                        
                    </table>
                    <table width="100%">
                        <tr><td colspan="2"><b>Rencana Pembayaran</b></td></tr>
                        <tr><td width="20%">Asuransi:</td><td id="asuransi"></td></tr>
                    </table>
                </td></tr>
            </table>
    <div id="result"></div>
    </div>
    </div>
</div>


<?php die; ?>