
    <script type="text/javascript">
        $(function(){
            $("#example-advanced").treetable({ expandable: true });
            $('#add_jenis').button({
                icons: {
                    secondary: 'ui-icon-circle-plus'
                }
            });
            $('#add_jenis').click(function() {
                dialog_jenis("","");
                return false;
            });

        });

        function dialog_jenis(id, nama){
            var str = '<div class=data-input id=dialogx><form action="" id=form_jenis>'+
                '<div class="msg" id="msg_jenis"></div>'+
                '<tr><td>Id:</td><td><?= form_input('id_jenis',get_last_id('jenis_layanan', 'id'),'id=id_jenis onKeyup=Angka(this)') ?>'+
                '<input type=hidden name="hd_id_jenis" id="hd_id_jenis" />'+
                '<tr><td>Nama:</td><td><?= form_input('nama_jenis', NULL, 'id=nama_jenis size=40') ?>'+
                '</form></div>';
            $('#dialog_form').append(str);
            if (id != "") {
                $('#id_jenis').val(id);
                $('input[name=hd_id_jenis]').val(id);
                var judul = "Edit Jenis Layanan";
            }else{
                var judul = "Tambah Jenis Layanan";
            }

            if (nama != "") {
                $('#nama_jenis').val(nama);
            };

            $('#dialogx').dialog({
                autoOpen: true,
                title : judul,
                height: 160,
                width: 480,
                modal: true,
                close: function() {
                    $(this).dialog().remove(); 
                },
                buttons: {
                    "Simpan": function() { 
                        save_jenis();
                    },
                    "Batal": function() { 
                        $(this).dialog().remove(); 
                    }
                } 
            });
            $('#nama_jenis').focus();

        }

        function save_jenis() {
            if ($('#id_jenis').val() == '') {
                $("#msg_jenis").fadeIn("fast").html('Id jenis layanan tidak boleh kosong !');
                $('#id_jenis').focus();
                return false;
            }
            if ($('#nama_jenis').val() == '') {
                $("#msg_jenis").fadeIn("fast").html('Nama jenis layanan tidak boleh kosong !');
                $('#nama_jenis').focus();
                return false;
            }
            if ($('#hd_id_jenis').val() == '') {
                var url = "<?= base_url('pelayanan/manage_jenis_layanan') ?>/add/";
            } else {
                var url = "<?= base_url('pelayanan/manage_jenis_layanan') ?>/edit/";
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: $('#form_jenis').serialize(),
                success: function(data) {
                    $('#dialogx').dialog().remove(); 
                    if ($('input[name=hd_id_jenis]').val() == '') {
                        alert_tambah();
                    } else {
                        alert_edit();
                    }
                    $('#list_jenis').html(data);
                }
            });
        }


        function dialog_sub_jenis(id_jenis, id_sub_jenis, nama){
            var str = '<div class="data-input data-input-dialog" id=dialogx><form action="" id=form_sub_jenis>'+
                '<div class="msg" id="msg_sub_jenis"></div>'+
                '<tr><td>Id:</td><td><?= form_input('id_sub_jenis',get_last_id('sub_jenis_layanan', 'id'),'id=id_sub_jenis onKeyup=Angka(this)') ?>'+
                '<tr><td>Jenis Layanan:</td><td><select name="jenis_id" id="jenis_id"><option value="">Pilih Jenis Layanan ...</option></select>'+
                '<input type=hidden name="hd_id_sub_jenis" id="hd_id_sub_jenis" />'+
                '<tr><td>Nama:</td><td><?= form_input('nama_sub_jenis', NULL, 'id=nama_sub_jenis size=40') ?>'+
                '</form></div>';
            $('#dialog_form').append(str);
            $('#jenis_id').val(id_jenis);
            load_data_jenis_layanan(id_jenis);

            if (id_sub_jenis != "") {
                $('#id_sub_jenis').val(id_sub_jenis);
                $('input[name=hd_id_sub_jenis]').val(id_sub_jenis);
                var judul = "Edit Sub Jenis Layanan";
            }else{
                var judul = "Tambah Sub Jenis Layanan";
            }

            if (nama != "") {
                $('#nama_sub_jenis').val(nama);
            }
           
            $('#dialogx').dialog({
                autoOpen: true,
                title : judul,
                height: 180,
                width: 480,
                modal: true,
                close: function() {
                    $(this).dialog().remove(); 
                },
                buttons: {
                    "Simpan": function() { 
                        save_sub_jenis(id_jenis, id_sub_jenis);
                    },
                    "Batal": function() { 
                        $(this).dialog().remove(); 
                    }
                } 
            });
             $('#nama_sub_jenis').focus();
        }

        function save_sub_jenis(id_jenis, id_sub_jenis) {
            if ($('#id_sub_jenis').val() == '') {
                $("#msg_sub_jenis").fadeIn("fast").html('Id sub jenis layanan tidak boleh kosong !');
                $('#id_jenis').focus();
                return false;
            }

            if ($('#jenis_id').val() == '') {
                $("#msg_sub_jenis").fadeIn("fast").html('Nama jenis layanan tidak boleh kosong !');
                $('#jenis_id').focus();
                return false;
            }

            if ($('#nama_sub_jenis').val() == '') {
                $("#msg_sub_jenis").fadeIn("fast").html('Nama sub jenis layanan tidak boleh kosong !');
                $('#nama_sub_jenis').focus();
                return false;
            }
            if ($('input[name=hd_id_sub_jenis]').val() == '') {
                var url = "<?= base_url('pelayanan/manage_jenis_layanan') ?>/add_sub";
            } else {
                var url = "<?= base_url('pelayanan/manage_jenis_layanan') ?>/edit_sub/";
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: $('#form_sub_jenis').serialize(),
                success: function(data) {
                    $('#dialogx').dialog().remove(); 
                    if ($('input[name=hd_id_sub_jenis]').val() == '') {
                        alert_tambah();
                    } else {
                        alert_edit();
                    }
                    load_data_jenis_layanan(id_jenis);
                    load_data_sub_jenis_layanan(id_sub_jenis);
                    $('#list_jenis').html(data);
                    $("#example-advanced").treetable('expandAll');
                }
            });
        }

        function delete_data(url, id){
            $('<div></div>').html("Anda yakin akan menghapus data ini ?")
                .dialog({
                autoOpen: true,
                title :'Hapus Data',
                modal: true,
                close: function() {
                    $(this).dialog().remove(); 
                },
                buttons: {
                    "OK": function() { 
                        $(this).dialog().remove(); 
                        $.ajax({
                            url: url+id,
                            cache: false,
                            success: function(data) {
                                alert_delete();
                                $('#list_jenis').html(data);                               
                            },
                            error : function(){
                                  alert_delete_failed();
                            }
                        });
                    },
                    "Batal": function() { 
                        $(this).dialog().remove(); 
                    }
                } 
            });
        }
        
        function load_data_jenis_layanan(id_jenis, id_sub) {
            $.ajax({
                url: '<?= base_url('pelayanan/load_data_jenis_layanan2/') ?>',
                cache: false,
                success: function(item) {
                    $('#s_jenis_id').html('');
                    $('#s_jenis_id').append(item).val(id_jenis);
                    $('#jenis_id').html('');
                    $('#jenis_id').append(item).val(id_jenis);
                }
            });
        }

        function load_data_sub_jenis_layanan(id_sub) {
            $.ajax({
                url: '<?= base_url('pelayanan/load_data_sub_jenis_layanan2/') ?>',
                cache: false,
                success: function(item) {
                    $('#s_subjenis_id').html('');
                    $('#s_subjenis_id').append(item).val(id_sub);
                }
            });
        }

        function dialog_sub_sub_jenis(id_jenis, id_sub, id_sub_sub, nama){
            $('#simpan').removeAttr('disabled');
            $('#id_sub_sub_jenis').val('<?= get_last_id("sub_sub_jenis_layanan", "id") ?>');
            $('#nama_jenis_layanan').html('<select name="jenis_id" id="s_jenis_id" readonly><option value="">Pilih Jenis Layanan ...</option></select>');
            $('#nama_sub_jenis_layanan').html('<select name="sub_jenis_id" id="s_subjenis_id" readonly><option value="">Pilih Jenis Sub Layanan ...</option></select>');
            load_data_jenis_layanan(id_jenis);
            load_data_sub_jenis_layanan(id_sub);
            
            if (id_sub_sub != "") {
                $('#id_sub_sub_jenis').val(id_sub_sub);
                $('input[name=hd_id_sub_sub_jenis]').val(id_sub_sub);
            }else{
               $('#nama_sub_sub_jenis').val(''); 
            }

            if (nama != "") {
                $('#nama_sub_sub_jenis').val(nama);
            }
            
            $('#nama_sub_sub_jenis').focus();
        }

       
        
    </script>

