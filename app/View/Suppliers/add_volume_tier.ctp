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
            <div class="mb-7 col-md-4">
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

            <div class="mb-7 col-md-4">
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

            <div class="mb-7 col-md-4">
                <label class="fw-semibold fs-6 mb-2 required">% Repasse</label>
                <?php echo $this->Form->input('percentual_repasse', [
                    "placeholder" => "0,00", 
                    "class" => "form-control mb-3 mb-lg-0 money_exchange",
                    "type" => "text"
                ]); ?>
                <div class="form-text">Percentual aplicado sobre o valor</div>
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

        // Validação básica no frontend
        $('#js-form-submit').on('submit', function(e) {
            const deQtd = parseInt($('#SupplierVolumeTierDeQtd').val());
            const ateQtd = parseInt($('#SupplierVolumeTierAteQtd').val());
            
            if (deQtd >= ateQtd) {
                e.preventDefault();
                alert('A quantidade final deve ser maior que a quantidade inicial.');
                return false;
            }
        });
    });
</script>