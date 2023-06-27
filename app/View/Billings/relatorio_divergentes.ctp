<div class="page page-profile">

	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-users"></i>
			</span>
			<h3><?php echo $action ?></h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-4 col-lg-6"></div>

				<form class="col-md-8 col-lg-6" action="" role="form" id="busca">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<select class="selectpicker form-control" name="t" id="t">
									<option value="">Todos</option>
									<?php
										for($a = 0; $a < count($status); $a++){
											$selected = "";
											if (isset($_GET["t"])) {
												if($status[$a]['Status']['id'] == $_GET["t"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
										}
									?>
								</select>
							</div>
						</div>

						<div class="col-md-5">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
									<input type="text" class="form-control js-input-search" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>">
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<button type="submit" class="btn btn-primary col-md-12 js-submit-search">Buscar</button>
						</div>
					</div>
				</form>
			</div>

		</div>
		<div class="panel-body">
			<form action="<?php echo $this->base.'/billings/enviar_email_reembolso/'.$id ?>" method="post">

					<div class="form-group">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-dollar"></i>
							Enivar emails
						</button>
					</div>

				<?php echo $this->element("table"); ?>
					<thead>
						<tr>
							<th>Código Associado</th>
							<th>Cliente</th>
							<th>Fevereiro R$</th>
							<th>Janeiro R$</th>
							<th>Total</th>
							<th>Ações</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($data) { ?>
							<?php for ($i=0; $i < count($data); $i++) { ?>
								<tr>
									<td><?php echo $data[$i]['c']['codigo_associado'] ?></td>
									<td><?php echo $data[$i]['c']['nome_primario'].' '.$data[$i]['c']['nome_secundario'] ?></td>
									<td>R$ <?php echo number_format($data[$i]['r']['fevereiro'],2,',','.') ?></td>
									<td>R$ <?php echo number_format($data[$i]['rjan']['janeiro'],2,',','.') ?></td>
									<td>R$ <?php echo number_format($data[$i][0]['pendente'],2,',','.') ?></td>
									<td>
										<input type="hidden" name="cod_associado[]" value="<?php echo $data[$i]['c']['codigo_associado'] ?>">
										<input type="hidden" name="valor_a_cobrar[]" value="<?php echo $data[$i][0]['pendente'] ?>">
										<input type="hidden" name="nome_secundario[]" value="<?php echo $data[$i]['c']['nome_secundario'] ?>">
										<input type="hidden" name="email[]" value="<?php echo $data[$i]['c']['email'] ?>">
										<input type="hidden" name="documento[]" value="<?php echo $data[$i]['c']['documento'] ?>">
										<input type="hidden" name="codigo_associado[]" value="<?php echo $data[$i]['c']['codigo_associado'] ?>">
										<a href="<?php echo $this->base.'/billings/demonstrativo/'.$data[$i]["b"]["id"].'/'.$data[$i]["c"]["id"]; ?>" class="btn btn-info btn-xs">
											Demonstrativo
										</a>
									</td>
								</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="6">Nenhum registro encontrado</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</form>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->