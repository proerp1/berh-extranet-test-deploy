<div class="page page-profile">
	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-cog"></i>
			</span>
			<h3><?php echo $action; ?></h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<?php
		$url = $this->base.'/customers/negativacoes';
		echo $this->element("abas_customers", array('id' => $this->request->data['CadastroPefin']['customer_id'], 'url' => $url));
	?>
	
	<section class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->Form->create('CadastroPefin', array("id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post")); ?>
				<input type="hidden" name="data[CadastroPefin][customer_id]" value="<?php echo $this->request->data['CadastroPefin']['customer_id'] ?>">
				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label"></label>
					<div class="col-sm-9">
						<p><strong>Nome: </strong><?php echo $this->request->data['CadastroPefin']['nome']; ?></p>
						<p><strong>Natureza de Operação: </strong><?php echo $this->request->data['NaturezaOperacao']['nome']; ?></p>
						<p><strong>Documento: </strong><?php echo $this->request->data['CadastroPefin']['documento']; ?></p>
						<p><strong>Valor: </strong><?php echo $this->request->data['CadastroPefin']['valor'] ?></p>
						<p><strong>Data de Cadastro: </strong><?php echo date('d/m/Y H:i:s', strtotime($this->request->data['CadastroPefin']['created'])); ?></p>
					</div>
				</div>

				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Motivo da baixa</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('motivo_baixa_id', array("div" => false, "label" => false, "required" => true, "class" => "form-control", "empty" => "Selecione"));  ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-9">
						<a href="<?php echo $this->base.'/customers/negativacoes/'.$this->request->data['CadastroPefin']['customer_id']; ?>" class="btn btn-default">Voltar</a>
						<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Baixar</button>
					</div>
				</div>
			</form>
		</div> <!-- /panel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->