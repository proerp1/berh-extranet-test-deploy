<?php
$url = $this->base . '/customers_users/bank_info';
echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
if ($user_id) {
    echo $this->element('abas_customer_users', ['id' => $id, 'url' => $url]);
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUserBankAccount', ['id' => 'js-form-submit', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <?php if (!isset($this->request->data['CustomerUserBankAccount']['id'])) { ?>
            <input type="hidden" name="data[CustomerUserBankAccount][customer_id]" value="<?php echo $id; ?>">
            <input type="hidden" name="data[CustomerUserBankAccount][customer_user_id]" value="<?php echo $user_id; ?>">
        <?php } ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "empty" => "Selecione", 'options' => $statuses]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Tipo Conta</label>
                <?php echo $this->Form->input('account_type_id', array("id" => "tipo_conta", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $bank_account_type)); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Banco</label>
                <?php echo $this->Form->input('bank_code_id', array("id" => "bank_name", "placeholder" => "Nome Banco", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $banks));  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Agência</label>
                <div class="row">
                    <div class="col">
                        <?php echo $this->Form->input('branch_number', array("id" => "agencia", "placeholder" => "Agência", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                    <div class="col">
                        <?php echo $this->Form->input('branch_digit', array("id" => "agencia_digito", "placeholder" => "Dígito", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                </div>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Conta</label>
                <div class="row">
                    <div class="col">
                        <?php echo $this->Form->input('acc_number', array("id" => "conta", "placeholder" => "Conta", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                    <div class="col">
                        <?php echo $this->Form->input('acc_digit', array("id" => "conta_digito", "placeholder" => "Dígito", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <?php
                    $options = [
                        '' => 'Selecione',
                        'cnpj' => 'CNPJ',
                        'cpf' => 'CPF',
                        'email' => 'e-mail',
                        'celular' => 'celular',
                        'chave' => 'chave'
                    ];
                    ?>
                    <label class="fw-semibold fs-6 mb-2">Tipo Chave</label>
                    <?php echo $this->Form->input('pix_type', ['placeholder' => 'Tipo PIX', "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $options]); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Chave PIX</label>
                    <?php echo $this->Form->input('pix_id', ['type' => 'text', 'placeholder' => 'PIX', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                </div>
            </div>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customer_users/bank_info/' . $id . '/' . $user_id . '?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>