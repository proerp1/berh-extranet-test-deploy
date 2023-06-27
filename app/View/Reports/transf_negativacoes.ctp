<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "transf_negativacoes")); ?>" role="form" id="busca" autocomplete="off">
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
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->here.'/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Cliente:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option></option>
                                    <?php
                                        foreach ($clientes as $id => $nome) {
                                            $selected = "";
                                            if (isset($_GET["c"])) {
                                                if($id == $_GET["c"]){
                                                    $selected = "selected";
                                                }
                                            }

                                            echo '<option value="'.$id.'" '.$selected.'>'.$nome.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($statuses); $a++){
                                            $selected = "";
                                            if (isset($_GET["s"])) {
                                                if($statuses[$a]['Status']['id'] == $_GET["s"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$statuses[$a]['Status']['id'].'" '.$selected.'>'.$statuses[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row js_div_copiar pb-6" style="display: none;">
        <div class="col-md-4"></div>
        <form class="col-md-8" action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "save_transf")); ?>/" method="post">
            <input type="hidden" name="negativacoes_id" id="negativacoes_id" value="">
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Copiar negativações para outro cliente</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-10">
                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Cliente" data-allow-clear="true" name="novo_cliente">
                            <option></option>
                            <?php
                                foreach ($clientes as $id => $nome) {
                                    if (isset($_GET['c'])) {
                                        if ($id != $_GET['c']) {
                                            echo '<option value="'.$id.'">'.$nome.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light-success btn-block js-submit-search"><i class="fa fa-clone"></i> Copiar!</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start"><input type="checkbox" class="check_all" id="check_all"> <label for="check_all">Selecionar todos</label></th>
                        <th>Status</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Documento</th>
                        <th>Número do titulo negativado</th>
                        <th>Valor</th>
                        <th class="w-200px min-w-200px rounded-end">Inclusão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($dados) { ?>
                        <?php for ($i=0; $i < count($dados); $i++) { ?>
                            <?php $erros = $dados[$i]['CadastroPefinErros'] ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" class="check_conta check_individual <?php echo $dados[$i]['CadastroPefin']['principal_id'] != '' ? 'tem_coobrigado' : '' ?>" data-id="<?php echo $dados[$i]["CadastroPefin"]["id"] ?>" data-coobrigadoid="<?php echo $dados[$i]["CadastroPefin"]["principal_id"] ?>" id="<?php echo $dados[$i]["CadastroPefin"]["id"] ?>">
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $dados[$i]["Status"]["label"] ?>'>
                                        <?php echo $dados[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['CadastroPefin']['nome'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['NaturezaOperacao']['nome'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['CadastroPefin']['documento'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['CadastroPefin']['numero_titulo'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['CadastroPefin']['valor'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($dados[$i]['CadastroPefin']['created'])) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="9">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);
            $("#s").val(null);
            $("#c").val(null);
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }

            get_ids();
        })

        $(".check_conta").on("click", function(){   
            get_ids();
        })

        $(".tem_coobrigado").on("click", function(){
            var coobrigadoid = $(this).data('coobrigadoid');

            if (!$(this).is(':checked')) {
                $("#"+coobrigadoid).prop('checked', false);
                $("body").find("[data-coobrigadoid='"+coobrigadoid+"']").prop('checked', false);
            } else {
                $("#"+coobrigadoid).prop('checked', true);
                $("body").find("[data-coobrigadoid='"+coobrigadoid+"']").prop('checked', true);
            }

            get_ids();
        });
    })

    function get_ids() {
        if ($(".check_individual:checked").length > 0) {
            $(".js_div_copiar").show();
        } else {
            $(".js_div_copiar").hide();
        }

        var pefinid = '';
        $(".check_individual:checked").each(function(index, el) {
            pefinid += $(this).data('id')+',';
        });

        $("#negativacoes_id").val(pefinid);
    }
</script>