<div class="page page-profile">

	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-users"></i>
			</span>
			<h3>Divisão sócios</h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<section class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group">
						<a href="<?php echo $this->here.'?excel' ?>" class="btn btn-success">
							<i class="fa fa-file-excel-o"></i>
							Exportar
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<?php echo $this->element("table"); ?>
				<thead>
					<tr>
						<th class="default">Mês/ano</th>
						<th>Valor</th>
						<?php foreach ($socios as $socio){ ?>
							<th><?php echo $socio['Socios']['name'].' ('.$socio['Socios']['percentual'].'%)' ?></th>
						<?php } ?>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($dados) { ?>
						<?php for ($i=0; $i < count($dados); $i++) { ?>
							<?php $get = ''; ?>
							<tr>
								<td><?php echo $dados[$i][0]['mes'] ?></td>
								<td><?php echo number_format($dados[$i][0]['valor'],2,',','.') ?></td>
								<?php foreach ($socios as $socio){ ?>
									<?php $get .= '&'.$socio['Socios']['name'].'='.number_format($dados[$i][0]['valor']*($socio['Socios']['percentual']/100),2,'.','') ?>
									<td><?php echo number_format($dados[$i][0]['valor']*($socio['Socios']['percentual']/100),2,',','.') ?></td>
								<?php } ?>
								<td>
									<a href="<?php echo $this->base.'/divisao_socios/detalhes/?mes='.$dados[$i][0]['mes']; ?>" class="btn btn-info btn-xs">
										Visualizar
									</a>
									<a href="<?php echo $this->base.'/divisao_socios/divide_cobranca/?'.$get.'&mes='.$dados[$i][0]['mes']; ?>" class="btn btn-info btn-xs">
										Divide
									</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="<?php echo count($socios)+3 ?>">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->