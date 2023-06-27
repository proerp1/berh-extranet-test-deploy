 <script type="text/javascript">
  $(document).ready(function(){
    qtde_colunas();

    $("#AnswerAgruparColunas1").on("change", function(){
      qtde_colunas();
    });

    $("#AnswerAgruparColunas2").on("change", function(){
      qtde_colunas();
    });
  });

  function qtde_colunas(){
    if ($('#AnswerAgruparColunas1').prop('checked')) {
      $("#qtde_colunas").show();
    } 
    if ($('#AnswerAgruparColunas2').prop('checked')) {
      $("#qtde_colunas").hide();
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
    <li class="active"><a href="<?php echo $this->base.'/products/answer/'.$id; ?>">Respostas</a></li>
    <li><a href="<?php echo $this->base.'/products/answer_item/'.$id.'/'.$answer_id; ?>">Itens da Resposta</a></li>
  </ul>

  <?php
    $options = array('1' => 'Sim','2' => 'Não');
    $attributes = array('legend' => false); ?>

  <section class="panel panel-default">
    <div class="panel-body">
      <?php echo $this->Form->create('Answer', array("class" => "form-horizontal col-md-12", "action" => "/".$form_action."/".$id, "method" => "post", "id" => "js-form-submit", 'enctype' => 'multipart/form-data')); ?>
        <input type="hidden" name="data[Answer][product_id]" value="<?php echo $id; ?>">
        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Nome</label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('name', array("div" => false, "label" => false, "placeholder" => "Nome", "class" => "form-control"));  ?>
          </div>
        </div>
        
        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Visível ao Cliente?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('visivel_cliente', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputEmail" class="col-sm-2 control-label">Marcar como Restrição?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('flag_restricao', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputEmail" class="col-sm-2 control-label">Agrupar Colunas?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('agrupar_colunas', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputEmail" class="col-sm-2 control-label">Feature?</label>
          <div class="col-sm-9">
            <?php echo $this->Form->radio('flag_feature', $options, $attributes);  ?>
          </div>
        </div>

        <div class="form-group">
          <label for="inputNome" class="col-sm-2 control-label">Agrupar com</label>
          <div class="col-sm-9">
            <select name="data[Answer][pai_id]" class="form-control" id="pai_id">
              <option value="">Nenhum</option>
              <?php
                for($a = 0; $a < count($resp_pai); $a++){
                  if($resp_pai[$a]['Answer']['id'] == $this->request->data['Answer']['pai_id']){
                    $selected = "selected";
                  } else {
                    $selected = "";
                  } ?>
                  <option value="<?php echo $resp_pai[$a]['Answer']['id']; ?>" <?php echo $selected; ?>><?php echo $resp_pai[$a]['Answer']['name']; ?></option>
          <?php }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group" id="qtde_colunas">
          <label for="inputNome" class="col-sm-2 control-label"></label>
          <div class="col-sm-9">
            <?php echo $this->Form->input('qtde_colunas', array("div" => false, "label" => false, "placeholder" => "Qtde Colunas", "class" => "form-control"));  ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base; ?>/products/answer/<?php echo $id; ?>" class="btn btn-default">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
          </div>
        </div>
      </form>
    </div> <!-- /panel-body -->
  </section> <!-- /panel-default -->
  
  <div class="panel-body">
    <table class="table table-bordered" id="datatable">
      <thead>
        <tr>
          <th class="default">Nome</th>
          <th style="width: 180px;">Visível ao Cliente?</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data_lista) { ?>
          <?php for ($i=0; $i < count($data_lista); $i++) { ?>
            <tr>
              <td><?php echo $data_lista[$i]["Answer"]["name"]; ?></td>
              <td><?php echo $data_lista[$i]["Answer"]["visivel_cliente"] == 1 ? "Sim" : "Não"; ?></td>
            </tr>
          <?php } ?>
        <?php } else{ ?>
          <tr>
            <td colspan="8">Nenhum registro encontrado</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

  </div> <!-- /painel-body -->
  
</div> <!-- /page-profile -->