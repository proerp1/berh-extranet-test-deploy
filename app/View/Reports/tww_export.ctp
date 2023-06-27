<script type="text/javascript">
	$(document).ready(function(){
		$(".input-daterange").datepicker({format: 'dd/mm/yyyy', multidate: false, weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true});
	})
</script>
<div class="page page-profile">

	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-list"></i>
			</span>
			<h3>Exportação para TWW</h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<form class="col-md-12" action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "tww_export")); ?>/" role="form" id="busca">
					<div class="row">

						<div class="col-md-3">
							<div class="input-daterange input-group" id="datepicker">
								<input type="text" class="form-control" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
								<span class="input-group-addon">até</span>
								<input type="text" class="form-control" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
							</div>
						</div>

						<!-- <div class="col-md-3">
							<div class="form-group">
								<select class="selectpicker form-control" name="r" id="r">
									<option value="" selected disabled>Regra</option>
									<option value="todos" <?php echo isset($_GET['r']) ? ($_GET['r'] == 'todos' ? 'selected' : '') : '' ?>>Todos</option>
									<option value="igual" <?php echo isset($_GET['r']) ? ($_GET['r'] == 'igual' ? 'selected' : '') : '' ?>>Data igual a do vencimento</option>
									<option value="menor" <?php echo isset($_GET['r']) ? ($_GET['r'] == 'menor' ? 'selected' : '') : '' ?>>Data menor ou igual que a do vencimento</option>
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<select class="selectpicker form-control" name="f" id="f">
									<option value="" selected disabled>Faturamento</option>
									<option value="">Todos</option>
									<?php
										for($a = 0; $a < count($faturamentos); $a++){
											$selected = "";
											if (isset($_GET["f"])) {
												if($faturamentos[$a]['Billing']['date_billing_nao_formatado'] == $_GET["f"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$faturamentos[$a]['Billing']['date_billing_nao_formatado'].'" '.$selected.'>'.$faturamentos[$a]['Billing']['date_billing'].'</option>';
										}
									?>
								</select>
							</div>
						</div> -->

						<div class="col-md-2">
							<div class="form-group">
								<select class="selectpicker form-control" name="sc" id="sc">
									<option value="">Status do cliente</option>
									<?php
										for($a = 0; $a < count($status_cliente); $a++){
											$selected = "";
											if (isset($_GET["sc"])) {
												if($status_cliente[$a]['Status']['id'] == $_GET["sc"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$status_cliente[$a]['Status']['id'].'" '.$selected.'>'.$status_cliente[$a]['Status']['name'].'</option>';
										}
									?>
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<select class="selectpicker form-control" name="s" id="s">
									<option value="">Status da conta</option>
									<?php
										for($a = 0; $a < count($status); $a++){
											$selected = "";
											if (isset($_GET["s"])) {
												if($status[$a]['Status']['id'] == $_GET["s"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
										}
									?>
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
									<input type="text" placeholder="Nome do grupo" class="form-control js-input-search" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>">
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<button type="submit" class="btn btn-primary col-md-12 js-submit-search">Buscar</button>
						</div>
					</div>
				</form>

				<div class="col-md-12">
					<?php if ($buscar): ?>
						<div class="form-group">
							<a href="<?php echo $this->base.'/reports/tww_export/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-primary">
								<i class="fa fa-file-excel-o"></i>
								Exportar
							</a>
						</div>
					<?php endif ?>
				</div>

			</div>
		</div>

		<div class="panel-body">
			<h4><?php echo count($data) ?> cliente(s)</h4>
			<?php echo $this->element("table"); ?>
				<thead>
					<tr>
						<th class="default">Cliente</th>
						<th>Telefone</th>
						<th class="default">Email</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<?php
							// hack para funcionar como o ramon pediu
								$celular = '';
								if ($data[$i]['Customer']['celular'] != '') {
									$celular = $data[$i]['Customer']['celular'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								} 

								if ($data[$i]['Customer']['celular1'] != '') {
									$celular = $data[$i]['Customer']['celular1'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								} 

								if ($data[$i]['Customer']['celular2'] != '') {
									$celular = $data[$i]['Customer']['celular2'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								} 

								if ($data[$i]['Customer']['celular3'] != '') {
									$celular = $data[$i]['Customer']['celular3'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								} 

								if ($data[$i]['Customer']['celular4'] != '') {
									$celular = $data[$i]['Customer']['celular4'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								} 

								if ($data[$i]['Customer']['celular5'] != '') {
									$celular = $data[$i]['Customer']['celular5'];

									echo '
									<tr>
										<td>'.$data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'].'</td>
										<td>'.$celular.'</td>
										<td>'.$data[$i]['Customer']['email'].'</td>
									</tr>';
								}
							?>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->