<div class="data-list">
	
	<table class="list-data" width="100%">
	    <thead>
	        <tr>
	            <th width="45%">Nama</th>
	            <th width="30%">Kelas</th>
	            <th width="15%">No. Bed</th>
	            <th width="10%">#</th>
	        </tr>
	    </thead>
	    <tbody>

	<?php foreach ($bed as $key => $value):?>
		<?php 
			if($key === 0){
				$id_unit = $value->id_unit;
				$unit = $value->nama;
			}else{
				if($id_unit !== $value->id_unit){
					$id_unit = $value->id_unit;
					$unit = $value->nama;
				}else{
					$unit = '';
				}
			}

		?>
		<tr>
                    <td><b><?= $unit ?></b></td>
                    <td align="center"><?= $value->kelas ?></td>
                    <td align="center"><?= $value->nomor ?></td>
                    <td class="aksi" align="center">
                        <span title="Click untuk pilih" style="cursor: pointer;" class="link_button" onclick="pilih_bed('<?= $value->id ?>', '<?= $value->nama ?>','<?= $value->kelas ?>', '<?= $value->nomor ?>', '<?= $value->id_tarif ?>', '<?= $value->id_unit ?>')" >Pilih</span>
                    </td>
		</tr>
		<?php
			
		?>
	<?php endforeach;?>
		</tbody>
	</table>
	<br/>
<div>