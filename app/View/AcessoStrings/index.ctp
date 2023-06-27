<?php echo $this->element("abas_customers", ['id' => $id]); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-3">
        <div class="card-title">
            <p>Dados de Acesso</p>
        </div>
    </div>
    <div class="card-body pt-0">
        <?php echo $this->Form->create('Acesso', ["id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post"]); ?>
            <div class="table-responsive">
            
                <?php if ($produto) { ?>
                  <?php for ($i=0; $i <count($produto) ; $i++) { ?>
                    <?php
                      $count = 0;
                      for ($b=0; $b < count($produto[$i]["Feature"]); $b++) {
                          $temp = $produto[$i]["Feature"];
                          if ($temp[$b]["status_id"] == 1 && $temp[$b]["data_cancel"] == '1901-01-01 00:00:00' && $temp[$b]["acesso_cli"] == '') {
                              $count++;
                          }
                      }
                      $checked = "";
                      for ($a=0; $a <count($acesso) ; $a++) {
                          if ($acesso) {
                              if ($acesso[$a]["Product"]["id"] == $produto[$i]["Product"]["id"]) {
                                  $checked = "checked";
                              }
                          }
                      }
                    ?>
                    <table class="table table-row-bordered table-row-gray-300 border border-gray-300 table-striped cf">
                      <thead>
                      <tr>
                        <th colspan="<?php echo $count ?>" class="ps-4">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" id="acessoProduto<?php echo $produto[$i]["Product"]["id"]; ?>" class="product form-check-input" name="acessoProduto[]" <?php echo $checked; ?> value="<?php echo $produto[$i]["Product"]["id"]; ?>">
                                <label class="form-check-label" style="padding-left:5px" for="acessoProduto<?php echo $produto[$i]["Product"]["id"]; ?>"><?php echo $produto[$i]["Product"]["name"]; ?></label>
                            </div>
                        </th>
                      </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <?php
                            for ($b=0; $b < count($produto[$i]["Feature"]); $b++) {
                                $temp = $produto[$i]["Feature"];
                                $features = "";
                                if ($temp[$b]["status_id"] == 1 && $temp[$b]["data_cancel"] == '1901-01-01 00:00:00' && $temp[$b]["acesso_cli"] == '') {
                                    $features = $temp[$b];
                                    $checked = "";
                                    for ($a=0; $a <count($acessoFeatures) ; $a++) {
                                        if ($acessoFeatures[$a]["AcessoFeature"]["feature_id"] == $features["id"]) {
                                            $checked = "checked";
                                        }
                                    } ?> 
                                <td class="ps-4">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input type="checkbox" id="acessoFeature<?php echo $features["id"]; ?>" name="acessoFeature[<?php echo $produto[$i]["Product"]["id"] ?>][]" <?php echo $checked; ?> data-produtoid="<?php echo $produto[$i]["Product"]["id"]; ?>" class="feature acessoProduto<?php echo $produto[$i]["Product"]["id"]; ?> form-check-input" value="<?php echo $features["id"]; ?>">
                                        <label class="form-check-label" style="padding-left:5px" for="acessoFeature<?php echo $features["id"]; ?>"><?php  echo $features["name"]; ?></label>
                                    </div>
                                </td>
                                <?php
                                }
                                if (($b+1) % 6 == 0) {
                                    echo "</tr><tr>";
                                }
                            }
                          ?>
                        </tr>
                      </tbody>
                    </table>
                  <?php } ?>
                <?php } ?>
            
            </div>

            <div class="mt-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <button class="btn btn-success js-salvar" type="submit" data-loading-text="Aguarde...">Salvar</button>
                    <button class="btn btn-primary js_marcar_todos" type="button" >Marcar Todos</button>
                    <button class="btn btn-primary js_desmarcar_todos" type="button" >Desmarcar Todos</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $(".feature").on("change", function(){
      var produto_id = $(this).data('produtoid');

      if ($(this).is(':checked') && !$("#acessoProduto"+produto_id).is(':checked')) {
        $("#acessoProduto"+produto_id).click();
      };
    });

    $(".product").on("change", function(){
      var produto_id = $(this).val();

      if (!$(this).is(':checked')) {
        $(".acessoProduto"+produto_id).attr('checked', false);
      };
    });

    $(".js_marcar_todos").on("click", function(){
      $("input[type='checkbox']").prop('checked', true);
    });

    $(".js_desmarcar_todos").on("click", function(){
      $("input[type='checkbox']").prop('checked', false);
    });
  })
</script>