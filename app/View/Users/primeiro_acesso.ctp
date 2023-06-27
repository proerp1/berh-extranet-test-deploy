<div class="page page-profile">
  <div class="panel panel-profile">
    <div class="panel-heading bg-dark clearfix mini-box">
      <span class="box-icon bg-success">
        <i class="fa fa-lock"></i>
      </span>
      <h3><?php echo $action; ?></h3>
    </div>
  </div>
  
  <?php echo $this->Session->flash(); ?>

  <section class="panel panel-default">
    <div class="panel-body">
		  <?php echo $this->Form->create('User', array("id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post")); ?>

		    <div class="form-group">
	        <label for="inputPassword" class="col-sm-2 control-label">Nova senha</label>
	        <div class="col-sm-9">
	          <?php echo $this->Form->input('password', array("div" => false, "label" => false, "id" => "inputPassword", "placeholder" => "Nova senha", "class" => "form-control"));?>
	        </div>
		    </div>

		    <div class="form-group">
		      <label for="inputConfPassword" class="col-sm-2 control-label">Confirmar Senha</label>
		      <div class="col-sm-9">
		        <input type="password" class="form-control" id="inputConfPassword" placeholder="Confirmar Senha">
            <span class="help-block" style="display:none">As senhas devem coincidir</span>
		      </div>
		    </div>

		  <div class="form-group">
		    <div class="col-sm-offset-2 col-sm-9">
		      <a href="<?php echo $this->base; ?>/users/" class="btn btn-default">Voltar</a>
		      <button type="submit" class="btn btn-success js-salvar-primeiro" data-loading-text="Aguarde...">Salvar</button>
		    </div>
		  </div>
		</form>
    </div> <!-- /panel-body -->
  </section> <!-- /panel-default -->
</div> <!-- /page-profile -->

<script>
  $(document).ready(function(){
       
    $("#js-form-submit").on("submit", function(){
      $("#inputConfPassword").parent().find(".help-block").hide();

      if($("#inputPassword").val() != $("#inputConfPassword").val()){
        $("#inputConfPassword").parent().parent().addClass("has-error");

        $("#inputConfPassword").parent().find(".help-block").show();
        event.preventDefault();
      } else {
        var $el = $(".js-salvar-primeiro");

        $el.button('loading');

        setTimeout(function(){$el.button('reset')},6000);
      }

    })
  })
</script>