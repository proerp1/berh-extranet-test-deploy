<?php
$url = $this->base . '/customers_users/bank_info';
echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
if($user_id){
    echo $this->element('abas_customer_users', ['id' => $id, 'url' => $url]);
}
?>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.money_field').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
    </script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUserItinerary', ['id' => 'js-form-submit', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <?php if(!isset($this->request->data['CustomerUserItinerary']['id'])){ ?>
            <input type="hidden" name="data[CustomerUserItinerary][customer_id]" value="<?php echo $id; ?>">
            <input type="hidden" name="data[CustomerUserItinerary][customer_user_id]" value="<?php echo $user_id; ?>">
        <?php } ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Benefício</label>
                <?php echo $this->Form->input('benefit_id', array("id" => "benefit_id", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true")); ?>
            </div>
            
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                <?php echo $this->Form->input('working_days', array("id" => "working_days",  "placeholder" => "Dias Úteis", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Valor Unitário</label>
                <?php echo $this->Form->input('unit_price', array("id" => "unit_price", 'type' => 'text', "placeholder" => "Valor Unitário", "required" => false, "class" => "form-control mb-3 mb-lg-0 money_field"));  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Quantidade</label>
                <?php echo $this->Form->input('quantity', array("id" => "agencia", "placeholder" => "Quantidade", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Valor por dia</label>
                <?php echo $this->Form->input('price_per_day', array("id" => "price_per_day", 'type' => 'text', "placeholder" => "Conta", "required" => false, "class" => "form-control mb-3 mb-lg-0 money_field"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Total</label>
                <?php echo $this->Form->input('total', array("id" => "total", 'type' => 'text', "placeholder" => "Conta", "required" => false, "class" => "form-control mb-3 mb-lg-0 money_field"));  ?>
            </div>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customer_users/itineraries/' . $id . '/'.$user_id.'?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>