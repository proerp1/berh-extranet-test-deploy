<?php $url_novo = $this->base."/plans/add_compsition/".$id; ?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#PlanProductGratuidade").on("keypress", function (event){
            var regex = new RegExp("^[a-zA-Z0-9]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
        });
    })
</script>
<?php
    echo $this->element("abas_plans", ['id' => $id]);
?>


<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('PlanProduct', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <input type="hidden" name="data[PlanProduct][plan_id]" value="<?php echo $id ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Produto</label>
                <?php echo $this->Form->input('product_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <?php if ($_GET['composicao'] == 2): ?>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Gratuidade</label>
                    <?php echo $this->Form->input('gratuidade', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['Não', 'Sim']]);?>
                </div>
            <?php endif ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/plans' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
    <form action="" role="form" id="busca" autocomplete="off">
        <input type="hidden" name="composicao" value="<?php echo $_GET['composicao'] ?>">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Produto</th>
                        <?php if ($_GET['composicao'] == 2): ?>
                            <th>Gratuidade</th>
                        <?php endif ?>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Product"]["name"]; ?></td>
                                <?php if ($_GET['composicao'] == 2): ?>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["PlanProduct"]["gratuidade"] ? 'Sim' : 'Não'; ?></td>
                                <?php endif ?>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/plans/delete_composition/'.$id.'/'.$data[$i]["PlanProduct"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="8">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>