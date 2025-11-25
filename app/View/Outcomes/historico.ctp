<?php
	echo $this->element("abas_outcomes", ['id' => $id]);
?>
<div class="card mb-5 mb-xl-8">
	<div class="card-body">
        <div class="table-responsive">
        	<?php echo $this->element("table"); ?>
				<thead>
					<tr class="fw-bolder text-muted bg-light">
						<th class="ps-4 rounded-start">Alteração</th>
						<th>Usuário</th>
						<th class="ps-4 rounded-end">Data alteração</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OutcomeLog"]["description"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["User"]["name"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]["OutcomeLog"]["created"])); ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="7" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
        </div>
    </div>
</div>