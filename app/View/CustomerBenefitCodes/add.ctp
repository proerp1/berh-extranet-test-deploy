<?php
    if (isset($customer_id)) {
        $url = $this->here;
        echo $this->element("abas_customers", array('id' => $customer_id, 'url' => $url));
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerBenefitCode', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[CustomerBenefitCode][customer_id]" value="<?= $customer_id ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Cod Beneficio BE</label>
              <?php echo $this->Form->input('benefit_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Cod Beneficio Cliente</label>
                <?php echo $this->Form->input('code_customer', ["placeholder" => "Código", "required" => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>
        </div>
            <br>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base . '/customer_benefit_codes/index/'.$customer_id ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>