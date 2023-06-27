<script type="text/javascript">
    $(document).ready(function(){
        toogleButton();

        $(".cobrador").on("change", function(){
            toogleButton();
        });

        $("#de, #ate").on("change", function(){
            toogleButton();
        });
    })

    function toogleButton() {
        if ($("input[type='checkbox']").is(':checked') && $("#de").val() != "" && $("#ate").val() != "") {
            $(".js-salvar").prop("disabled", false);
        } else {
            $(".js-salvar").prop("disabled", true);
        }
    }
</script>

<?php
    if(isset($id)) {
        echo $this->element("abas_customers", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <?php echo $this->Form->create('DistribuicaoCobranca', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <div class="card-header border-0 pt-6 pb-6">
            <div>
                <div class="row">
                    <div class="col mb-10">
                        <label class="form-label fs-5 fw-bold mb-3">Período:</label>
                        <div class="input-daterange input-group" id="datepicker">
                            <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                            <span class="input-group-text" style="padding: 5px;"> até </span>
                            <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                        </div>
                    </div>

                    <div class="col mb-10">
                        <label class="form-label fs-5 fw-bold mb-3">Status cliente:</label>
                        <select class="form-select fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sCliente">
                            <option></option>
                            <?php foreach ($statusCliente as $key => $value) { ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col mb-10">
                        <label class="form-label fs-5 fw-bold mb-3">Status contas:</label>
                        <select class="form-select fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sContas">
                            <option></option>
                            <?php foreach ($statusContas as $key => $value) { ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col mb-10">
                        <label class="form-label fs-5 fw-bold mb-3">Franquia:</label>
                        <select class="form-select fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="resale" required>
                            <option></option>
                            <?php foreach ($resales as $resalesId => $resalesName) { ?>
                                <option value="<?php echo $resalesId ?>"><?php echo $resalesName ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col mb-10">
                        <label class="form-label fs-5 fw-bold mb-3">Gerar:</label>
                        <select class="form-select fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="period">
                            <option value="3">Todas as contas atrasadas</option>
                            <option value="1">Contas que não foram cobradas</option>
                            <option value="2">Contas com da data de retorno</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($cobradors as $id => $name): ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="inputCobrador<?php echo $id ?>" class="control-label">
                                    <?php echo $name ?>
                                    <input type="checkbox" name="data[DistribuicaoCobranca][cobrador_id][]" class="cobrador" id="inputCobrador<?php echo $id ?>" value="<?php echo $id ?>">
                                </label>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

                <div class="row">
                    <div class="mb-7">
                        <div class="col-sm-offset-2 col-sm-9">
                            <button type="submit" class="btn btn-success js-salvar">Gerar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Data</th>
                        <th>Usuários</th>
                        <th>Quantidade</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($log) { ?>
                        <?php for ($i=0; $i < count($log); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y h:i:s', strtotime($log[$i]["DistribuicaoCobranca"]["created"])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo count($log[$i]['QtdeUsuarios']); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo count($log[$i]['DistribuicaoCobrancaUsuario']); ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/cobrancas/visualizar_divisao/'.$log[$i]["DistribuicaoCobranca"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Ver
                                    </a>
                                    <a href="<?php echo $this->base.'/cobrancas/excluir_divisao/'.$log[$i]["DistribuicaoCobranca"]["id"]; ?>" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>