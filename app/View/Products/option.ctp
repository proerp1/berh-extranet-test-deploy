<div class="page page-profile">

  <div class="panel panel-profile">
    <div class="panel-heading bg-dark clearfix mini-box">
      <span class="box-icon bg-success">
        <i class="fa fa-list"></i>
      </span>
      <h3>Opções</h3>
    </div>
  </div>

  <?php echo $this->Session->flash(); ?>

  <ul class="nav nav-tabs">
    <li><a href="<?php echo $this->base.'/products/edit/'.$id; ?>">Dados</a></li>
    <li><a href="<?php echo $this->base.'/products/features/'.$id; ?>">Features</a></li>
    <li><a href="<?php echo $this->base.'/products/answer/'.$id; ?>">Respostas</a></li>
    <li><a href="<?php echo $this->base.'/products/answer_item/'.$id.'/'.$answer_id; ?>">Itens da Resposta</a></li>
    <li class="active"><a href="<?php echo $this->base.'/products/option/'.$id.'/'.$answer_id.'/'.$answer_item_id; ?>">Opções</a></li>
  </ul>

	<section class="panel panel-default">
   	<div class="panel-heading">
			<div class="row">
				<div class="col-md-4 col-lg-6">
					<?php $url_novo = $this->base.'/products/add_option/'.$id.'/'.$answer_id.'/'.$answer_item_id; ?>						
					<div class="form-group">
						<a href="<?php echo $url_novo;?>" class="btn btn-primary">
							<i class="glyphicon glyphicon-file"></i>
							Novo
						</a>
					</div>
				</div>

				<form class="col-md-8 col-lg-6" action="<?php echo $this->Html->url(array( "controller" => "products", "action" => "option")); ?>/<?php echo $id; ?>/<?php echo $answer_id; ?>/<?php echo $answer_item_id; ?>" role="form" id="busca">
					<div class="row">
						<div class="col-md-4">
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
			<?php echo $this->element("table", array("url_novo" => $url_novo)); ?>
			  <thead>
			    <tr>
			      <th class="default">Nome</th>
			      <th>Código</th>
			      <th>Ações</th>
			    </tr>
			  </thead>
			  <tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td><?php echo $data[$i]["ItemOption"]["name"]; ?></td>
								<td><?php echo $data[$i]["ItemOption"]["codigo"]; ?></td>
								<td>
									<a href="<?php echo $this->base; ?>/products/edit_option/<?php echo $id ?>/<?php echo $answer_id ?>/<?php echo $answer_item_id; ?>/<?php echo $data[$i]["ItemOption"]["id"]; ?>" class="btn btn-info btn-xs">
										<i class="fa fa-edit"></i>
									</a>
									<a href="javascript:" onclick="verConfirm('<?php echo $this->base; ?>/products/delete_option/<?php echo $id ?>/<?php echo $answer_id ?>/<?php echo $answer_item_id; ?>/<?php echo $data[$i]["ItemOption"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-xs">
										<i class="fa fa-trash-o"></i>
									</a>							
								</td>
							</tr>
						<?php } ?>
					<?php } else{ ?>
						<tr>
							<td colspan="8">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
			  </tbody>
			</table>

			<?php echo $this->element("pagination"); ?>

    </div> <!-- /painel-body -->
  </section> <!-- /panel-default -->
</div> <!-- /page-profile -->