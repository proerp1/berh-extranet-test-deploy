<?php
    $form_action = isset($economicGroupId) ? 'edit' : 'add';
    echo $this->element("abas_customers", array('id' => $id));
?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#EconomicGroupDocument").mask("99.999.999/9999-99");
    }); 
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('EconomicGroup', ['id' => 'js-form-submit', 'url' => ['action' => $form_action, $id, isset($economicGroupId) ? $economicGroupId : ''], 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">

        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2 required">Status</label>
            <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
        </div>

        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2 required">Nome</label>
            <?php echo $this->Form->input('name', ['placeholder' => 'Nome', 'class' => 'form-control mb-3 mb-lg-0']); ?>
        </div>

        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2 required">Razão Social</label>
            <?php echo $this->Form->input('razao_social', ['placeholder' => 'Razão Social', 'class' => 'form-control mb-3 mb-lg-0']); ?>
        </div>

        <div class="mb-7 col">
            <label class="fw-semibold fs-6 mb-2 required">CNPJ</label>
            <?php echo $this->Form->input('document', ['placeholder' => 'CNPJ', 'class' => 'form-control mb-3 mb-lg-0']); ?>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/economic_groups/index/'.$id.'/' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>
