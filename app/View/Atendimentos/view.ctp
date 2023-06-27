<script>
	$(document).ready(function() {
		$('#AtendimentoCustomerId').select2();
		$('#AtendimentoDataAtendimento').mask('99/99/9999 99:99');
	});
</script>
<?php 
	$disabled = true;
	if ($form_action == 'add') {
		$disabled = false;
	}
?>
<div class="card mb-5 mb-xl-8">
	<div class="card-body pt-7 py-3">
		<?php echo $this->Form->create('Atendimento', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Status</label>
				<?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"]);?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Cliente</label>
				<?php echo $this->Form->input('customer_id', ['disabled' => $disabled, "data-control" => "select2", "class" => "form-select ".($disabled ? 'form-select-solid' : '')." mb-3 mb-lg-0", "empty" => "Selecione"]); ?>
			</div>
			
			<?php if ($disabled): ?>
				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Nome Atendente</label>
					<?php echo $this->Form->input('name_atendente', array('disabled' => $disabled, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Nome Atendente"));  ?>
				</div>

				<div class="mb-7">
					<label class="fw-semibold fs-6 mb-2">Data Atendimento</label>
					<?php echo $this->Form->input('data_atendimento', array('disabled' => $disabled, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Data Atendimento", 'type' => 'text'));  ?>
				</div>
			<?php endif ?>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Departamento</label>
				<?php echo $this->Form->input('department_id', array('disabled' => $disabled, "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"));  ?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Assunto</label>
				<?php echo $this->Form->input('subject', array('disabled' => $disabled, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Assunto"));  ?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Mensagem</label>
				<?php echo $this->Form->input('message', array('disabled' => $disabled, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Mensagem"));  ?>
			</div>

			<div class="mb-7">
				<label class="fw-semibold fs-6 mb-2">Resposta</label>
				<?php echo $this->Form->input('answer', array("class" => "form-control mb-3 mb-lg-0", "placeholder" => "Resposta"));  ?>
			</div>

			<div class="mb-7">
				<div class="col-sm-offset-2 col-sm-9">
					<a href="<?php echo $this->base.'/atendimentos'; ?>" class="btn btn-light-dark">Voltar</a>
					<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
				</div>
			</div>
		</form>
	</div>
</div>