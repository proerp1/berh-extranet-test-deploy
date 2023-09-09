<div class="card mb-5 mb-xl-8">
  <div class="card-body pt-7 py-3">
    <?php echo $this->Form->create('User', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

    <div class="row">
      <div class="mb-7 col">
        <label class="fw-semibold fs-6 mb-2">Senha</label>
        <?php echo $this->Form->input('password', array("div" => false, "label" => false, "id" => "inputPassword", "placeholder" => "Nova senha", "class" => "form-control")); ?>
      </div>
    </div>

    <div class="row">
      <div class="mb-7 col">
        <label class="fw-semibold fs-6 mb-2">Confirmar Senha</label>
        <input type="password" class="form-control" id="inputConfPassword" placeholder="Confirmar Senha">
        <span class="help-block" style="display:none; color: red">As senhas devem coincidir</span>
      </div>
    </div>

    <div class="mb-7">
      <div class="col-sm-offset-2 col-sm-9">
        <a href="<?php echo $this->base . '/dashboard' ?>" class="btn btn-light-dark">Voltar</a>
        <button type="submit" class="btn btn-success js-salvar-primeiro" data-loading-text="Aguarde...">Salvar</button>
      </div>
    </div>

    </form>
  </div>
</div>

<script>
  $(document).ready(function() {

    $("#js-form-submit").on("submit", function() {
      $("#inputConfPassword").parent().find(".help-block").hide();

      if ($("#inputPassword").val() != $("#inputConfPassword").val()) {
        $("#inputConfPassword").parent().parent().addClass("has-error");

        $("#inputConfPassword").parent().find(".help-block").show();
        event.preventDefault();
      } else {
        var $el = $(".js-salvar-primeiro");

        $el.button('loading');

        setTimeout(function() {
          $el.button('reset')
        }, 6000);
      }

    })
  })
</script>