<title><?= $title ?></title>
<script type="text/javascript">
function get_same_rows() {
    var pendapatan  = $('#left tr').length;
    var beban       = $('#right tr').length;
    var selisih     = (pendapatan - beban);
    
    if (pendapatan < beban) {
        for (i = 1; i <= (selisih*(-1)); i++) {
            var baris = '<tr><td></td><td></td><td></td><td></td></tr>';
            $('#left').append(baris);
        }
    } else {
        for (i = 1; i <= selisih; i++) {
            var baris = '<tr><td></td><td></td><td></td><td></td></tr>';
            $('#right').append(baris);
        }
    }
}
$(function() {
    get_same_rows();
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#tampil').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    });
    $('#cetak').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        var awal = date2mysql($('#awal').val());
        var akhir= date2mysql($('#akhir').val());
        var wWidth = $(window).width();
        var dWidth = wWidth * 0.9;
        var wHeight= $(window).height();
        var dHeight= wHeight * 0.9;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url('akuntansi/cetak_shu') ?>/'+awal+'/'+akhir,'Cetak SHU','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').load('<?= base_url('akuntansi/lap_shu') ?>');
    });
    $('#tampil').click(function() {
        $.ajax({
            url: '<?= base_url('akuntansi/lap_shu') ?>',
            data: 'awal='+$('#awal').val()+'&akhir='+$('#akhir').val(),
            cache: false,
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    });
});
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div class="data-input">
        <?= form_open('','id=form_shu') ?>
        <table width="100%" class="inputan">
            <tr><td>Range Tanggal Jurnal:</td><td><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'style="width: 75px;" id=awal') ?> s.d <?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'style="width: 75px;" id=akhir') ?></td></tr>
            <tr><td></td><td><?= form_button(null, 'Tampil', 'id=tampil') ?> <?= form_button(null, 'Cetak', 'id=cetak') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
    </div>
    <div class="data-list">
        
        <table class="list-data" width="50%" id="left" style="float: left; clear: right;">
            <?php
            $total1 = 0;
            foreach ($pendapatan_operasional as $r1 => $data) { ?>
            <tr data-tt-id='<?= $r1 ?>' class="even" style="font-weight: bold;">
                <td width="10%"><?= $data->rekening ?></td>
                <td></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
           <?php 
           if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                    <tr class="even">
                        <td align="center"></td>
                        <td><?= $rows->nama ?></td>
                        <td align="center"></td>
                        <td align="center"></td>
                    </tr> <?php 
                    
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_po = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row(); ?>
                        <tr class="even">
                            <td align="center"></td>
                            <td></td>
                            <td><?= $rowx->nama ?></td>
                            <td align="right"><?= rupiah($total_po->total_kredit) ?></td>
                        </tr>
                        <?php
                        $total1 = $total1 + $total_po->total_kredit;
                    }
               } 
           } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b><?= rupiah($total1) ?></b></td>
            </tr>
        </table>
        
        <table class="list-data" width="50%" id="right" style="float: left; border-left: none; clear: right;">
            <?php
            $total2 = 0;
            foreach ($beban_operasional as $r1 => $data) { ?>
            <tr data-tt-id='<?= $r1 ?>' class="even" style="font-weight: bold;">
                <td><?= $data->rekening ?></td>
                <td></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
           <?php 
            if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                    <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>' data-tt-parent-id='<?= $r1 ?>' class="even">
                        <td align="center"></td>
                        <td><?= $rows->nama ?></td>
                        <td align="center"></td>
                        <td align="center"></td>    
                    </tr> <?php 
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_bo = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row();
                        ?>
                        <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>' class="even">
                            <td align="center"></td>
                            <td></td>
                            <td><?= $rowx->nama ?></td>
                            <td align="right"><?= rupiah($total_bo->total_debet) ?></td>    
                        </tr>
                        <?php
                        $total2 = $total2 + $total_bo->total_debet;
                    }
               } 
           } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b><?= rupiah($total2) ?></b></td>
            </tr>
        </table>
        <br/>
        
        <?php
            $jml_laba_bersih = $total1-$total2;
        ?>
        <table width="50%" align="right">
            <tr><td align="right" style="font-size: 18px;">LABA BERSIH:</td><td align="right" style="font-size: 18px;"><b><?= rupiah($jml_laba_bersih) ?></b></td></tr>
        </table>
    </div>
</div>