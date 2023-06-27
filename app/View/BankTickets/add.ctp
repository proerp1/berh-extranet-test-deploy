<script type="text/javascript">
	$(document).ready(function(){
		$('.money_exchange').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 2
		});
		$('.money_exchange_3').maskMoney({
			decimal: ',',
			thousands: '.',
			precision: 3
		});
	});
</script>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base.'/bank_accounts/edit/'.$idBank; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base.'/bank_tickets/tickets/'.$idBank; ?>">Boletos</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('BankTicket', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
			<input type="hidden" name="query_string" value="<?php echo $_SERVER['QUERY_STRING'] ?>">
			<input type="hidden" name="idBank" value="<?php echo $idBank ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Carteira</label>
                <?php echo $this->Form->input('carteira', ["placeholder" => "Carteira", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Codigo Cedente</label>
                <?php echo $this->Form->input('codigo_cedente', ["placeholder" => "Codigo Cedente", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cobrança Taxa Bancaria</label>
                <?php echo $this->Form->input('cobranca_taxa_bancaria', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", 'empty' => 'Selecione', 'options' => ['Sim' => 'Sim', 'Não' => 'Não']]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Valor taxa bancaria</label>
                <?php echo $this->Form->input('taxa_bancaria', ["placeholder" => "Valor taxa bancaria", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Multa Boleto</label>
                <?php echo $this->Form->input('multa', ["placeholder" => "Multa Boleto", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Juros Boleto por dia</label>
                <?php echo $this->Form->input('juros', ["placeholder" => "Juros Boleto por dia", "class" => "form-control mb-3 mb-lg-0 money_exchange_3"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Instrução do Boleto 1</label>
                <?php echo $this->Form->input('instrucao_boleto_1', ["placeholder" => "Instrução do Boleto 1", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Instrução do Boleto 2</label>
                <?php echo $this->Form->input('instrucao_boleto_2', ["placeholder" => "Instrução do Boleto 2", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Instrução do Boleto 3</label>
                <?php echo $this->Form->input('instrucao_boleto_3', ["placeholder" => "Instrução do Boleto 3", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Instrução do Boleto 4</label>
                <?php echo $this->Form->input('instrucao_boleto_4', ["placeholder" => "Instrução do Boleto 4", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Informativo do Boleto</label>
                <?php echo $this->Form->input('informativo_boleto', ["placeholder" => "Informativo do Boleto", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação do Boleto</label>
                <?php echo $this->Form->input('observacao', ["placeholder" => "Observação do Boleto", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/bank_tickets/tickets/'.$idBank ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
    	</form>
    </div>
</div>