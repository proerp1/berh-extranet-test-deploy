<script>
$(document).ready(function() {
    $('#AtendimentoCustomerId').select2();
    $('#AtendimentoDataAtendimento').mask('99/99/9999 99:99');
    $('#AtendimentoDataFinalizacao').mask('99/99/9999 99:99');

    if ($('input[name="Atendimento[mostrar_cliente_option]"]:checked').val() == '1') {
        $('input[name="Atendimento[mostrar_cliente_option]"]').attr('disabled', true);
    }

    $('form#js-form-submit').on('submit', function() {
        var mostrar_cliente_value = $('input[name="Atendimento[mostrar_cliente_option]"]:checked').val();
        $('#AtendimentoMostrarCliente').val(mostrar_cliente_value);

        if (mostrar_cliente_value == '1') {
            $('input[name="Atendimento[mostrar_cliente_option]"]').attr('disabled', true);
        }
    });

    var initialStatus = $('#AtendimentoStatusId').val();
    if (initialStatus == 35) {
        $('#data-finalizacao-section').show();
    } else {
        $('#data-finalizacao-section').hide();
    }
});
</script>

<?php 
$disabled = true;
if ($form_action == 'add') {
    $disabled = false;
}
?>
<?php
$mostrar_cliente = isset($this->request->data['Atendimento']['mostrar_cliente']) ? $this->request->data['Atendimento']['mostrar_cliente'] : 0;
$status = isset($this->request->data['Atendimento']['status_id']) ? $this->request->data['Atendimento']['status_id'] : null;
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Atendimento', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
        <?php echo $this->Form->input('mostrar_cliente', ['type' => 'hidden', 'id' => 'AtendimentoMostrarCliente', 'value' => $mostrar_cliente]); ?>
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
                <?php echo $this->Form->input('name_atendente', array( "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Nome Atendente"));  ?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Data de Abertura</label>
                <?php echo $this->Form->input('data_atendimento', array( "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Data Atendimento", 'type' => 'text'));  ?>
            </div>
        <?php endif ?>

        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2">Departamento</label>
            <?php echo $this->Form->input('department_id', array( "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"));  ?>
        </div>

        <div class="mb-7">
            <label class="fw-semibold fs-6 mb-2">Assunto</label>
            <?php echo $this->Form->input('subject', array('disabled' => $disabled, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Assunto"));  ?>
        </div>

        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2">Arquivo</label>
            <div class="col-sm-5">
                <?php echo $this->Form->input('file_atendimento', array("div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o arquivo"));  ?>
                <?php if (isset($this->request->data["Atendimento"])): ?>
                    <br>
                    <a download href="<?php echo $this->webroot.'files/atendimento/file_atendimento/'.$this->request->data["Atendimento"]["id"].'/'.$this->request->data["Atendimento"]["file_atendimento"]; ?>"><?php echo $this->request->data["Atendimento"]["file_atendimento"] ?></a>
                <?php endif ?>
            </div>
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
            <label class="fw-semibold fs-6 mb-2">Mostrar Cliente?</label>
            <div class="form-check form-check-inline d-block mt-2">
                <input class="form-check-input" type="radio" name="Atendimento[mostrar_cliente_option]" id="mostrar_cliente_sim" value="1" <?php echo $mostrar_cliente == 1 ? 'checked' : ''; ?> <?php echo $mostrar_cliente == 1 ?>
                <label class="form-check-label" for="mostrar_cliente_sim">Sim</label>
            </div>
            <div class="form-check form-check-inline d-block mt-2">
                <input class="form-check-input" type="radio" name="Atendimento[mostrar_cliente_option]" id="mostrar_cliente_nao" value="0" <?php echo $mostrar_cliente == 0 ? 'checked' : ''; ?> <?php echo $mostrar_cliente == 1 ?>
                <label class="form-check-label" for="mostrar_cliente_nao">Não</label>
            </div>
        </div>

        <div id="data-finalizacao-section" class="mb-7" style="display: none;">
            <label class="fw-semibold fs-6 mb-2">Data Finalização</label>
            <?php echo $this->Form->input('data_finalizacao', array('disabled' => $status == 35, "class" => "form-control mb-3 mb-lg-0", "placeholder" => "Data Finalização", 'type' => 'text'));  ?>
        </div>

        <!-- Botões de ação -->
		<div class="mb-7">
			<div class="col-sm-offset-2 col-sm-9">
				<a href="<?php echo $this->base.'/atendimentos'; ?>" class="btn btn-light-dark">Voltar</a>

				<?php if ($status != 35):?>
					<button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
				<?php endif; ?>

			</div>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
