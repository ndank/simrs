<div class="kegiatan">
<div class="titling"><h1><?= $title ?></h1></div>
<script type="text/javascript">
    function fill_field(data){
        $("input[name=id_kunjungan]").val(data.no_daftar);
        $("#nama").html(data.nama);
        $("#alamat").val(data.alamat);
        $("#wilayah").html(data.kelurahan);
        $("#wilayah").append(" ");
        $("#wilayah").append(data.kecamatan);
        $("#gender").html((data.gender=="L")?'Laki-laki':'Perempuan');
        $("#umur").html(hitungUmur(data.lahir_tanggal));
        $("#pekerjaan").html(data.pekerjaan);
        $("#pendidikan").html(data.pendidikan);
        $("#waktu_datang").html((data.arrive_time == null)?'':datetimefmysql(data.arrive_time));

    }
    $(function() {
        $('#no').autocomplete("<?= base_url('pelayanan/pasien_load_data') ?>",
        {
              extraParams :{ 
                jenis : function(){
                    return 'Poliklinik';
                }
            },
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].no_rm // nama field yang dicari
                    };
                }
                $('input[name=no_rm]').val('');
                return parsed;

            },
            formatItem: function(data,i,max) {
                if (data.no_daftar != null) {
                    var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                }
                return str;
            },
            max: 100,
            width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.no_rm);
            $('#msg_detail').html('');
            $('input[name=no_rm]').val(data.no_rm);
            data_pasien = data;
            fill_field(data);
        });
    });
</script>
<div class="data-input">
    <table width="100%" class="inputan">Parameter</legend>
        <table width="100%">
            <tr valign="top">
                <td width="50%">
                    <table width="100%" style="line-height: 22px;">
                        <tr><td width="30%">No. RM:</td><td><?= form_input('',NULL,'id=no size=25')?></td></tr>
                        <tr><td>Nama Pasien:</td><td id="nama"></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat"></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah"></td></tr>
                        <tr><td>Gender:</td><td id="gender"></td></tr>
                        <tr><td>Umur:</td><td id="umur"></td></tr>
                        <tr><td>Pekerjaan:</td><td id="pekerjaan"></td></tr>
                        <tr><td>Pendidikan:</td><td id="pendidikan"></td></tr>
                    </table> 
                </td>
                <td width="50%">
                    <table width="100%" style="line-height: 22px;">
                        <tr><td width="30%">Waktu Order:</td><td><?= form_input('waktu', date("d/m/Y H:i"), 'id=waktu size=15') ?></td></tr>
                        <tr><td></td><td></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </table>
</div>
</div>