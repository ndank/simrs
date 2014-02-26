<div class="data-list">
	<div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
	<table class="tabel" id="tbl_hasil" width="100%">
	    <thead>
	        <tr>
	            <th width="10%">ID Kunjungan</th>
	            <th width="25">Nama</th>
	            <th width="25%">Kelurahan</th>
	            <th width="25%">Alamat</th>
	            <th width="10%">#</th>
	        </tr>
	    </thead>
	    <tbody>
	    <?php if(sizeof($customer) > 0): ?>
			<?php foreach ($customer as $key => $value):?>
				<tr>
					<td align="center"><?= $value->no_daftar ?></td>
					<td><?= $value->nama ?></td>
					<td><?= $value->kelurahan.", ".$value->kecamatan.", ".$value->kabupaten ?></td>
					<td><?= $value->alamat ?></td>
					<td class="aksi">
						<?php if($value->pk == 0): ?>
						<span class="link_button" onclick="entry_fisioterapi('<?= $value->no_daftar ?>')">Entry Pelayanan Fisioterapi</span>
						<?php else: ?>
						<span class="link_button" onclick="edit_fisioterapi('<?= $value->no_daftar ?>','<?= $value->id_pk ?>')">Edit Pelayanan Fisioterapi</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach;?>
		<?php else: ?>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
		<?php endif; ?>
		</tbody>
	</table>
	<br/>
	<div id="paging"><?= $paging ?></div>
<div>