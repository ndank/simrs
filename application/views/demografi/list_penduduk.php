<div class="data-list">
	<div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
	<table class="list-data" id="tbl_hasil" width="100%">
	    <thead>
	        <tr>
	            <th width="3%">No.</th>
	            <th widt="10%">No. RM</th>
	            <th width="34%">Nama</th>
	            <th width="50%">Alamat</th>
	            <th width="3%">#</th>
	        </tr>
	    </thead>
	    <tbody>
	<?php foreach ($penduduk as $key => $value):?>
		<tr>
			<td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
			<td align="center"><?= ($value->no_rm !== null)?$value->no_rm:'-' ?></td>
			<td><?= $value->nama ?></td>
			<td><?= $value->alamat ?></td>
			<td class="aksi" align="center">
				<?php
					if(isset($value->no_daftar)){
						$no_daftar = $value->no_daftar;
					}else{
						$no_daftar = '';
					}
				?>
				<span class="choosing" onclick="pilih_penduduk('<?= $value->id_penduduk ?>','<?= $no_daftar ?>')" ></span>
			</td>
		</tr>
	<?php endforeach;?>
		</tbody>
	</table>
	
	<div id="paging"><?= $paging ?></div><br/><br/>
<div>