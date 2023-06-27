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
			<h3>Nova vida - Cliente</h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<?php if ($exportar): ?>
						<div class="form-group">
							<a href="<?php echo $this->base.'/reports/nova_vida/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-primary">
								<i class="fa fa-file-excel-o"></i>
								Exportar
							</a>
						</div>
					<?php endif ?>
				</div>

				<form class="col-md-12" action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "nova_vida")); ?>/" role="form" id="busca">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<select class="form-control" name="p" id="p">
									<option value="" selected disabled>Produto</option>
									<option value="">Todos</option>
									<?php
										foreach ($produtos as $id => $name) {
											$selected = "";
											if (isset($_GET["p"])) {
												if($id == $_GET["p"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
										}
									?>
								</select>
							</div>
						</div>
					
						<div class="col-md-4">
							<div class="input-daterange input-group" id="datepicker">
								<input type="text" class="form-control" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
								<span class="input-group-addon">até</span>
								<input type="text" class="form-control" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
									<input type="text" class="form-control js-input-search" placeholder="Código do Associado" name="c" value="<?php echo isset($_GET["c"]) ? $_GET["c"] : ""; ?>">
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
									<input type="text" class="form-control js-input-search" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>">
								</div>
							</div>
						</div>

						<div class="col-md-2">
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
						<th class="default">Produto</th>
						<th>Data</th>
						<th class="default">Associado</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td><?php echo $data[$i]['Product']['name'] ?></td>
								<td><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['NovaVidaLogConsulta']['created'])) ?></td>
								<td><?php echo $data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'] ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php echo $data ? $this->element("pagination") : ''; ?>

		</div> <!-- /painel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->