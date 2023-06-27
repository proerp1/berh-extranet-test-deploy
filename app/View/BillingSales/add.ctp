<?php
    if (isset($id)) {
        echo $this->element("aba_faturamento_vendas_revenda", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('BillingSale', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

            <?php if (isset($id)) { ?>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Faturamento</label>
                    <p><?php echo date('m/Y', strtotime($this->request->data["BillingSale"]["mes_pagamento"])) ?></p>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Status</label>
                    <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>
            <?php } else { ?>
                <div class="mb-7">
                    <label for="cep" class="form-label">Mês/Ano</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <?php echo $this->Form->input('mes_pagamento', ["type" => "text", "class" => "form-control datepickerMes mb-3 mb-lg-0"]);  ?>
                    </div>
                </div>
            <?php } ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/billing_sales' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

       </form> 
    </div>
</div>