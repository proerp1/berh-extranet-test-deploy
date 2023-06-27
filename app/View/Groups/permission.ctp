<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base.'/groups/edit/'.$id; ?>">Grupos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base.'/groups/permission/'.$id; ?>">Permissões</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <form action="<?php echo $this->base; ?>/groups/alter_permission/" method="post">
            <input type="hidden" name="group_id" value="<?php echo $id; ?>">
            <div class="table-responsive">
                <table class="table perm_table all-default">
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-4 rounded-start">Área</th>
                            <th>Leitura</th>
                            <th>Escrita</th>
                            <th class="rounded-end">Exclusão</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=0; $i < count($permissions); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $permissions[$i]["pa"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <div class="container-switcher">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-switcher btn-true btn-icon <?php echo $permissions[$i]["pe"]["leitura"] == 1 ? "btn-success active" : ""; ?>" value="1">
                                            <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-switcher btn-false btn-icon <?php echo $permissions[$i]["pe"]["leitura"] == 0 || $permissions[$i]["pe"]["leitura"] == null  ? "btn-danger active" : ""; ?>" value="0"><i class="fas fa-times "></i></button>
                                        </div>
                                        <input type="hidden" name="permissoes[<?php echo $permissions[$i]["pa"]["id"]; ?>][leitura]" value="<?php echo $permissions[$i]["pe"]["leitura"] == "" ? 0 : $permissions[$i]["pe"]["leitura"]; ?>">
                                    </div>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <div class="container-switcher">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-switcher btn-true btn-icon <?php echo $permissions[$i]["pe"]["escrita"] == 1 ? "btn-success active" : ""; ?>" value="1">
                                            <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-switcher btn-false btn-icon <?php echo $permissions[$i]["pe"]["escrita"] == 0 || $permissions[$i]["pe"]["escrita"] == null ? "btn-danger active" : ""; ?>" value="0"><i class="fas fa-times "></i></button>
                                        </div>
                                        <input type="hidden" name="permissoes[<?php echo $permissions[$i]["pa"]["id"]; ?>][escrita]" value="<?php echo $permissions[$i]["pe"]["escrita"] == "" ? 0 : $permissions[$i]["pe"]["escrita"]; ?>">
                                    </div>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <div class="container-switcher">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-switcher btn-true btn-icon <?php echo $permissions[$i]["pe"]["excluir"] == 1 ? "btn-success active" : ""; ?>" value="1">
                                            <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-switcher btn-false btn-icon <?php echo $permissions[$i]["pe"]["excluir"] == 0 || $permissions[$i]["pe"]["excluir"] == null ? "btn-danger active" : ""; ?>" value="0"><i class="fas fa-times "></i></button>
                                        </div>
                                        <input type="hidden" name="permissoes[<?php echo $permissions[$i]["pa"]["id"]; ?>][excluir]" value="<?php echo $permissions[$i]["pe"]["excluir"] == "" ? 0 : $permissions[$i]["pe"]["excluir"]; ?>">
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base.'/groups/edit/'.$id ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
  $(document).ready(function(){
    $("#js-form-submit").on("submit", function(){
      var $el = $(".js-salvar");

      $el.button('loading');

      setTimeout(function(){$el.button('reset')},6000);
    });

    $(".btn-switcher").on("click", function(){
      var div_mae = $(this).parent().parent();
      var botao = $(this);

      if($(this).hasClass("btn-false")){
        var type = 0;
      }
      else{
        var type = 1;
      }

      div_mae.find("input[type='hidden']").val(type);

      div_mae.find("button").each(function(){
        $(this).removeClass("active");
        $(this).removeClass("btn-success");
        $(this).removeClass("btn-danger");
      })
      if($(this).hasClass("btn-false")){
        $(this).addClass("btn-danger");
      } else {
        $(this).addClass("btn-success");
      }
      $(this).addClass("active");
    })
  })
</script>
