<?php echo $this->Html->css('css_temp/style'); ?>
<!-- .row -->
 <div class="row">
 	<div class="col-lg-3 col-sm-6 col-xs-12">
 		<div class="white-box">
 			<h3 class="box-title">Pedidos aprovados</h3>
 			<ul class="country-state">
 				<li>
 					<h2>R$ 0,00</h2> <small>Aguardando emissão dos boletos</small>
 					<div class="pull-right">48% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:48%;"> <span class="sr-only">48% realizado</span></div>
 					</div>
 				</li>
 			</ul>
 		</div>
 	</div>
 	<div class="col-lg-3 col-sm-6 col-xs-12">
 		<div class="white-box">
 			<h3 class="box-title">Pedidos faturados</h3>
 			<ul class="country-state">
 				<li>
 					<h2>R$ 0,00</h2> <small>Aguardando emissão das notas</small>
 					<div class="pull-right">10% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:10%;"> <span class="sr-only">10% realizado</span></div>
 					</div>
 				</li>
 			</ul>
 		</div>
 	</div>
 	<div class="col-lg-3 col-sm-6 col-xs-12">
 		<div class="white-box">
 			<h3 class="box-title">Contas a receber</h3>
 			<ul class="country-state">
 				<li>
 					<h2>R$ <?php echo number_format($incomes['total'], 2, ',', '.'); ?></h2> <small>Aguardando recebimento</small>
 					<div class="pull-right"><?php echo number_format($porcIn, 2, ',', '.'); ?>% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $porcIn ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $porcIn ?>%;"> <span class="sr-only"><?php echo $porcIn ?>% realizado</span></div>
 					</div>
 				</li>
 			</ul>
 		</div>
 	</div>
 	<div class="col-lg-3 col-sm-6 col-xs-12">
 		<div class="white-box">
 			<h3 class="box-title">Contas a pagar</h3>
 			<ul class="country-state">
 				<li>
 					<h2>R$ <?php echo number_format($totPend, 2, ',', '.'); ?></h2> <small>Aguardando pagamento</small>
 					<div class="pull-right"><?php echo number_format($porcPendOut, 2, ',', '.'); ?>% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $porcPendOut ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $porcPendOut ?>%;"> <span class="sr-only"><?php echo $porcPendOut ?>% realizado</span></div>
 					</div>
 				</li>
 			</ul>
 		</div>
 	</div>

 </div>
 <!-- /.row -->
 <!-- ============================================================== -->
 <!-- Extra-component -->
 <!-- ============================================================== -->
 <div class="row">
 	<div class="col-lg-3 col-md-6">
 		<div class="white-box">
 			<h3 class="box-title">Workflow</h3>
 			<ul class="country-state  p-t-20">
 				<li>
 					<h2>R$ <?php echo isset($outcomes[15]) ? number_format($outcomes[15]['total'], 2, ',', '.') : '0,00' ?></h2> <small>Pendente</small> <!-- status_id = 15 -->
 					<div class="pull-right"><?php echo isset($outcomes[15]) ? number_format($outcomes[15]['porc'], 2, ',', '.') : '0' ?>% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo isset($outcomes[15]) ? $outcomes[15]['porc'] : '0' ?>%;"> <span class="sr-only"><?php echo isset($outcomes[15]) ? $outcomes[15]['porc'] : '0' ?>% Complete</span></div>
 					</div>
 				</li>
 				<li>
	 				<h2>R$ <?php echo isset($outcomes[46]) ? number_format($outcomes[46]['total'], 2, ',', '.') : '0,00' ?></h2> <small>Reprogramar</small> <!-- status_id = 18 -->
	 				<div class="pull-right"><?php echo isset($outcomes[46]) ? number_format($outcomes[46]['porc'], 2, ',', '.') : '0' ?>% <!-- <i class="fa fa-level-down-alt text-danger"></i> --></div>
	 				<div class="progress">
	 					<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo isset($outcomes[46]) ? number_format($outcomes[46]['porc'], 2, ',', '.') : '0' ?>%;"> <span class="sr-only"><?php echo isset($outcomes[46]) ? number_format($outcomes[46]['porc'], 2, ',', '.') : '0' ?>% Complete</span></div>
	 				</div>
	 			</li>
 				<li>
 					<h2>R$ <?php echo isset($outcomes[16]) ? number_format($outcomes[16]['total'], 2, ',', '.') : '0,00' ?></h2> <small>Programado</small> <!-- status_id = 16 -->
 					<div class="pull-right"><?php echo isset($outcomes[16]) ? number_format($outcomes[16]['porc'], 2, ',', '.') : '0' ?>% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 					<div class="progress">
 						<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo isset($outcomes[16]) ? number_format($outcomes[16]['porc'], 2, ',', '.') : '0' ?>%;"> <span class="sr-only"><?php echo isset($outcomes[16]) ? number_format($outcomes[16]['porc'], 2, ',', '.') : '0' ?>% Complete</span></div>
 					</div>
 				</li>
 				<h2>R$ <?php echo isset($outcomes[17]) ? number_format($outcomes[17]['total'], 2, ',', '.') : '0,00' ?></h2> <small>Pago</small> <!-- status_id = 17 -->
 				<div class="pull-right"><?php echo isset($outcomes[17]) ? number_format($outcomes[17]['porc'], 2, ',', '.') : '0' ?>% <!-- <i class="fa fa-level-up-alt text-success"></i> --></div>
 				<div class="progress">
 					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo isset($outcomes[17]) ? number_format($outcomes[17]['porc'], 2, ',', '.') : '0' ?>%;"> <span class="sr-only"><?php echo isset($outcomes[17]) ? number_format($outcomes[17]['porc'], 2, ',', '.') : '0' ?>% Complete</span></div>
 				</div>
 			</li>
 			
 			</ul>
 		</div>
 	</div>
 	<div class="col-md-9">
 		<div class="panel">
 			<div class="panel-heading">AGUARDANDO APROVAÇÃO DE PAGAMENTO</div>
 			<div class="table-responsive">
 				<table class="table table-hover manage-u-table">
 					<thead>
 						<tr>
 							<th class="text-center">NOME</th>
 							<th>BANCO</th>
 							<th>DATA</th>
 							<th>VALOR</th>
 							<th>AÇÕES</th>
 						</tr>
 					</thead>
 					<tbody>
 						<?php if ($data){ ?>
 							<?php for ($i=0; $i < count($data); $i++) { ?>
 								<?php 
 								$conta = $data[$i]['ViewBankAccount']['name'];
 								$displayConta = explode(' - ', $conta);
 								$displayConta[1] = str_replace('Ag', '', $displayConta[1]);
 								$displayConta[2] = str_replace(':', ' :', $displayConta[2]);
 								?>
 								<tr>
 									<td class="text-center" style="vertical-align: middle">
 										<span class="font-medium"><?php echo $data[$i]['Outcome']['name'] ?></span>
 										<?php echo !empty($data[$i]['Customer']['clienteNomeFantasia']) ? '<br/><span class="text-muted">'.$data[$i]['Customer']['clienteNomeFantasia'].' </span>' : ""; ?>
 									</td>
 									<td><?php echo $displayConta[0]." ".$displayConta[1] ?><br/><span class="text-muted"><?php echo $displayConta[2] ?></span></td>
 									<td>Vencimeto: <?php echo $data[$i]['Outcome']['data_vencimento'] ?><br/><span class="text-muted">Competência: <?php echo $data[$i]['Outcome']['data_competencia'] ?></span></td>
 									<td>Liquido: R$ <?php echo $data[$i]['Outcome']['valor_liquido'] ?><br/><span class="text-muted">Bruto: R$ <?php echo $data[$i]['Outcome']['valor_bruto'] ?></span></td>
 									<td>
 										<form action="<?php echo $this->base.'/outcomes/change_status/'.$data[$i]['Outcome']['id'].'/P' ?>" method="post">
 											<select class="form-control js_status" name="status_id">
 												<option value="">Selecione</option>
 												<?php 
	 												foreach ($status as $status_id => $status_nome) {
	 													$selected = "";
	 													if($status_id == CakeSession::read('Busca.Outcome.get.t')){
	 														$selected = "selected";
	 													}
	 													echo '<option value="'.$status_id.'" '.$selected.'>'.$status_nome.'</option>';
	 												}
 												?>
 											</select>
 										</form>
 									</td>
 								</tr>
 							<?php } ?>
 						<?php } else { ?>
 							<tr>
 								<td colspan="5">Nenhum registro encontrado</td>
 							</tr>
 						<?php } ?>
 					</tbody>
 				</table>
 			</div>
 		</div>
 	</div>
</div>
 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->

<script>
	$(document).ready(function(){
		$('.js_status').on('change', function(){
			$(this).parent().submit();
		})
	})
</script>