<script type="text/javascript">
    function submitOptions(){
        var unit_price_changed = $('#BenefitUnitPriceChanged').val();

        if (unit_price_changed == 1) {
            $('#modal-confirm').modal('show');
        } else {
            $('#js-form-submit').unbind('submit').submit();
        }
    }
    $(document).ready(function() {
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

        // Check if the unit_price has changed
        $('#BenefitUnitPrice').on('keyup', function() {
            var unit_price = $(this).val();
            var unit_price_old = $(this).data('unit-price-old');
            // convert string to float
            unit_price = parseFloat(unit_price.replace('.', '').replace(',', '.'));
            unit_price_old = parseFloat(unit_price_old);

            if (unit_price != unit_price_old) {
                // Salva flag em campo hidden
                $('#BenefitUnitPriceChanged').val(1);
            }
        });

        $('#js-form-submit').on('submit', function(e) {
            e.preventDefault();

            submitOptions(); 
        });

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                submitOptions();
                return false;
            }
        });

        // Alterar somente benefício
        $('#alterar_beneficio').on('click', function() {
            $('#ShouldUpdateItinerary').val(0);
            $('#js-form-submit').unbind('submit').submit();
        });

        // Alterar itinerários
        $('#alterar_itinerarios').on('click', function() {
            $('#ShouldUpdateItinerary').val(1);
            $('#js-form-submit').unbind('submit').submit();
        });
    })
</script>

<?php
$oldUnitPrice = isset($this->request->data['Benefit']['unit_price_not_formated']) ? $this->request->data['Benefit']['unit_price_not_formated'] : '';
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Benefit', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" id="BenefitUnitPriceChanged" value="2">
        <input type="hidden" id="ShouldUpdateItinerary" name="ShouldUpdateItinerary" value="2">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Código</label>
                <?php echo $this->Form->input('code', ["placeholder" => "Código", "required" => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Nome</label>
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
                <label class="fw-semibold fs-6 mb-2 required">Valor Unitário</label>
                <?php echo $this->Form->input('unit_price', ['type' => 'text', "placeholder" => "Nome", "required" => true, "class" => "form-control mb-3 mb-lg-0", 'data-unit-price-old' => $oldUnitPrice]);  ?>
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


<!-- Modal para confirmação -->
<div class="modal fade" id="modal-confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Confirmação</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-lg-10 px-lg-10">
                <div class="fv-row mb-10">
                    <div class="fs-6 fw-bold">Deseja alterar o valor unitário para todos itenerários cadastrados nos beneficiarios?</div>
                </div>
            </div>

            <div class="modal-footer flex-right">
                <button type="button" class="btn btn-light" id="alterar_beneficio">Alterar Somente Benefício</button>
                <button type="button" class="btn btn-success" id="alterar_itinerarios">Alterar Itinerários</button>
            </div>
        </div>
    </div>
</div>