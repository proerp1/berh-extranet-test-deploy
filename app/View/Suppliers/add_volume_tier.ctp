<?php
if (isset($id)) {
    $url = $this->here;
    echo $this->element("abas_suppliers", array('id' => $id, 'url' => $url));
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-3 mb-1">
                <?php echo isset($tier_id) ? 'Editar' : 'Nova'; ?> Faixa de Volume
            </span>
            <span class="text-muted mt-1 fw-bold fs-7"><?php echo h($supplier['Supplier']['nome_fantasia']); ?></span>
        </h3>
    </div>

    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('SupplierVolumeTier', ["id" => "js-form-submit", "url" => "/suppliers/" . $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        
        <div class="row">
            <div class="mb-7 col-md-3">
                <label class="fw-semibold fs-6 mb-2 required">De (Qtd)</label>
                <?php echo $this->Form->input('de_qtd', [
                    "placeholder" => "Quantidade inicial", 
                    "class" => "form-control mb-3 mb-lg-0",
                    "type" => "number",
                    "min" => "1",
                    "max" => "999999"
                ]); ?>
                <div class="form-text">Quantidade mínima para esta faixa</div>
            </div>

            <div class="mb-7 col-md-3">
                <label class="fw-semibold fs-6 mb-2 required">Até (Qtd)</label>
                <?php echo $this->Form->input('ate_qtd', [
                    "placeholder" => "Quantidade final", 
                    "class" => "form-control mb-3 mb-lg-0",
                    "type" => "number",
                    "min" => "1",
                    "max" => "999999"
                ]); ?>
                <div class="form-text">Quantidade máxima para esta faixa</div>
            </div>

            <div class="mb-7 col-md-3">
                <label class="fw-semibold fs-6 mb-2 required">Tipo de Taxa</label>
                <?php echo $this->Form->input('fee_type', [
                    "id" => "fee_type",
                    "options" => ['percentage' => 'Percentual', 'fixed' => 'Valor Fixo'], 
                    "class" => "form-select mb-3 mb-lg-0", 
                    "data-control" => "select2", 
                    "empty" => "Selecione", 
                    "type" => "select"
                ]); ?>
                <div class="form-text">Tipo de cobrança para esta faixa</div>
            </div>

            <div class="mb-7 col-md-3" id="percentage_field">
                <label class="fw-semibold fs-6 mb-2">% Repasse</label>
                <?php echo $this->Form->input('percentual_repasse', [
                    "placeholder" => "0,00", 
                    "class" => "form-control mb-3 mb-lg-0 money_exchange",
                    "type" => "text"
                ]); ?>
                <div class="form-text">Percentual aplicado sobre o valor</div>
            </div>

            <div class="mb-7 col-md-3" id="fixed_field" style="display: none;">
                <label class="fw-semibold fs-6 mb-2">Valor Fixo</label>
                <?php echo $this->Form->input('valor_fixo', [
                    "placeholder" => "0,00", 
                    "class" => "form-control mb-3 mb-lg-0 money_exchange",
                    "type" => "text"
                ]); ?>
                <div class="form-text">Valor fixo a ser cobrado</div>
            </div>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/suppliers/volume_tiers/' . $id; ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar Faixa</button>
            </div>
        </div>
        
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Aplicar máscara de percentual
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '',
            precision: 2,
            suffix: '',
            allowZero: true,
            allowNegative: false
        });

        // Handle fee type change
        $('#fee_type').on('change', function() {
            const feeType = $(this).val();
            
            if (feeType === 'percentage') {
                $('#percentage_field').show();
                $('#fixed_field').hide();
                $('#SupplierVolumeTierValorFixo').prop('required', false);
                $('#SupplierVolumeTierPercentualRepasse').prop('required', true);
            } else if (feeType === 'fixed') {
                $('#percentage_field').hide();
                $('#fixed_field').show();
                $('#SupplierVolumeTierPercentualRepasse').prop('required', false);
                $('#SupplierVolumeTierValorFixo').prop('required', true);
            } else {
                $('#percentage_field').show();
                $('#fixed_field').hide();
                $('#SupplierVolumeTierPercentualRepasse').prop('required', false);
                $('#SupplierVolumeTierValorFixo').prop('required', false);
            }
        });

        // Initialize fee type display
        $('#fee_type').trigger('change');

        // Validação básica no frontend
        $('#js-form-submit').on('submit', function(e) {
            const deQtd = parseInt($('#SupplierVolumeTierDeQtd').val());
            const ateQtd = parseInt($('#SupplierVolumeTierAteQtd').val());
            const feeType = $('#fee_type').val();
            
            if (deQtd >= ateQtd) {
                e.preventDefault();
                alert('A quantidade final deve ser maior que a quantidade inicial.');
                return false;
            }

            if (!feeType) {
                e.preventDefault();
                alert('Por favor, selecione o tipo de taxa.');
                return false;
            }

            if (feeType === 'percentage' && !$('#SupplierVolumeTierPercentualRepasse').val()) {
                e.preventDefault();
                alert('Por favor, informe o percentual de repasse.');
                return false;
            }

            if (feeType === 'fixed' && !$('#SupplierVolumeTierValorFixo').val()) {
                e.preventDefault();
                alert('Por favor, informe o valor fixo.');
                return false;
            }
        });
    });
</script>