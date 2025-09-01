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

        // Show/hide fields based on supplier's transfer_fee_type
        var supplierFeeType = <?php echo json_encode($supplier['Supplier']['transfer_fee_type'] ?? 2); ?>;
        
        if (supplierFeeType == 1) { // Fixed value
            $('#percentage_field').hide();
            $('#fixed_field').show();
            $('#SupplierVolumeTierPercentualRepasse').prop('required', false);
            $('#SupplierVolumeTierValorFixo').prop('required', true);
        } else { // Percentage (2 or default)
            $('#percentage_field').show();
            $('#fixed_field').hide();
            $('#SupplierVolumeTierValorFixo').prop('required', false);
            $('#SupplierVolumeTierPercentualRepasse').prop('required', true);
        }

        // Validação básica no frontend
        $('#js-form-submit').on('submit', function(e) {
            const deQtd = parseInt($('#SupplierVolumeTierDeQtd').val());
            const ateQtd = parseInt($('#SupplierVolumeTierAteQtd').val());
            
            if (deQtd >= ateQtd) {
                e.preventDefault();
                alert('A quantidade final deve ser maior que a quantidade inicial.');
                return false;
            }

            // Validate based on supplier's fee type
            if (supplierFeeType == 1 && !$('#SupplierVolumeTierValorFixo').val()) {
                e.preventDefault();
                alert('Por favor, informe o valor fixo.');
                return false;
            }
            
            if (supplierFeeType == 2 && !$('#SupplierVolumeTierPercentualRepasse').val()) {
                e.preventDefault();
                alert('Por favor, informe o percentual de repasse.');
                return false;
            }
        });
    });
</script>