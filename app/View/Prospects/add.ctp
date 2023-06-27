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

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
    	<?php echo $this->Form->create('Prospect', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

    		<div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

    		<div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Revenda</label>
                <?php echo $this->Form->input('resale_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Empresa</label>
                <?php echo $this->Form->input('empresa', ["placeholder" => "Empresa", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Contato</label>
                <?php echo $this->Form->input('contato', ["placeholder" => "Contato", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                <?php echo $this->Form->input('telefone', ["placeholder" => "Telefone", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular</label>
                <?php echo $this->Form->input('celular', ["placeholder" => "Celular", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Email</label>
                <?php echo $this->Form->input('email', ["placeholder" => "Email", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cidade</label>
                <?php echo $this->Form->input('cidade', ["placeholder" => "Cidade", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

    		<div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Estado</label>
                <?php echo $this->Form->input('estado', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => ["AC" => "Acre", "AL" => "Alagoas", "AP" => "Amapá", "AM" => "Amazonas", "BA" => "Bahia", "CE" => "Ceará", "DF" => "Distrito Federal", "ES" => "Espirito Santo", "GO" => "Goiás", "MA" => "Maranhão", "MT" => "Mato Grosso", "MS" => "Mato Grosso do Sul", "MG" => "Minas Gerais", "PA" => "Pará", "PB" => "Paraiba", "PR" => "Paraná", "PE" => "Pernambuco", "PI" => "Piauí", "RJ" => "Rio de Janeiro", "RN" => "Rio Grande do Norte", "RS" => "Rio Grande do Sul", "RO" => "Rondônia", "RR" => "Roraima", "SC" => "Santa Catarina", "SP" => "São Paulo", "SE" => "Sergipe", "TO" => "Tocantins"]]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/prospects' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

    	</form>
	</div>
</div>