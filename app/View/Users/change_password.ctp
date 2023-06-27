<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('User', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Nova senha</label>
                <?php echo $this->Form->input('password', ["placeholder" => "Nova senha", "id" => "inputPassword", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Confirmar senha</label>
                <?php echo $this->Form->input('conf_password', ["placeholder" => "Confirmar senha", "id" => "inputConfPassword", "class" => "form-control mb-3 mb-lg-0", "type" => "password"]);?>
                <span class="error-message" style="display:none">As senhas devem coincidir</span>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base; ?>/users/" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#js-form-submit").on("submit", function(){
            $("#inputConfPassword").parent().find(".error-message").hide();
            $("#inputConfPassword").removeClass("form-error");

            if($("#inputPassword").val() != $("#inputConfPassword").val()){
                $("#inputConfPassword").addClass("form-error");

                $("#inputConfPassword").parent().find(".error-message").show();
                event.preventDefault();
            } else {
                var $el = $(".js-salvar");

                $el.button('loading');

                setTimeout(function(){$el.button('reset')},6000);
            }
        })
    })
</script>