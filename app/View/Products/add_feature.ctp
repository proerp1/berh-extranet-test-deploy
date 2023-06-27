<?php echo $this->Html->script('jquery-maskmoney'); ?>

<?php echo $this->Html->script("html_editor/summernote"); ?>
<?php echo $this->Html->script("html_editor/summernote-pt-BR"); ?>
<?php echo $this->Html->css("html_editor/summernote"); ?>

<script>
	$(document).ready(function(){
		$('#FeatureDescricao').summernote({
      lang: 'pt-BR',
      height: 300,
      toolbar : [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize', 'fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['group', ['link', 'hr' ]],
        ['misc', [ 'codeview', 'undo', 'redo' ]],
        ['help', [ 'help' ]],
      ]
    });

		$('.money_exchange').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 2
		});
	});
</script>

<div class="page page-profile">
	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-list"></i>
			</span>
			<h3><?php echo $action; ?></h3>
		</div>
	</div>

	<?php echo $this->Session->flash(); ?>

	<ul class="nav nav-tabs">
		<li><a href="<?php echo $this->base.'/products/edit/'.$id; ?>">Dados</a></li>
		<li class="active"><a href="<?php echo $this->base.'/products/features/'.$id; ?>">Features</a></li>    
		<li><a href="<?php echo $this->base.'/products/answer/'.$id; ?>">Respostas</a></li>
	</ul>

	<section class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->Form->create('Feature', array("class" => "form-horizontal col-md-12", "action" => "/".$form_action."/".$id, "method" => "post", "id" => "js-form-submit", 'enctype' => 'multipart/form-data')); ?>
				<input type="hidden" name="data[Feature][product_id]" value="<?php echo $id; ?>">
				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Nome</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('name', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
					</div>
				</div>
				
				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Descrição</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('descricao', array("div" => false, "label" => false, "placeholder" => "Descrição", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Data de ativação</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input type="text" name="data[Feature][data_ativacao]" id="data_ativacao" class="form-control datepicker" value="<?php echo isset($this->request->data["Feature"]) ? $this->request->data["Feature"]["data_ativacao"] : ""; ?>" placeholder="Data">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Valor da Feature</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">R$</span>
							<?php echo $this->Form->input('valor', array("type" => "text", "div" => false, "label" => false, "placeholder" => "Valor da Feature", "class" => "form-control money_exchange"));  ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Valor Mínimo</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">R$</span>
							<?php echo $this->Form->input('valor_minimo', array("type" => "text", "div" => false, "label" => false, "placeholder" => "Valor Mínimo", "class" => "form-control money_exchange"));  ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Cor</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">#</span>
							<?php echo $this->Form->input('color', array("div" => false, "label" => false, "placeholder" => "Cor", "class" => "form-control"));  ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="inputNome" class="col-sm-2 control-label">Status</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('status_id', array("div" => false, "label" => false, "placeholder" => "Status", "class" => "form-control", "empty" => "Selecione"));  ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-9">
						<a href="<?php echo $this->base; ?>/products/features/<?php echo $id; ?>" class="btn btn-default">Voltar</a>
						<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
					</div>
				</div>
			</form>
		</div> <!-- /panel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->