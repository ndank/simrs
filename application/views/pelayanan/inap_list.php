<div class="data-list">
	<table class="list-data" id="tabel_inap" width="100%">
    	<thead>
    		<th width="3%">ID</th>
    		<th width="15%">Waktu</th>
    		<th width="10%">Bangsal</th>
    		<th width="5%">Kelas</th>
    		<th width="5%">No. TT</th>
    		<th width="30%">Dokter Penanggung Jawab</th>
    		<th width="20%">#</th>
    	</thead>

    	<tbody>
    		<?php if (count($inap) == 0) : ?>
	        <?php for ($i = 1; $i <= 2; $i++) : ?>
	            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
	                <td>&nbsp;</td>
	                <td></td>
	                <td></td>
	                <td></td>
	                <td></td>
	                <td></td>
	                <td class="aksi"></td>
	            </tr>
	        <?php endfor; ?>
		    <?php else: ?>
		        <?php foreach ($inap as $key => $data): ?>
		            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
		            	<td align="center"><span title="Klik untuk edit pelayanan rawat inap" class="link_button" onclick="edit_pelayanan_irna('<?= $data->id ?>')"><?= $data->id ?></span></td>
		            	<td align="center"><?= ($data->waktu != null)?datetimefmysql($data->waktu, true):'-' ?></td>
		                <td><?= $data->nama_unit ?></td>
		                <td align="center"><?= $data->kelas ?></td>
		                <td align="center"><?= $data->nomor_bed ?></td>
		                <td><?= $data->nama_pegawai ?></td>
		                <td class="aksi" align="center">
		                	<span title="Klik untuk entri data vital sign" class="link_button" onclick="vital('<?= $data->id ?>')">Vital Sign</span> |
		                	<span title="Klik untuk entri data diagnosis" class="link_button" onclick="diagnosis('<?= $data->id ?>')">Diagnosis</span> |
		                	<span title="Klik untuk entri data tindakan" class="link_button" onclick="tindakan('<?= $data->id ?>')">Tindakan</span> |
		                	<span title="Klik untuk entri data pemeriksaan laboratorium" class="link_button" onclick="laboratorium('<?= $data->id ?>')">Lab</span> |
		                	<span title="Klik untuk entri data pemeriksaan radiologi" class="link_button" onclick="radiologi('<?= $data->id ?>')">Radiologi</span>
		                </td>
		            </tr>
		        <?php endforeach; ?>
		    <?php endif; ?>

    	</tbody>

    </table>
</div>