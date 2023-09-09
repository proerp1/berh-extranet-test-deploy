<?php
if (isset($id)) {
    echo $this->element("abas_users");
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('User', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Email</label>
                <?php echo $this->Form->input('username', ["placeholder" => "Email", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Grupo</label>
                <?php echo $this->Form->input('group_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Vendedor</label>
                <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
                    <!-- <input class="form-check-input" type="checkbox" value="1" checked="checked" /> -->
                    <?php echo $this->Form->input('is_seller', ["type" => "checkbox", "class" => "form-check-input"]); ?>
                </label>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Meta</label>
                <?php echo $this->Form->input('sales_goal', ["type" => "text", "placeholder" => "Meta", "class" => "form-control mb-3 mb-lg-0 money_field"]);  ?>
            </div>


        </div>

        <?php if (!isset($id)) { ?>
            <input type="hidden" class="form-control" name="data[User][password]" value="<?php echo $senha ?>">
        <?php } ?>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/users' ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                <?php if (isset($id)) { ?>
                    <a href="<?php echo $this->base; ?>/users/reenviar_senha/<?php echo $id ?>" class="btn btn-primary">Reenviar senha</a>
                <?php } ?>
            </div>
        </div>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.money_field').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>