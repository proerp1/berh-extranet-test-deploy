<script>
  $(document).ready(function(){
    msg_item();

    $("#formatacao").on("change", function(){
      msg_item();
    });
  });

  function msg_item(){
    var val = $("#formatacao").val();
    
    if(val == "9"){
      $("#msg_personalizada").show();
    } else {
      $("#msg_personalizada").hide();
    }
  }
</script>

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
    <li class="active"><a href="<?php echo $this->base.'/products/answer_item/'.$id.'/'.$answer_id; ?>">Itens da Resposta</a></li>
    <li><a href="<?php echo $this->base.'/products/option/'.$id.'/'.$answer_id.'/'.$this->request->data['AnswerItem']['id']; ?>">Opções</a></li>
  </ul>

  <?php
    $options = array('1' => 'Sim','2' => 'Não');
    $attributes = array('legend' => false); ?>

  <section class="panel panel-default">
    <div class="panel-body">
      <?php echo $this->Form->create('AnswerItem', array("class" => "form-horizontal col-md-12", "action" => "/".$form_action."/".$id."/".$answer_id, "method" => "post", "id" => "js-form-submit", 'enctype' => 'multipart/form-data')); ?>
        <input type="hidden" name="data[AnswerItem][product_id]" value="<?php echo $id; ?>">
        <input type="hidden" name="data[AnswerItem][answer_id]" value="<?php echo $answer_id; ?>">
        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">1ª opção de nome</label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('name', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">2ª opção de nome</label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('itemNome2', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
          </div>
        </div>
        
        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Visível ao Cliente?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('visivel_cliente', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputEmail" class="col-sm-2 control-label">Multivalorado?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('multivalorado', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Formatação</label>
          <div class="col-sm-9">
            <select name="data[AnswerItem][formatacao]" class="form-control" id="formatacao">
              <option value="">Selecione...</option>
              <option value="1" <?php echo $this->request->data['AnswerItem']['formatacao'] == 1 ? "selected": ""; ?>>Data (AAAAMMDD)</option>
              <option value="15" <?php echo $this->request->data['AnswerItem']['formatacao'] == 15 ? "selected": ""; ?>>Data (AAAAMMDD com 0 no relato)</option>
              <option value="7" <?php echo $this->request->data['AnswerItem']['formatacao'] == 7 ? "selected": ""; ?>>Data 2 (MMAAAA)</option>
              <option value="4" <?php echo $this->request->data['AnswerItem']['formatacao'] == 4 ? "selected": ""; ?>>Data 3 (DDMMAAAA)</option>
              <option value="8" <?php echo $this->request->data['AnswerItem']['formatacao'] == 8 ? "selected": ""; ?>>Data 4 (AAAAMM)</option>
              <option value="2" <?php echo $this->request->data['AnswerItem']['formatacao'] == 2 ? "selected": ""; ?>>Dinheiro</option>
              <option value="5" <?php echo $this->request->data['AnswerItem']['formatacao'] == 5 ? "selected": ""; ?>>Dinheiro (Int)</option>
              <option value="11" <?php echo $this->request->data['AnswerItem']['formatacao'] == 11 ? "selected": ""; ?>>Dinheiro (Somente R$)</option>
              <option value="14" <?php echo $this->request->data['AnswerItem']['formatacao'] == 14 ? "selected": ""; ?>>Dinheiro (Milhar)</option>
              <option value="10" <?php echo $this->request->data['AnswerItem']['formatacao'] == 10 ? "selected": ""; ?>>Inteiro</option>
              <option value="3" <?php echo $this->request->data['AnswerItem']['formatacao'] == 3 ? "selected": ""; ?>>Horário</option>
              <option value="6" <?php echo $this->request->data['AnswerItem']['formatacao'] == 6 ? "selected": ""; ?>>Porcentagem (%)</option>
              <option value="12" <?php echo $this->request->data['AnswerItem']['formatacao'] == 12 ? "selected": ""; ?>>Porcentagem (% com decimal)</option>
              <option value="16" <?php echo $this->request->data['AnswerItem']['formatacao'] == 16 ? "selected": ""; ?>>Porcentagem (% com duas casas decimais)</option>
              <option value="13" <?php echo $this->request->data['AnswerItem']['formatacao'] == 13 ? "selected": ""; ?>>Imagem</option>
              <option value="9" <?php echo $this->request->data['AnswerItem']['formatacao'] == 9 ? "selected": ""; ?>>Mensagem</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="msg_personalizada">
          <label for="inputNome" class="col-sm-2 control-label"></label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('msg_personalizada', array("div" => false, "label" => false, "placeholder" => "Mensagem", "class" => "form-control"));  ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base; ?>/products/answer_item/<?php echo $id; ?>/<?php echo $answer_id; ?>" class="btn btn-default">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
          </div>
        </div>
      </form>
    </div> <!-- /panel-body -->
  </section> <!-- /panel-default -->  
</div> <!-- /page-profile -->