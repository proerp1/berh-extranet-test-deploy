<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>
    
<?php if ($form_action == 'edit'): ?>
    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo $this->here; ?>">Dados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $this->base.'/bank_tickets/tickets/'.$id; ?>">Boletos</a>
        </li>
    </ul>
<?php endif ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('BankAccount', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Banco</label>
                <?php echo $this->Form->input('bank_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('description', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Agência</label>
                <?php echo $this->Form->input('agency', ["placeholder" => "Agência", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número da conta</label>
                <?php echo $this->Form->input('account_number', ["placeholder" => "Número da conta", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Convênio</label>
                <?php echo $this->Form->input('convenio', ["placeholder" => "Convênio", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Saldo inicial</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('initial_balance', ["type" => "text", "placeholder" => "Saldo inicial", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Data início do saldo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('start_date', ["type" => "text", "placeholder" => "Data início do saldo", "class" => "form-control mb-3 mb-lg-0 datepicker"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Limite</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('limit', ["type" => "text", "placeholder" => "Limite", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação</label>
                <?php echo $this->Form->input('observation', ["placeholder" => "Observação", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/bank_accounts' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>