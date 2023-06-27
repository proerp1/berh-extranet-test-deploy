<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>

<?php
    if (isset($id)) {
        echo $this->element("abas_plans", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Plan', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tipo</label>
                <?php echo $this->Form->input('type', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['1' => 'Quantidade', '2' => 'Consumo']]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('description', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('value', ["type" => "text", "placeholder" => "Valor", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label class="form-label">Comissão Vendedor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('commission', ["type" => "text", "placeholder" => "Comissão Vendedor", "class" => "form-control mb-3 mb-lg-0 money_exchange"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Quantidade</label>
                <?php echo $this->Form->input('quantity', ["placeholder" => "Quantidade", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Total gratuidade BeRH</label>
                <?php echo $this->Form->input('total_gratuity', ["placeholder" => "Total gratuidade BeRH", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/plans' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>