<div id="dialog_form"></div>
<div class="data-list">
    <?= form_button(null, 'Tambah Jenis Layanan', 'id=add_jenis') ?>
    <br/>
    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table class="tabel-advance list-data" width="100%" id="example-advanced">
        <thead>
            <tr>
                <th width="15%">Kode</th>
                <th width="40%">Nama</th>
                <th width="20%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php  foreach ($list_data as $r1 => $data) { ?>
        <tr data-tt-id='<?= $r1 ?>' class="even">
            <td><span onclick="dialog_jenis('<?= $data->id ?>','<?= $data->nama ?>')"><?= $data->id ?></span></td>
            <td><?= $data->nama ?></td>            
            <td>
                <span class="link_button" onclick="delete_data('<?= base_url() ?>pelayanan/delete_jenis_layanan/','<?= $data->id?>')">Hapus</span>
                <span class="link_button" onclick="dialog_sub_jenis('<?= $data->id ?>','','')">Tambah Sub</span>
            </td>
        </tr>
        <?php 
            $sub_jenis = $this->m_pelayanan->data_subjenis_layanan_load_data(isset($id_sub)?$id_sub:NULL, $data->id)->result();
            foreach ($sub_jenis as $r2 => $rows) { ?>
                <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>' data-tt-parent-id='<?= $r1 ?>' class="even">
                    <td><span onclick="dialog_sub_jenis('<?= $data->id ?>','<?= $rows->id ?>','<?= $rows->nama ?>');"><?= $data->id.'.'.$rows->id ?></span></td>
                    <td><?= $rows->nama ?></td>
                    <td><span class="link_button" onclick="delete_data('<?= base_url() ?>pelayanan/delete_sub_jenis_layanan/','<?= $rows->id?>')">Hapus</span>
                       <span class="link_button" onclick="dialog_sub_sub_jenis('<?= $data->id ?>', '<?= $rows->id?>','','')">Tambah Sub Sub</span>
                   </td>
                </tr>
                   
                 <?php    $sub_sub_jenis = $this->m_pelayanan->data_subsubjenis_layanan_load_data(isset($id_sub_sub)?$id_sub_sub:NULL, $rows->id)->result();
                         foreach ($sub_sub_jenis as $r3 => $rowss) { ?>
                            <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>' class="even">
                                <td><span onclick="dialog_sub_sub_jenis('<?= $data->id ?>', '<?= $rows->id?>','<?= $rowss->id ?>','<?= $rowss->nama ?>')"><?= $data->id.'.'.$rows->id .'.'.$rowss->id ?></span></td>
                                <td><?= $rowss->nama ?></td>
                                <td>
                                    <span class="deletion" onclick="delete_data('<?= base_url() ?>pelayanan/delete_sub_sub_jenis_layanan/','<?= $rowss->id?>')">Hapus</span>
                                </td>
                            </tr>
                        <?php } ?>         
            <?php } ?>    
        <?php } ?>
        </tbody>
    </table>
    <?= $paging ?><br/><br/>
</div>