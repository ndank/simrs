<script type="text/javascript">
function load_sub_rekening(id_rekening, id_sub_rekening) {
    $.ajax({
        url: '<?= base_url('akuntansi/get_sub_rekening_dropdown') ?>/'+id_rekening,
        cache: false,
        success: function(data) {
            $('#sub_rekening').append(data).val(id_sub_rekening);
        }
    });
}
function load_sub_sub_rekening(id_sub_rekening, id_sub_sub_rekening) {
    $.ajax({
        url: '<?= base_url('akuntansi/get_sub_sub_rekening_dropdown') ?>/'+id_sub_rekening,
        cache: false,
        success: function(data) {
            $('#sub_sub_rekening').append(data).val(id_sub_sub_rekening);
        }
    });
}
function load_sub_sub_sub_rekening(id_sub_sub_rekening, id_sub_sub_sub_rekening) {
    $.ajax({
        url: '<?= base_url('akuntansi/get_sub_sub_sub_rekening_dropdown') ?>/'+id_sub_sub_rekening,
        cache: false,
        success: function(data) {
            $('#sub_sub_sub_rekening').append(data).val(id_sub_sub_sub_rekening);
        }
    });
}
function load_sub_sub_sub_sub_rekening(id) {
    $.ajax({
        url: '<?= base_url('akuntansi/get_sub_sub_sub_sub_rekening_dropdown') ?>/'+id,
        cache: false,
        dataType: 'json',
        success: function(data) {
            $('#nama_ssss').val(data.nama);
            $('#pairing').val(data.pairing);
            $('input[name=id_pairing]').val(data.id_sub_sub_sub_sub_rekening_pairing);
            
            if (data.jenis_laporan === 'SHU') {
                $('#shu').attr('checked','checked');
            }
            if (data.jenis_laporan === 'Neraca') {
                $('#neraca').attr('checked','checked');
            }
            
        }
    });
}

$("#example-advanced").treetable({ expandable: true });
//$('#example-advanced').treetable('expandAll');
// Highlight selected row
/*$("#example-advanced tbody tr").mousedown(function() {
    $("tr.selected").removeClass("selected");
    $(this).addClass("selected");
});*/
$('#add_rekening').button({
    icons: {
        secondary: 'ui-icon-circle-plus'
    }
});
$('#add_rekening').click(function() {
    dialog_rekening();
    return false;
});
$('.add_subsubrekening').click(function() {
    var id = $(this).attr('id').split('#');
    dialog_sub_sub_rekening(id[0]);
    $('#sub_rekening_id').val(id[1]);
    return false;
});
$('.add_subsubsubrekening').click(function() {
    var arr = $(this).attr('id').split('#');
    dialog_sub_sub_sub_rekening(arr[0]);
    $('#sub_sub_rek_id').val(arr[1]);
    return false;
});

$('.add_subsubsubsubrekening').click(function() {
    $('input[name=id_sub_sub_sub_sub_reks]').val('');
    var id = $(this).attr('id').split('#');
    //custom_message('Peringatan',id[0]+' - '+id[1]+' - '+id[2]+' - '+id[3]);
    var rekening = ''+
        '<select name=rekening id=rekening><?php foreach ($list_rekening as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; }  ?></select>';
    $('#nama_rekening').html(rekening);
    $('#rekening').attr('disabled','disabled');
    var subrekening= ''+
        '<select name=sub_rekening id=sub_rekening><option value="">Pilih Sub Rekening ...</option></select>';
    $('#nama_sub_rekening').html(subrekening);
    $('#sub_rekening').attr('disabled','disabled');
    load_sub_rekening(id[3], id[2]);
    
    var sub_sub_rekening = ''+
        '<select name=sub_sub_rekening id=sub_sub_rekening><option value="">Pilih Sub Sub Rekening ...</option></select>';
    $('#nama_sub_sub_rekening').html(sub_sub_rekening);
    $('#sub_sub_rekening').attr('disabled','disabled');
    load_sub_sub_rekening(id[2], id[1]);
    
    var sub_sub_sub_rekening = ''+
        '<select name=sub_sub_sub_rekening id=sub_sub_sub_rekening><option value="">Pilih Sub Sub Sub Rekening ...</option></select>';
    $('#nama_sub_sub_sub_rekening').html(sub_sub_sub_rekening);
    load_sub_sub_sub_rekening(id[1], id[0]);
    //$('#sub_sub_sub_rekening').attr('disabled','disabled');
    $('#nama_ssss').val('').focus();
    return false;
});

