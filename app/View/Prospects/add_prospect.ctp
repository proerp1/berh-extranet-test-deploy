
<script type="text/javascript">
	$(document).ready(function() {
		$("#ProspectTelefone").mask("(99) 9999-9999");
		$("#ProspectCelular").focusout(function(){
			var phone, element;
			element = $(this);
			element.unmask();
			phone = element.val().replace(/\D/g, '');
			if(phone.length > 10) {
				element.mask("(99) 99999-999?9");
			} else {
				element.mask("(99) 9999-9999?9");
			}
		}).trigger('focusout');

	});
</script>
<div class="page page-profile">
	<div class="panel panel-profile">
		<div class="panel-heading bg-dark clearfix mini-box">
			<span class="box-icon bg-success">
				<i class="fa fa-cog"></i>
			</span>
			<h3>Cadastro de empresa</h3>
		</div>
	</div>
	
	<?php echo $this->Session->flash(); ?>

	<section class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->Form->create('Prospect', array("id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post")); ?>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Status</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('status_id', array("div" => false, "label" => false, "class" => "form-control", "empty" => "Selecione"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Empresa</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('empresa', array("div" => false, "label" => false, "placeholder" => "Empresa", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Contato</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('contato', array("div" => false, "label" => false, "placeholder" => "Contato", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Telefone</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('telefone', array("div" => false, "label" => false, "placeholder" => "Telefone", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Celular</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('celular', array("div" => false, "label" => false, "placeholder" => "Celular", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Email</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('email', array("div" => false, "label" => false, "placeholder" => "Email", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Cidade</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('cidade', array("div" => false, "label" => false, "placeholder" => "Cidade", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<label for="inputEmail" class="col-sm-2 control-label">Estado</label>
					<div class="col-sm-9">
						<?php echo $this->Form->input('estado', array("div" => false, "label" => false, "placeholder" => "Estado", "class" => "form-control"));  ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-9">
						<a href="<?php echo $this->base.'/prospects'; ?>" class="btn btn-default">Voltar</a>
						<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
					</div>
				</div>
			</form>
		</div> <!-- /panel-body -->
	</section> <!-- /panel-default -->
</div> <!-- /page-profile -->