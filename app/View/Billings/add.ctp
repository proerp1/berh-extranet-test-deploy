<script type="text/javascript">
    $(document).ready(function(){
        $(".datepicker2").datepicker({
            startView: 1,
            minViewMode: 1,
            language: "pt-BR",
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        <?php if ($form_action == 'edit') { ?>
            $(".datepicker2").prop('disabled', true);
        <?php } ?>
    })
</script>

<?php
    if ($form_action == "edit") {
        echo $this->element("abas_billings", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Billing', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Data do Faturamento (Mês/Ano)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('date_billing', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 datepicker2"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/billings' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
    </div>
</div>