$('.edit_rek').click(function() {
    dialog_rekening();
    var id = $(this).attr('id');
    var arr = id.split('#');
    $('#kode_rek, #kode_rek_id').val(arr[0]);
    $('#nama_rekeningx').val(arr[1]);
    return false;
});
$('.edit_sub_rek').click(function() {
    var id = $(this).attr('id');
    var arr = id.split('#');
    dialog_subrekening(arr[0]);
    $('#kode_sub_rek_id,#kode_sub_rek').val(arr[1]);
    $('#rekening_id').val(arr[0]);
    $('#nama_sub').val(arr[2]);
    return false;
});
$('#rekening').live('change', function() {
    var id_rek = $(this).val();
    $.ajax({
        url: '<?= base_url('akuntansi/get_sub_rekening_dropdown') ?>/'+id_rek,
        cache: false,
        success: function(data) {
            $('#nama_sub_rekening').html(data);
        }
    });
});
$('.delete').click(function() {
    var ok = confirm('Apakah anda yakin akan menghapus data ini ?');
    if (ok) {
        $.ajax({
            url: $(this).attr('href'),
            cache: false,
            success: function(data) {
                alert_delete();
                get_list_rekening(1, null);
            }
        });
    }
    return false;
});

/*Rekening Manage*/
function dialog_rekening() {
    var str = '<div class=data-input id=dialogx><form action="" id=form_rekening>'+
        '<tr><td>Kode.:</td><td><?= form_input('kode_rek',get_last_id('rekening', 'id'),'id=kode_rek') ?><input type=hidden name=kode_rek_id id=kode_rek_id />'+
        '<tr><td>Nama:</td><td><?= form_input('nama_rekening', NULL, 'id=nama_rekeningx size=40') ?>'+
        '</form></div>';
    
    $('#dialog_form').append(str);
    $('#dialogx').dialog({
        autoOpen: true,
        title :'Tambah Rekening',
        height: 200,
        width: 650,
        modal: true,
        close: function() {
            $(this).dialog().remove(); 
        },
        open: function() {
            $('#nama_rekeningx').focus();
        },
        buttons: {
            "Simpan": function() { 
                save_rekening();
                $(this).dialog().remove();
            },
            "Batal": function() { 
                $(this).dialog().remove(); 
            }
        } 
    });
}
function save_rekening() {
    if ($('#kode_rek').val() == '') {
        custom_message('Peringatan','Kode rekening tidak boleh kosong !');
        $('#kode_rek').focus();
        return false;
    }
    if ($('#nama_rekeningx').val() == '') {
        custom_message('Peringatan','Nama rekening tidak boleh kosong !');
        $('#nama_rekeningx').focus();
        return false;
    }
    if ($('#kode_rek_id').val() == '') {
        var url = '<?= base_url('akuntansi/manage_rekening') ?>/add/';
    } else {
        var url = '<?= base_url('akuntansi/manage_rekening') ?>/edit_rek/';
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: $('#form_rekening').serialize(),
        success: function(data) {
            $('#dialogx').dialog().remove(); 
            if ($('#kode_rek_id').val() == '') {
                alert_tambah();
            } else {
                alert_edit();
            }
            $('#list_rekening').html(data);
        },
        error: function() {
            alert_tambah_failed();
        }
    });
}

