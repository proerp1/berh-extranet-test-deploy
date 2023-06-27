<script type="text/javascript">
    $(document).ready(function(){
        $(".tel").mask("(99) 9999-9999");
        $(".cel").focusout(function(){
            var phone, element;
            element = $(this);
            element.unmask();
            phone = element.val().replace(/\D/g, '');
            if(phone.length > 10) {
                element.mask("(99) 99999-999?9");
            } else {
                element.mask("(99) 9999-9999?9");
            }
        }).trigger('focusout');
    })
</script>

<?php
    if ($_GET['tipo'] == 'vendedor') {
        echo $this->element("abas_resales", array('id' => $resale_id, 'seller_id' => $id));
    } else {
        echo $this->element("abas_resales", array('id' => $id));
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUser', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if ($_GET['tipo'] == 'vendedor') { ?>
                <input type="hidden" name="data[CustomerUser][seller]" value="1">
            <?php } else { ?>
                <input type="hidden" name="data[CustomerUser][resale]" value="1">
            <?php } ?>
            <input type="hidden" name="data[CustomerUser][customer_id]" value="<?php echo $id ?>">
            <input type="hidden" name="query_string" value="<?php echo $_SERVER['QUERY_STRING'] ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', array("placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Email</label>
                <?php echo $this->Form->input('email', array("placeholder" => "Email", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <?php if (isset($this->request->data['CustomerUser'])): ?>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Usuário</label>
                    <?php echo $this->Form->input('username', array("disabled" => true, "placeholder" => "Usuário", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            <?php endif ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                <?php echo $this->Form->input('tel', array("placeholder" => "Telefone", "class" => "form-control tel mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular</label>
                <?php echo $this->Form->input('cel', array("placeholder" => "Celular", "class" => "form-control cel mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/resales/users/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>