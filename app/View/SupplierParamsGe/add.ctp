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
    $form_action = isset($paramsGeId) ? 'edit' : 'add';
    echo $this->element('abas_suppliers', ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7">
        <?php echo $this->Form->create('SupplierParamsGe', ['id' => 'js-form-submit', 'url' => ['controller' => 'supplier_params_ge', 'action' => $form_action, $id, isset($paramsGeId) ? $paramsGeId : ''], 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Status</label>
                    <?php echo $this->Form->input('status_id', ['class' => 'form-select mb-3 mb-lg-0', 'data-control' => 'select2', 'empty' => 'Selecione']); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Cliente</label>
                    <?php echo $this->Form->input('customer_id', ['class' => 'form-select mb-3 mb-lg-0', 'data-control' => 'select2', 'empty' => 'Todos']); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Passagens</label>
                    <?php echo $this->Form->input('tickets', ['placeholder' => 'Passagens', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Compra mínima</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <?php echo $this->Form->input('minimum_purchase', ["type" => "text", "placeholder" => "Compra mínima", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base.'/supplier_params_ge/index/'.$id.'/'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>

        </form>
    </div>
</div>