/*Sub Rekening Manage*/
$('.add_subrekening').click(function() {
    var value = $(this).attr('id');
    dialog_subrekening(value);
    return false;
});
function dialog_subrekening(id_rek) {
    var str = '<div class="data-input" id="dialogy">'+
    '<form action="" id=form_subrekening>'+
    '<tr><td>Kode:</td><td><?= form_input('kode_sub_rek', get_last_id('sub_rekening', 'id'), 'id=kode_sub_rek') ?><input type=hidden name=kode_sub_rek_id id=kode_sub_rek_id />'+
    '<tr><td>Nama Rekening:</td><td><select name="rekening_id" id="rekening_id"><option value="">Pilih rekening ...</option><?php foreach ($list_rekening as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; } ?></select>'+
    '<tr><td>Nama:</td><td><?= form_input('nama_sub', NULL, 'id=nama_sub size=40') ?>'+
    '</form>'+
    '</div>';
    $('#dialog_form').append(str);
    $('#rekening_id').val(id_rek);
    $('#dialogy').dialog({
        autoOpen: true,
        title :'Tambah Sub Rekening',
        height: 200,
        width: 650,
        modal: true,
        close: function() {
            $(this).dialog().remove(); 
        },
        open: function() {
            $('#nama_sub').focus();
        },
        buttons: {
            "Simpan": function() { 
                save_subrekening();
                $(this).dialog().remove();
            },
            "Batal": function() { 
                $(this).dialog().remove(); 
            }
        } 
    });
}
function save_subrekening() {
    if ($('#kode_sub_rek').val() == '') {
        custom_message('Peringatan','Kode sub rekening tidak boleh kosong !');
        $('#kode_sub_rek').focus();
        return false;
    }
    if ($('#rekening_id').val() === '') {
        custom_message('Peringatan','Rekening tidak boleh kosong !');
        $('#rekening_id').focus();
        return false;
    }
    if ($('#nama_sub').val() === '') {
        custom_message('Peringatan','Nama Sub rekening tidak boleh kosong !');
        $('#nama_sub').focus();
        return false;
    }
    if ($('#kode_sub_rek_id').val() == '') {
        var url = '<?= base_url('akuntansi/manage_rekening') ?>/add_sub/';
    } else {
        var url = '<?= base_url('akuntansi/manage_rekening') ?>/edit_sub/';
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: $('#form_subrekening').serialize(),
        success: function(data) {
            $('#dialogy').dialog().remove(); 
            if ($('#kode_sub_rek_id').val() == '') {
                alert_tambah();
            } else {
                alert_edit();
            }
            $('#list_rekening').html(data);
            $('#example-advanced').treetable('expandAll');
        },
        error: function() {
            alert_tambah_failed();
        }
    });
}

function dialog_sub_sub_rekening(id_sub_rekening) {
    var str = '<div class="data-input" id="dialogz">'+
    '<form action="" id=form_subsubrekening>'+
    '<tr><td>Kode:</td><td><?= form_input('kode_sub_sub_rek', get_last_id('sub_sub_rekening', 'id'), 'id=kode_sub_sub_rek') ?><input type=hidden name=sub_sub_rek_id id=sub_sub_rek_id />'+
    '<tr><td>Sub Rekening:</td><td><?= form_input('', NULL, 'id=sub_rekening_id size=30') ?><input name=sub_rekening_id type=hidden />'+
    '<tr><td>Nama:</td><td><?= form_input('nama_sub_sub', NULL, 'id=nama_sub_sub size=40') ?>'+
    '</form>'+
    '</div>';
    $('#loaddata').append(str);
    $('#sub_rekening_id').autocomplete("<?= base_url('akuntansi/get_sub_rekening_auto') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].srekening // nama field yang dicari
                };
            }
            $('input[name=id_pairing]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.srekening+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=sub_rekening_id]').val(data.id);
    });
    $('input[name=sub_rekening_id]').val(id_sub_rekening);
    $('#dialogz').dialog({
        autoOpen: true,
        title: 'Sub Sub Rekening',
        width: 600,
        height: 220,
        modal: true,
        open: function() {
            $('#nama_sub_sub').focus();
        },
        buttons: {
            "Simpan": function() {
                save_sub_sub();
                $(this).dialog().remove();
            }, 
            "Batal": function() {
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }
    });

}

