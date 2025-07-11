<?php
    if (isset($benefit_id)) {
        $url = $this->here;
        echo $this->element("abas_beneficios", array('id' => $benefit_id, 'url' => $url));
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('BenefitCustomer', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[BenefitCustomer][benefits_id]" value="<?= $benefit_id ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Cliente</label>
                <?php echo $this->Form->input('customer_id', ["class" => "form-select mb-3 mb-lg-0", 'required' => true, "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Código</label>
                <?php echo $this->Form->input('code', ["placeholder" => "Código", "required" => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>
        </div>
            <br>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base . '/benefit_customers/index/'.$benefit_id ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>