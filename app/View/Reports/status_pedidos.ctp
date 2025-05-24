<?php echo $this->element("../Orders/_totais_index"); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "status_pedidos")); ?>" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->base.'/reports/status_pedidos/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <a href="#" id="alterar_sel" class="btn btn-primary me-3">
                        <i class="fas fa-edit"></i>
                        Alterar Status Pedido
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>

                        <div class="px-7 py-5">
                            
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <span class="input-group-text" style="padding: 5px;"> de </span>
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET['de']) ? $_GET['de'] : ''; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET['ate']) ? $_GET['ate'] : ''; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Clientes:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option value="">Selecione</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Tipo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="tipo[]" id="tipo" multiple>
                                    <option value=''></option>
                                    <option value="2" <?php echo isset($_GET['tipo']) && in_array('2', $_GET['tipo']) ? 'selected' : ''; ?>>Automático</option>
                                    <option value="4" <?php echo isset($_GET['tipo']) && in_array('4', $_GET['tipo']) ? 'selected' : ''; ?>>Emissão</option>
                                    <option value="1" <?php echo isset($_GET['tipo']) && in_array('1', $_GET['tipo']) ? 'selected' : ''; ?>>Importação</option>
                                    <option value="3" <?php echo isset($_GET['tipo']) && in_array('3', $_GET['tipo']) ? 'selected' : ''; ?>>PIX</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Número(s) de Pedido:</label>
                                <input type="text" class="form-control form-control-solid fw-bolder" name="num" id="num" placeholder="Digite o(s) pedido(s) separado(s) por virgula" value="<?php echo isset($_GET['num']) ? $_GET['num'] : ''; ?>">
                            </div>
                            <div id="selectedNumbers"></div>

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

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-80px min-w-80px rounded-start">
                            <input type="checkbox" class="check_all">
                        </th>
                        <th>Status</th>
                        <th>Código</th>
                        <th>Data de criação</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data Pagamento</th>
                        <th>Data Finalização</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Taxa</th>
                        <th>Desconto</th>
                        <th>TPP</th>
                        <th>Fee Economia</th>
                        <th>Cliente</th>
                        <th>Economia</th>
                        <th data-priority="1"><?php echo $this->Paginator->sort('Order.total', 'Total'); ?> <?php echo $this->Paginator->sortKey() == 'Order.total' ? "<i class='fas fa-sort-".($this->Paginator->sortDir() == 'asc' ? 'up' : 'down')."'></i>" : ''; ?></th>
                        <th>Usuário</th>
                        <th>Grupo Econômico</th>
                        <th>Tipo</th>
                        <th>Gestão Eficiente</th>
                        <th class="w-200px min-w-200px rounded-end">Vencimento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <?php 
                                $fee_economia = 0;
                                $total_economia = 0;
                                $vl_economia = $data[$i][0]["total_balances"];
                                $fee_saldo = $data[$i]["Order"]["fee_saldo_not_formated"];

                                if ($fee_saldo != 0 and $vl_economia != 0) {
                                    $fee_economia = (($fee_saldo / 100) * ($vl_economia));
                                }

                                $vl_economia = ($vl_economia - $fee_economia);
                                $total_economia = ($vl_economia + $fee_economia);

                                $v_is_partial = "";
                                if ($data[$i]['Order']['is_partial'] == 1) {
                                    $v_is_partial = "Importação";
                                } elseif ($data[$i]['Order']['is_partial'] == 2) {
                                    $v_is_partial = "Automático";
                                } elseif ($data[$i]['Order']['is_partial'] == 3) {
                                    $v_is_partial = "PIX";
                                } elseif ($data[$i]['Order']['is_partial'] == 4) {
                                    $v_is_partial = "Emissão";
                                }
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" name="alt_linha" class="check_individual" id="">
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['created'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><input type="hidden" class="order_id" value="<?php echo $data[$i]["Order"]["id"]; ?>"><a href="<?php echo $this->base.'/orders/edit/'.$data[$i]["Order"]["id"]; ?>"><?php echo $data[$i]["Order"]["id"]; ?></a></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>     
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["end_date"]; ?></td>     
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["tpp_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($fee_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($vl_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($total_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerCreator"]["name"] != '' ? $data[$i]["CustomerCreator"]["name"] : $data[$i]["Creator"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['EconomicGroup']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $v_is_partial ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]['pedido_complementar'] == 1 ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["due_date"]; ?></td>     
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_alterar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
            </div>
            <div class="modal-body">
                <p>Alterar Status Pedido</p>

                <div class="row" style="margin-top:20px;">
                    <label class="mb-2">Status Pedido</label>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-custom form-check-solid">
                                <select name="status_pedido" id="status_pedido" class="form-select mb-3 mb-lg-0">
                                <?php
                                    foreach ($status as $data) {
                                        echo '<option value="'.$data['Status']['id'].'">'.$data['Status']['name'].'</option>';
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" id="canc_confirm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="alterar_confirm">Sim</button>
            </div>
        </div>
    </div>
</div>


<script>
    function trigger_date_change() {
        var v_ini = $("#de").val();
        var v_end = $("#ate").val();

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const curr_c = urlParams.get('c');
        const curr_sup = urlParams.get('sup');

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "reports", "action" => "getSupplierAndCustomerByDate")); ?>',
            type: 'POST',
            data: {
                ini: v_ini,
                end: v_end
            },
            success: function(data) {

                var obj = JSON.parse(data);
                var html = '<option>Selecione</option>';
                var sel = '';
                for (var i = 0; i < obj.customers.length; i++) {
                    if (obj.customers[i].Customer.id == curr_c) {
                        sel = 'selected';
                    } else {
                        sel = '';
                    }
                    html += '<option value="' + obj.customers[i].Customer.id + '" '+sel+'>' + obj.customers[i].Customer.nome_primario + '</option>';
                }
                $("#c").html(html);

                html = '<option>Selecione</option>';
                var sel_sup = '';
                for (var i = 0; i < obj.suppliers.length; i++) {
                    if (obj.suppliers[i].Supplier.id == curr_sup) {
                        sel_sup = 'selected';
                    } else {
                        sel_sup = '';
                    }
                    html += '<option value="' + obj.suppliers[i].Supplier.id + '" '+sel_sup+'>' + obj.suppliers[i].Supplier.nome_fantasia + '</option>';
                }
                $("#sup").html(html);

                // reload select2
                $("#c").select2();
                $("#sup").select2();
            }
        });
    }

    function fnc_dt_range() {
        $('.filter').attr('disabled', false);

        var dataInicialStr = $('#de').val();
        var dataFinalStr = $('#ate').val();

        var regexData = /^(\d{2})\/(\d{2})\/(\d{4})$/;

        var matchInicial = dataInicialStr.match(regexData);
        var matchFinal = dataFinalStr.match(regexData);

        if (matchInicial && matchFinal) {
            var dataInicial = new Date(matchInicial[3], matchInicial[2] - 1, matchInicial[1]);
            var dataFinal = new Date(matchFinal[3], matchFinal[2] - 1, matchFinal[1]);

            var diff = (dataFinal - dataInicial);
            var diffDays = (diff / (1000 * 60 * 60 * 24));

            if (diffDays > 365 || diffDays < 0) {
                alert('A data final deve ser no máximo 1 ano após a data inicial.');
                $('.filter').attr('disabled', true);

                return false;
            }
        } else {
            alert('Formato de data inválido. Use o formato dd/mm/yyyy.');
            $('.filter').attr('disabled', true);

            return false;
        }
    }

    $(document).ready(function() {
        $(".datepicker").mask("99/99/9999");

        trigger_date_change();

        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });

        $('#de').on('change', function() {
            fnc_dt_range();
            trigger_date_change();
        });
        
        $('#ate').on('change', function() {
            fnc_dt_range();
            trigger_date_change();
        });

        $('#alterar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="alt_linha"]:checked').length > 0) {
                $('#modal_alterar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a ser alterado.');
            }
        });

        $('#alterar_confirm').on('click', function(e) {
            e.preventDefault();
            
            $(this).prop('disabled', true);
            $("#canc_confirm").prop('disabled', true);

            const v_status_pedido = $('#status_pedido').val();

            const checkboxes = $('input[name="alt_linha"]:checked');
            const orderIds = [];

            checkboxes.each(function() {
                orderIds.push($(this).parent().parent().find('.order_id').val());
            });

            if (orderIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/reports/alter_status_pedido',
                    data: {
                        orderIds,
                        v_status_pedido,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });
    });
</script>

<style>
    table tr th a {
        color: #009ef7;
        display: block;
        width: 100%;
        height: 100%;
    }
</style>