function save_sub_sub() {
    var id = $('#sub_sub_rek_id').val();
    if (id === '') {
        var url = '<?= base_url('akuntansi/manage_rekening/add_sub_sub_rek') ?>';
    } else {
        var url = '<?= base_url('akuntansi/manage_rekening/edit_sub_sub_rek') ?>';
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: $('#form_subsubrekening').serialize(),
        success: function(data) {
            $('#dialogz').dialog().remove(); 
            if (id === '') {
                alert_tambah();
            } else {
                alert_edit();
            }
            $('#list_rekening').html(data);
            $('#example-advanced').treetable('expandAll');
        },
        error: function() {
            alert_tambah_failed();
        }
    });
}

function dialog_sub_sub_sub_rekening(id_sub_sub) {
    var str = '<div class=data-input id=dialogq>'+
        '<form action="" id="form_sub_sub_sub">'+
        '<tr><td>Kode:</td><td><?= form_input('kode', get_last_id('sub_sub_sub_rekening', 'id'), 'id=kode') ?><input type=hidden name=id_sub_sub_sub id=id_sub_sub_sub />'+
        '<tr><td>Sub Sub Rekening:</td><td><?= form_input('', NULL, 'id=sub_sub_rek_id size=30') ?><input type=hidden name=sub_sub_rek_id />'+
        '<tr><td>Nama:</td><td><?= form_input('nama', NULL, 'id=nama_sss size=30') ?>'+
        '</form>'+
        '</div>';
    $('#loaddata').append(str);
    $('#sub_sub_rek_id').val();
    $('input[name=sub_sub_rek_id]').val(id_sub_sub);
    $('#sub_sub_rek_id').autocomplete("<?= base_url('akuntansi/get_sub_sub_rek_auto') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].ssrekening // nama field yang dicari
                };
            }
            $('input[name=id_pairing]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.ssrekening+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=sub_sub_rek_id]').val(data.id);
    });
    $('#dialogq').dialog({
        autoOpen: true,
        title: 'Sub Sub Sub Rekening',
        width: 600,
        height: 220,
        modal: true,
        open: function() {
            $('#nama_sss').focus();
        },
        buttons: {
            "Simpan": function() {
                save_sub_sub_sub();
                $(this).dialog().remove();
            }, 
            "Batal": function() {
                $(this).dialog().remove();
            }
        },
        close: function() {
            $(this).dialog().remove();
        }
    });
}

function save_sub_sub_sub() {
    var id = $('#id_sub_sub_sub').val();
    if (id === '') {
        var url = '<?= base_url('akuntansi/manage_rekening/add_sub_sub_sub_rek') ?>';
    } else {
        var url = '<?= base_url('akuntansi/save_edit_sub_sub_sub_rek') ?>';
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: $('#form_sub_sub_sub').serialize(),
        success: function(data) {
            $('#dialogq').dialog().remove(); 
            if (id === '') {
                alert_tambah();
            } else {
                alert_edit();
            }
            $('#list_rekening').html(data);
            $('#example-advanced').treetable('expandAll');
        }
    });
}

function delete_ssss(el, id) {
    $("<div id='dialogq'>Anda yakin akan menghapus data ini ?</div>").dialog({
        title: 'Konfirmasi Penghapusan',
        modal: true,
        buttons: {
            "OK": function() {
                $(this).dialog().remove();
                var parent = el.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
                $.ajax({
                    url: '<?= base_url('akuntansi/delete_subsubsubsubrekening') ?>/'+id,
                    cache: false,
                    success: function(data) {
                        
                    }
                });
            },
            "Batal": function() {
                $(this).dialog().remove();
            }
        }
    });
    return false;
}

