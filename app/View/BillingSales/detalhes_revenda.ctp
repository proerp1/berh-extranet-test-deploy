<div class="page page-profile">

	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-dollar"></i>
			</span>
			<h3><?php echo $action ?></h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>
	
	<?php
		$url = $this->base.'/billing_sales/revenda';
		echo $this->element("aba_faturamento_vendas_revenda", array('id' => $id, 'url' => $url));
	?>

	<section class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->element("table"); ?>
				<thead>
					<tr>
						<th class="default">Cliente</th>
						<th>Porcentagem comissão</th>
						<th>Valor pago</th>
						<th>Valor comissão</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$total_pago = 0;
						$total_comissao = 0;
					?>
					<?php if ($dados) { ?>
						<?php for ($i=0; $i < count($dados); $i++) { ?>
							<?php
								$total_pago += $dados[$i]['i']['valor_pago'];
								$total_comissao += $dados[$i][0]['valor_comissao'];
							?>
							<tr>
								<td><?php echo $dados[$i]['c']['nome_primario'] ?></td>
								<td><?php echo number_format($dados[$i]['r']['valor_recebido_cliente'],2,'.','') ?>%</td>
								<td>R$ <?php echo number_format($dados[$i]['i']['valor_pago'],2,',','.') ?></td>
								<td>R$ <?php echo number_format($dados[$i][0]['valor_comissao'],2,',','.') ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="4">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th>Total:</th>
						<th>R$ <?php echo number_format($total_pago,2,',','.') ?></th>
						<th>R$ <?php echo number_format($total_comissao,2,',','.') ?></th>
					</tr>
				</tfoot>
			</table>
			<a href="<?php echo $this->base.'/billing_sales/revenda/'.$id; ?>" class="btn btn-default">Voltar</a>
		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->