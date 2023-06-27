<?php
	echo $this->element("abas_incomes", ['id' => $id]);
?>
<div class="card mb-5 mb-xl-8">
	<div class="card-body">
        <div class="table-responsive">
        	<?php echo $this->element("table"); ?>
				<thead>
					<tr class="fw-bolder text-muted bg-light">
						<th class="ps-4 w-200px min-w-200px rounded-start">Status da ligação</th>
						<th>Histórico</th>
						<th>Usuário</th>
						<th>Data criação</th>
						<th>Novo boleto gerado?</th>
						<th>Data de retorno</th>
						<th class="w-150px min-w-150px rounded-end">Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["call_status"] == 1 ? "Com sucesso" : "Sem sucesso"; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["text"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["User"]["name"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]["ChargesHistory"]["created"])); ?></td>
								<td class="fw-bold fs-7 ps-4">
									<?php
										if ($data[$i]["ChargesHistory"]["generate_new_income"] == 1) {
											echo 'Sim - <a class="btn btn-primary btn-sm" target="_blank" href="'.$this->base.'/incomes/edit/'.$data[$i]["ChargesHistory"]["new_income_id"].'">Ver</a>';
										} else {
											echo 'Não';
										}
									?>
								</td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["ChargesHistory"]["return_date"]; ?></td>
								<td class="fw-bold fs-7 ps-4">
									<a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/cobrancas/delete_historico/'.$id.'/'.$data[$i]["ChargesHistory"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
										Excluir
									</a>
								</td>
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