function delete_sss(el, id) {
    $("<div id='dialogq'>Anda yakin akan menghapus data ini ?</div>").dialog({
        title: 'Konfirmasi Penghapusan',
        modal: true,
        buttons: {
            "OK": function() {
                $(this).dialog().remove();
//                var parent = el.parentNode.parentNode;
//                parent.parentNode.removeChild(parent);
                $.ajax({
                    url: '<?= base_url('akuntansi/delete_subsubsubrekening') ?>/'+id,
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === true) {
                            alert_delete();
                            $('#loaddata').load('<?= base_url('akuntansi/rekening') ?>');
                        }
                    }
                });
            },
            "Batal": function() {
                $(this).dialog().remove();
            }
        }
    });
    return false;
}
$('.edit_ssss').live('click', function() {
    var id = $(this).attr('id').split('#');
    var rekening = ''+
        '<select name=rekening id=rekening><?php foreach ($list_rekening as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; }  ?></select>';
    $('#nama_rekening').html(rekening);
    $('#rekening').attr('disabled','disabled');
    $('#rekening').val(id[0]);
    var subrekening= ''+
        '<select name=sub_rekening id=sub_rekening><option value="">Pilih Sub Rekening ...</option></select>';
    $('#nama_sub_rekening').html(subrekening);
    load_sub_rekening(id[0], id[1]);
    $('#sub_rekening').attr('disabled','disabled');
    
    var sub_sub_rekening = ''+
        '<select name=sub_sub_rekening id=sub_sub_rekening><option value="">Pilih Sub Sub Rekening ...</option></select>';
    $('#nama_sub_sub_rekening').html(sub_sub_rekening);
    load_sub_sub_rekening(id[1], id[2]);
    $('#sub_sub_rekening').attr('disabled','disabled');
    var sub_sub_sub_rekening = ''+
        '<select name=sub_sub_sub_rekening id=sub_sub_sub_rekening><option value="">Pilih Sub Sub Sub Rekening ...</option></select>';
    $('#nama_sub_sub_sub_rekening').html(sub_sub_sub_rekening);
    load_sub_sub_sub_rekening(id[2], id[3]);
    //$('#sub_sub_sub_rekening').attr('disabled','disabled');
    load_sub_sub_sub_sub_rekening(id[4]);
    $('input[name=id_sub_sub_sub_sub_reks], #kode_subsubsubsub').val(id[4]);
    if (id[5] === 'Kredit') { $('#kredit').attr('checked', 'checked'); } else { $('#debet').attr('checked', 'checked'); }
    return false;
});

