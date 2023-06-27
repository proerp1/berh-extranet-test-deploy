<?php echo $this->element("table"); ?>
	<thead>
		<tr class="fw-bolder text-muted bg-light">
			<th class="ps-4 min-w-125px rounded-start">Status</th>
			<th>Nome</th>
			<th>Tipo</th>
			<th>Cliente</th>
			<th>Documento</th>
			<th>Número do titulo negativado</th>
			<th>Valor</th>
			<th>Coobrigado/Avalista</th>
			<th>Cadastro</th>
			<th <?php echo isset($pdf) ? 'class="min-w-125px rounded-end"' : '' ?>>Erros</th>
			<?php if (!isset($pdf)): ?>
				<th class="min-w-125px rounded-end">Ações</th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0; ?>
		<?php if ($data) { ?>
			<?php for ($i=0; $i < count($data); $i++) { ?>
				<?php $erros = $data[$i]['CadastroPefinErros'] ?>
				<?php 
					$status = $data[$i]["Status"]["name"];
					$sLabel = $data[$i]["Status"]["label"];
					$date1 = new DateTime($data[$i]['CadastroPefin']['venc_divida_nao_formatado']." 23:59:59");
					$date2 = new DateTime();
					$intervalo = $date1->diff($date2);
					$intervaloMeses = $intervalo->y*12 + $intervalo->m;
					if ($intervaloMeses > 59) {
						$status = 'Baixado decurso do prazo';
						$sLabel = 'label-success';
					}
				?>
				<tr>
					<td class="fw-bold fs-7 ps-4">
						<span class='badge <?php echo $sLabel ?>'>
							<?php echo $status ?>
						</span>
					</td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['nome'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['NaturezaOperacao']['nome'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?> - <?php echo $data[$i]['Customer']['nome_secundario'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['documento'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['numero_titulo'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['valor'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['coobrigado_nome'] ?></td>
					<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['created']; //date('d/m/Y H:i:s', strtotime($data[$i]['CadastroPefin']['created'])) ?></td>
					<td class="fw-bold fs-7 ps-4">
						<?php 
							if (!empty($erros) and $data[$i]["Status"]["id"] == 23) {
								for ($a=0; $a < count($erros); $a++) { 
									echo $erros[$a]['ErrosPefin']['descricao'].'<br>';
								}
							}
						?>
					</td>
					<?php if (!isset($pdf)): ?>
						<td class="fw-bold fs-7 ps-4">
							<a href="<?php echo $this->base.'/negativacao/view/'.$data[$i]["CadastroPefin"]["id"]; ?>" class="btn btn-info btn-sm">Detalhes</a>
						</td>
					<?php endif ?>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="11">Nenhum registro encontrado</td>
			</tr>
		<?php } ?>
	</tbody>
</table>