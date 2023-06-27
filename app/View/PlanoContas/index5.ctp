<?php $url_novo = $this->base."/plano_contas/add/5/".$pai_id."?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>
<div class="page page-profile">

	<?php
			echo $this->element("aba_plano_contas_list", array('pai_id' => $pai_id, 'nivel', $nivel));
	?>

	<section class="panel panel-default">
		<div class="panel-heading">
			<h3 class="box-title">Plano de contas</h3>
			<div class="row">
				<div class="col-md-4 col-lg-6">
					<div class="form-group">
						<a href="<?php echo $url_novo;?>" class="btn btn-primary">
							<i class="fa fa-file"></i>
							Novo
						</a>
					</div>
				</div>

				<form class="col-md-8 col-lg-6" action="<?php echo $this->Html->url(array( "controller" => "plano_contas/index5/5/$pai_id", "action" => "index")); ?>/" role="form" id="busca">
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
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
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
			<?php echo $this->element("table"); ?>
				<thead>
					<tr>
						<th>Status</th>
						<th>Descrição do plano</th>
						<th>Número de identificação</th>
						<th>Referência</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data){ ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td>
									<span class='label <?php echo $data[$i]["Status"]["label"] ?>'>
										<?php echo $data[$i]["Status"]["name"] ?>
									</span>
								</td>
								<td><?php echo $data[$i]["PlanoConta"]["name"]; ?></td>
								<td><?php echo $data[$i]["PlanoConta"]["numero"]; ?></td>
								<td><?php echo $data[$i]["PlanoConta"]["referencia"]; ?></td>
								<td>
									<a href="<?php echo $this->base.'/plano_contas/edit/'.$data[$i]["PlanoConta"]["id"].'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-info btn-xs">
										Editar
									</a>
									<a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/plano_contas/delete/'.$data[$i]["PlanoConta"]["id"].'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-xs">
										Excluir
									</a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
							<tr>
								<td colspan="6">Nenhum registro encontrado.</td>
							</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php echo $this->element("pagination"); ?>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->