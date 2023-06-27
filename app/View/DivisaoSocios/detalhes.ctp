<div class="page page-profile">

	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-users"></i>
			</span>
			<h3>Detalhe <?php echo $_GET['mes'] ?></h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>
	
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<a href="<?php echo $this->here.'?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'&excel' ?>" class="btn btn-success">
					<i class="fa fa-file-excel-o"></i>
					Exportar
				</a>
			</div>
		</div>
		<div class="col-xs-6">
			<section class="panel panel-default">
				<div class="panel-body">
					<h4>BeRH</h4>
					<?php echo $this->element("table"); ?>
						<thead>
							<tr>
								<th class="default">Cliente</th>
								<th>Valor</th>
								<th>Vencimento</th>
								<th>Ações</th>
							</tr>
						</thead>
						<tbody>
							<?php $total_hiper = 0; ?>
							<?php if ($hipercheck) { ?>
								<?php for ($i=0; $i < count($hipercheck); $i++) { ?>
									<?php $total_hiper += $hipercheck[$i]['Income']['valor_total_nao_formatado'] ?>
									<tr>
										<td><?php echo $hipercheck[$i]['Customer']['nome_primario'] ?></td>
										<td><?php echo $hipercheck[$i]['Income']['valor_total'] ?></td>
										<td><?php echo $hipercheck[$i]['Income']['vencimento'] ?></td>
										<td>
											<a href="<?php echo $this->base.'/incomes/edit/'.$hipercheck[$i]['Income']['id'] ?>" class="btn btn-info btn-xs">
												Ver fatura
											</a>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td colspan="3">Nenhum registro encontrado</td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td>Total</td>
								<td><?php echo number_format($total_hiper,2,',','.') ?></td>
							</tr>
						</tfoot>
					</table>

				</div> <!-- /painel-body -->
			</section> <!-- /panel-default -->
		</div>
		<div class="col-xs-6">
			<section class="panel panel-default">
				<div class="panel-body">
					<h4>Ivan</h4>
					<?php echo $this->element("table"); ?>
						<thead>
							<tr>
								<th class="default">Cliente</th>
								<th>Valor</th>
								<th>Vencimento</th>
								<th>Ações</th>
							</tr>
						</thead>
						<tbody>
							<?php $total_ivan = 0; ?>
							<?php if ($ivan) { ?>
								<?php for ($i=0; $i < count($ivan); $i++) { ?>
									<?php $total_ivan += $ivan[$i]['Income']['valor_total_nao_formatado'] ?>
									<tr>
										<td><?php echo $ivan[$i]['Customer']['nome_primario'] ?></td>
										<td><?php echo $ivan[$i]['Income']['valor_total'] ?></td>
										<td><?php echo $ivan[$i]['Income']['vencimento'] ?></td>
										<td>
											<a href="<?php echo $this->base.'/incomes/edit/'.$ivan[$i]['Income']['id'] ?>" class="btn btn-info btn-xs">
												Ver fatura
											</a>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td colspan="3">Nenhum registro encontrado</td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td>Total</td>
								<td><?php echo number_format($total_ivan,2,',','.') ?></td>
							</tr>
						</tfoot>
					</table>

				</div> <!-- /painel-body -->
			</section> <!-- /panel-default -->
		</div>
	</div>
	
</div> <!-- /page-profile -->