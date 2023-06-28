<script type="text/javascript">
	$(document).ready(function(){
		$("form").on("submit", function(){
      var $el = $(".js-importar");

      $el.button('loading');
    });
	})
</script>
<div class="page page-profile">

	<?php
		$url = $this->here;
		echo $this->element("abas_billings", array('id' => $id, 'url' => $url));
	?>

	<section class="panel panel-default">
		<div class="panel-body">
			<form action="#" class="form-horizontal col-md-12">
				<input type="hidden" name="data[Pefin][billing_id]" value="<?php echo $id ?>">
				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Registros para processar: <?php echo $qtde_processar ?></label>
					<div class="col-sm-2">
						<a href="<?php echo $this->base.'/billings/processar_meproteja/'.$id; ?>" class="btn btn-primary">Processar registros</a>
					</div>
				</div>
			</form>
		</div>
	</section>

	<section class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-4 col-lg-6">
				</div>

				<form class="col-md-8 col-lg-6" action="<?php echo $this->Html->url(array( "controller" => "billings", "action" => "berh")).'/'.$id; ?>" role="form" id="busca">
					<div class="row">
						<div class="col-md-4">
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
						<th class="default">Produto</th>
						<th>Código Associado</th>
						<th>Cliente</th>
						<th>Dias</th>
						<th>Valor R$</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td><?php echo $data[$i]['Product']['name'] ?></td>
								<td><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
								<td><?php echo $data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'] ?></td>
								<td><?php echo $data[$i]['ClienteMeProteja']['clienteMeProtejaDias'] ?></td>
								<td>R$ <?php echo number_format($data[$i]['ClienteMeProteja']['clienteMeProtejaValor'],2,',','.') ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="6">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		
			<?php echo $this->element("pagination"); ?>
		
		</div> <!-- /panel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->