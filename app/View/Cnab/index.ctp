<?php echo $this->Html->script('moeda'); ?>
<script type="text/javascript">
    $(document).ready(function(){
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
        
        $(".input-daterange").datepicker({format: 'dd/mm/yyyy', multidate: false, weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true});
    })

    function get_ids() {
        if ($(".check_individual:checked").length > 0) {
            $(".js_link_gerar_arquivo").show();
        } else {
            $(".js_link_gerar_arquivo").hide();
        }

        var contaid = '';
        var total = 0;
        $(".check_individual:checked").each(function(index, el) {
            contaid += $(this).data('id')+',';
            
            total += parseFloat($(this).data('valor'));
        });

        // $(".js_link_gerar_arquivo").attr('href', '<?php echo $this->base ?>/cnab/gerar_txt/?id='+contaid);
        $("#income_ids").val(contaid);
        $(".total").text($(".check_individual:checked").length+" conta(s) selecionada(s) no valor total de R$ "+retorna_dinheiro(total));
    }
</script>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "cnab", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
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
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">CNAB gerado?</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option value="" disabled selected>Boleto gerado?</option>
                                    <option value="1" <?php echo isset($_GET["c"]) ? ($_GET["c"] == 1 ? 'selected' : '') : ''; ?>>Sim</option>
                                    <option value="2" <?php echo isset($_GET["c"]) ? ($_GET["c"] == 2 ? 'selected' : '') : ''; ?>>Nao</option>
                                    <option value="3" <?php echo isset($_GET["c"]) ? ($_GET["c"] == 3 ? 'selected' : '') : ''; ?>>Cobrança</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Contas Bancárias:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        foreach ($bancos as $id => $name) {
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($id == $_GET["t"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-group input-daterange" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : '01/'.date('m/Y'); ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : date('t/m/Y'); ?>">
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
            <div class="js_link_gerar_arquivo col-12 mt-3" style="display: none;">
                <a href="#" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#modal_gerar_arquivo">
                    <i class="fas fa-file"></i>
                    Gerar arquivo
                </a>
                <h4 class="total mt-3"></h4>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">
                            <input type="checkbox" class="check_all" id="check_all"> <label for="check_all">Todos</label>
                        </th>
                        <th>Nome</th>
                        <th>Vencimento</th>
                        <th>Valor a receber R$</th>
                        <th>Valor recebido R$</th>
                        <th>Usuário</th>
                        <th>Data de criação</th>
                        <th class="w-250px min-w-250px rounded-end">Saldo devedor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" class="check_conta check_individual" data-id="<?php echo $data[$i]["Income"]["id"] ?>" id="<?php echo $data[$i]["Income"]["id"] ?>" data-valor="<?php echo $data[$i]["Income"]["valor_total_nao_formatado"] ?>">
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"].' - '.$data[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["UserCreated"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["created"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php 
                                        if ($data[$i]["Status"]['id'] == 15 and $data[$i]["Income"]['valor_pago'] < $data[$i]["Income"]['valor_total']){
                                            $devedor = $data[$i]["Income"]['valor_total_nao_formatado'] - (isset($data[$i]["Income"]['valor_pago_nao_formatado']) ? $data[$i]["Income"]['valor_pago_nao_formatado'] : 0);
                                            echo number_format($devedor, 2, ',', '.');
                                        } else {
                                            echo '0,00';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#c").val(null).trigger('change');
            $("#t").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>

<div class="modal fade" tabindex="-1" id="modal_gerar_arquivo" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base.'/cnab/gerar_txt' ?>" class="form-horizontal" method="post">
            <input type="hidden" name="ids" id="income_ids">
                <div class="modal-body">
                    <p>Tem certeza que deseja gerar CNAB para esse(s) cliente(s)?</p>
                    <input type="hidden" name="banco" value="<?php echo isset($_GET['t']) ? $_GET['t'] : '' ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>