<?php
$url = $this->base . '/customers_users/bank_info';
echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
if($user_id){
    echo $this->element('abas_customer_users', ['id' => $id, 'url' => $url]);
}
?>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUserItinerary', ['id' => 'js-form-submit', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <?php if(!isset($this->request->data['CustomerUserItinerary']['id'])){ ?>
            <input type="hidden" name="data[CustomerUserItinerary][customer_id]" value="<?php echo $id; ?>">
            <input type="hidden" name="data[CustomerUserItinerary][customer_user_id]" value="<?php echo $user_id; ?>">
        <?php } ?>

        <div class="row">
        <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "empty" => "Selecione", 'options' => $statuses]); ?>
            </div>
            
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Benefício</label>
                <?php 
                $benefitOptions = array("id" => "benefit_id", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'empty' => 'Selecione');
                if (isset($isEdit) && $isEdit) {
                    $benefitOptions['readonly'] = 'readonly'; $benefitOptions['disabled'] = 'disabled'; 
                }
                echo $this->Form->input('benefit_id', $benefitOptions); 
                ?>
            </div>
            
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                <?php 
                $workingDaysOptions = array("id" => "working_days", "placeholder" => "Dias Úteis", "required" => true, "class" => "form-control mb-3 mb-lg-0");

                if (isset($isEdit) && $isEdit) {
                    $workingDaysOptions['readonly'] = 'readonly';$workingDaysOptions['disabled'] = 'disabled';
                }
                echo $this->Form->input('working_days', $workingDaysOptions); 
                ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Valor Unitário</label>
                <?php 
                $unitPriceOptions = array("id" => "unit_price", 'type' => 'text', "placeholder" => "Valor Unitário", "required" => true, "class" => "form-control mb-3 mb-lg-0 money_field"
                );

                if (isset($isEdit) && $isEdit) {
                    $unitPriceOptions['readonly'] = 'readonly';$unitPriceOptions['disabled'] = 'disabled';
                }

                echo $this->Form->input('unit_price', $unitPriceOptions); 
                ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">N° Cartão</label>
                <?php echo $this->Form->input('card_number', array("id" => "card_number", "placeholder" => "N° Cartão", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Quantidade</label>
                <?php 
                $quantityOptions = array("id" => "quantity", "placeholder" => "Quantidade", "required" => true, "class" => "form-control mb-3 mb-lg-0");

                if (isset($isEdit) && $isEdit) {
                    $quantityOptions['readonly'] = 'readonly';$quantityOptions['disabled'] = 'disabled';
                }
                echo $this->Form->input('quantity', $quantityOptions); 
                ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Valor por dia</label>
                <?php echo $this->Form->input('price_per_day', array("id" => "price_per_day", 'type' => 'text', "placeholder" => "Valor por dia", "required" => true, "class" => "form-control mb-3 mb-lg-0 money_field", 'disabled'));  ?>
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

<?php echo $this->Html->script('itinerary'); ?>
