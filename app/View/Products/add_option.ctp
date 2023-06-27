<div class="page page-profile">
  <div class="panel panel-profile">
    <div class="panel-heading bg-dark clearfix mini-box">
      <span class="box-icon bg-success">
        <i class="fa fa-list"></i>
      </span>
      <h3><?php echo $action; ?></h3>
    </div>
  </div>

  <?php echo $this->Session->flash(); ?>

  <ul class="nav nav-tabs">
    <li><a href="<?php echo $this->base.'/products/edit/'.$id; ?>">Dados</a></li>
    <li><a href="<?php echo $this->base.'/products/features/'.$id; ?>">Features</a></li>
    <li><a href="<?php echo $this->base.'/products/answer/'.$id; ?>">Respostas</a></li>
    <li><a href="<?php echo $this->base.'/products/answer_item/'.$id.'/'.$answer_id; ?>">Itens da Resposta</a></li>
    <li class="active"><a href="<?php echo $this->base.'/products/option/'.$id.'/'.$answer_id.'/'.$answer_item_id; ?>">Opções</a></li>
  </ul>

  <section class="panel panel-default">
    <div class="panel-body">
      <?php echo $this->Form->create('ItemOption', array("class" => "form-horizontal col-md-12", "action" => "/".$form_action."/".$id."/".$answer_id."/".$answer_item_id, "method" => "post", "id" => "js-form-submit", 'enctype' => 'multipart/form-data')); ?>
        <input type="hidden" name="data[ItemOption][product_id]" value="<?php echo $id; ?>">
        <input type="hidden" name="data[ItemOption][answer_id]" value="<?php echo $answer_id; ?>">
        <input type="hidden" name="data[ItemOption][answer_item_id]" value="<?php echo $answer_item_id; ?>">
        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Nome</label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('name', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Código</label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('codigo', array("div" => false, "label" => false, "placeholder" => "Código", "class" => "form-control"));  ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base; ?>/products/option/<?php echo $id; ?>/<?php echo $answer_id; ?>/<?php echo $answer_item_id; ?>" class="btn btn-default">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
          </div>
        </div>
      </form>
    </div> <!-- /panel-body -->
  </section> <!-- /panel-default -->  
</div> <!-- /page-profile -->