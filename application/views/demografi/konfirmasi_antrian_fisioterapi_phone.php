<div class="kegiatan">
<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<script type="text/javascript">
	$(function(){
		$('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('#cari_antri').button({icons: {secondary: 'ui-icon-search'}});
		$(".tanggal").datepicker({
            changeYear : true,
            changeMonth : true
        });
        $('#reset').click(function(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        
        });
	});

	function get_list(p){  
        $.ajax({
            url: '<?= base_url("demografi/search_antrian_fisioterapi/") ?>/'+p,
            data: $('#form').serialize(),
            cache: false,
            success: function(msg) {
                $('#list').html(msg);                       
            }
        });    
    }
    
    function paging(page, tab,search){
        get_list(page);
    }

</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="data-input">
	<?= form_open('','id=form') ?>
	<fieldset>
	    <tr><td>Tanggal:</td><td><?= form_input('tanggal',date('d/m/Y'),'class=tanggal size=15')?>
	    <tr><td>Jenis Layanan:</td><td><span class="label"><?= form_hidden('layanan', $id_jurusan)?>Fisioterapist</span>
	    <tr><td>No. Antrian:</td><td><?= form_input('antri', null, 'size=40 id=no_antri onkeyup="Angka(this)"') ?>
	    <tr><td></td><td><?= form_button('cari', 'Cari', 'class=cari id=cari_antri onclick=get_list(1)') ?>
	    <?= form_button('', 'Reset', 'class=resetan id=reset') ?></td></tr>
	</table>
	<?= form_close() ?>
</div>
<div id="list"></div>
</div>