$('.edit_sss').click(function() {
    var id = $(this).attr('id').split('#');
    dialog_sub_sub_sub_rekening(id[2]);
    //custom_message('Peringatan',id[2]);
    $('#sub_sub_rek_id').val(id[5]);
    $('input[name=sub_sub_rek_id]').val(id[2]);
    $('#kode, #id_sub_sub_sub').val(id[3]);
    $('#nama_sss').val(id[4]);
    return false;
});
$('.edit_sub_sub').click(function() {
    var id = $(this).attr('id').split('#');
    dialog_sub_sub_rekening(id[2]);
    $('#sub_rekening_id').val(id[1]);
    $('#kode_sub_sub_rek, #sub_sub_rek_id').val(id[0]);
    $('#nama_sub_sub').val(id[3]);
    return false;
});
</script>
<div class="data-list">
    <div id="dialog_form"></div>
    <?= form_button(null, 'Tambah Rekening', 'id=add_rekening') ?>
    <table class="list-data-advance" width="100%" id="example-advanced">
        <tr>
            <th width="25%">Kode</th>
            <th width="45%">Nama</th>
            <th width="25%">#</th>
        </tr>
        <?php 
        // Rekening
        foreach ($list_data as $r1 => $data) { ?>
        <tr data-tt-id='<?= $r1 ?>' class="even" style="font-weight: bold;">
            <td><?= anchor('', $data->id, 'class=edit_rek id="'.$data->id.'#'.$data->nama.'"') ?></td>
            <td><?= $data->rekening ?></td>
            <td><?= anchor('akuntansi/delete_rekening/'.$data->id, 'Hapus', 'class=delete') ?> <?= anchor('akuntansi/tambah_sub_rekening/'.$data->id, 'Tambah Sub', 'class=add_subrekening id='.$data->id) ?></td>
        </tr>
        <?php 
            // Sub Rekening
            if (isset($id_sub)) {
                $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) {
                $id_sub = $data->id_sub_rekening;
            } else {
                $id_sub = NULL;
            }
            $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
            foreach ($sub_rekening as $r2 => $rows) { ?>
                <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>' data-tt-parent-id='<?= $r1 ?>' class="even">
                    <td><?= anchor('', $data->id.'.'.$rows->id, 'class=edit_sub_rek id="'.$data->id.'#'.$rows->id.'#'.$rows->nama.'"') ?></td>
                    <td><?= $rows->nama ?></td>
                    <td><?= anchor('akuntansi/delete_subrekening/'.$rows->id, 'Hapus', 'class=delete') ?> <?= anchor('akuntansi/tambah_sub_subrekening/'.$rows->id, 'Tambah Sub Sub', 'class=add_subsubrekening id="'.$rows->id.'#'.$rows->nama.'#'.$data->nama.'#'.$data->id.'" title="'.$rows->id.'#'.$rows->nama.'#'.$data->nama.'#'.$data->id.'"') ?></td>
                </tr>
                    <?php 
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(isset($id_sub_sub)?$id_sub_sub:NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { ?>
                        <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>' class="even">
                            <td><?= anchor('', $data->id.'.'.$rows->id.'.'.$rowx->id, 'class=edit_sub_sub id="'.$rowx->id.'#'.$rows->nama.'#'.$rowx->id_subrekening.'#'.$rowx->nama.'"') ?></td>
                            <td><?= $rowx->nama ?></td>
                            <td><?= anchor('akuntansi/delete_subsubrekening/'.$rowx->id, 'Hapus', 'class=delete') ?> <?= anchor('akuntansi/tambah_sub_sub_subrekening/'.$rowx->id, 'Tambah Sub Sub Sub', 'class=add_subsubsubrekening id="'.$rowx->id.'#'.$rowx->nama.'"') ?></td>
                        </tr>
                        <?php
                        $sub_sub_sub_rekening = $this->m_akuntansi->data_subsubsub_rekening_load_data(isset($id_sub_sub_sub)?$id_sub_sub_sub:NULL, $rowx->id)->result();
                        foreach ($sub_sub_sub_rekening as $r4 => $rowy) { ?>
                            <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>-<?= $r4 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>' class="even">
                                
                                <td><?= anchor('',$data->id.'.'.$rows->id.'.'.$rowx->id.'.'.$rowy->id,'class=edit_sss id="'.$data->id.'#'.$rows->id.'#'.$rowx->id.'#'.$rowy->id.'#'.$rowy->nama.'#'.$rowx->nama.'"') ?></td>
                                <td><?= $rowy->nama ?></td>
                                <td><span style="cursor: pointer;" onclick="delete_sss(this, <?= $rowy->id ?>)">Hapus</span> <?= anchor('', 'Tambah Sub Sub Sub Sub', 'class=add_subsubsubsubrekening id="'.$rowy->id_sub_sub_sub_rekening.'#'.$rowy->id_sub_sub_rekening.'#'.$rowy->id_sub_rekening.'#'.$rowy->id_rekening.'"') ?></td>
                            </tr>
                        <?php
                            $sub_sub_sub_sub_rekening = $this->m_akuntansi->data_subsubsubsub_rekening_load_data(isset($id_sub_sub_sub_sub)?$id_sub_sub_sub_sub:NULL, $rowy->id)->result();
                            foreach ($sub_sub_sub_sub_rekening as $r5 => $rowz) { ?>
                                <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>-<?= $r4 ?>-<?= $r5 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>-<?= $r4 ?>' class="even">

                                    <td><?= anchor('',$data->id.'.'.$rows->id.'.'.$rowx->id.'.'.$rowy->id.'.'.$rowz->id,'class=edit_ssss id="'.$data->id.'#'.$rows->id.'#'.$rowx->id.'#'.$rowy->id.'#'.$rowz->id.'"') ?></td>
                                    <td><?= $rowz->sub_sub_sub_sub_rekening ?></td>
                                    <td><span style="cursor: pointer;" onclick="delete_ssss(this, '<?= $rowz->id ?>')">Hapus</span></td>
                                </tr>
                            <?php

                            }
                        }
                    }
            }
        } ?>
    </table>
</div>