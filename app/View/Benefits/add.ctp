<script type="text/javascript">
    $(document).ready(function(){
        $("#BenefitLastFareUpdate").datepicker({
            language: "pt-BR",
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('#BenefitUnitPrice').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Benefit', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Código</label>
                <?php echo $this->Form->input('code', ["placeholder" => "Código", "required" => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Nome", "required" => true,  "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Fornecedor</label>
                <?php echo $this->Form->input('supplier_id', array("id" => "supplier_id", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true")); ?>
            </div>

        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Tipo</label>
                <?php echo $this->Form->input('benefit_type_id', array("id" => "benefit_type_id", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $benefit_types)); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Valor Unitário</label>
                <?php echo $this->Form->input('unit_price', ['type' => 'text', "placeholder" => "Nome", "required" => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Prazo Recarga</label>
                <?php echo $this->Form->input('time_to_recharge', ["placeholder" => "Nome", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Prazo Cartão Novo</label>
                <?php echo $this->Form->input('time_card', ["placeholder" => "Nome", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Data Atualizacão Tarifa</label>
                <?php echo $this->Form->input('last_fare_update', ['type' => 'text', "placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Cidade</label>
                <?php echo $this->Form->input('city', array("id" => "cidade", "placeholder" => "Cidade", "required" => true, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Estado</label>
                <?php echo $this->Form->input('state', array("id" => "estado", "placeholder" => "Estado", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true")); ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base . '/benefits' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
