<?php
    if ($form_action == 'edit') {
        echo $this->element("aba_plano_contas", ['id' => $id, 'pai_id' => $pai_id, 'nivel', $nivel]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('PlanoConta', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número de Identificação</label>
                <?php echo $this->Form->input('numero', ["placeholder" => "Número de Identificação", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Código de Referência</label>
                <?php echo $this->Form->input('referencia', ["type" => "number", "placeholder" => "Código de Referência", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/plano_contas/index'.$nivel.'/'.$nivel.'/'.$pai_id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".nivel").on("change", function(){
            var val = $(this).val();

            if (val == 1) {
                $("#pai_id").prop('required', false);
            } else {
                $("#pai_id").prop('required', true);
            }
        })
    })
</script>