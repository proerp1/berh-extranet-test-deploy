<?php echo $this->element("../Orders/_abas"); ?>

<div class="row">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="table-responsive" id="search_form">
                <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "compras/" . $id)); ?>" role="form" id="busca" autocomplete="off">
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

                                <a href="#" id="alterar_sel" class="btn btn-primary me-3">
                                    <i class="fas fa-edit"></i>
                                    Alterar Status Processamento
                                </a>

                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                                    <div class="px-7 py-5">
                                        <div class="fs-4 text-dark fw-bolder">Opções</div>
                                    </div>
                                    <div class="separator border-gray-200"></div>

                                    <div class="px-7 py-5">

                                        <div class="mb-10">
                                            <label class="form-label fs-5 fw-bold mb-3">Fornecedores:</label>
                                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sup" id="sup">
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>

                                        <div id="selectedNumbers"></div>

                                        <div class="mb-10">
                                            <label class="form-label fs-5 fw-bold mb-3">Status Processamento:</label>
                                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="stp[]" id="stp" multiple>
                                                <option value="">Selecione</option>
                                                <option value="ARQUIVO_GERADO">ARQUIVO_GERADO</option>
                                                <option value="CADASTRO_INCONSISTENTE">CADASTRO_INCONSISTENTE</option>
                                                <option value="CADASTRO_PROCESSADO">CADASTRO_PROCESSADO</option>
                                                <option value="CREDITO_INCONSISTENTE">CREDITO_INCONSISTENTE</option>
                                                <option value="CREDITO_PROCESSADO">CREDITO_PROCESSADO</option>
                                                <option value="FALHA_GERACAO_ARQUIVO">FALHA_GERACAO_ARQUIVO</option>
                                                <option value="GERAR_PAGAMENTO">GERAR_PAGAMENTO</option>
                                                <option value="INICIO_PROCESSAMENTO">INICIO_PROCESSAMENTO</option>
                                                <option value="PAGAMENTO_REALIZADO">PAGAMENTO_REALIZADO</option>
                                                <option value="PROCESSAMENTO_PENDENTE">PROCESSAMENTO_PENDENTE</option>
                                                <option value="VALIDACAO_PENDENTE">VALIDACAO_PENDENTE</option>
                                            </select>
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

                <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-80px min-w-80px rounded-start">
                            <input type="checkbox" class="check_all">
                        </th>
                        <th>Fornecedor</th>
                        <th>Beneficiário</th>
                        <th>Benefício</th>
                        <th width="90px">Dias Úteis</th>
                        <th width="120px">Quantidade por dia</th>
                        <th>Valor por dia</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Taxa</th>
                        <th class="<?php echo $order['Order']['status_id'] != 83 ? 'rounded-end' : '' ?>">Total</th>
                        <th>Economia</th>
                        <th>Relatório beneficio</th>
                        <th>Data inicio Processamento</th>
                        <th>Data fim Processamento</th>
                        <th>Status Processamento</th>
                        <th>Motivo Processamento</th>
                        <th>Pedido Operadora</th>
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <th class="rounded-end"></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($items_total[0])) { ?>
                        <tr>
                            <td>Total</td>
                            <td colspan="6"></td>
                            <td class="subtotal_sum">R$<?php echo number_format($items_total[0][0]['subtotal'], 2, ',', '.'); ?></td>
                            <td class="transfer_fee_sum">R$<?php echo number_format($items_total[0][0]['transfer_fee'], 2, ',', '.'); ?></td>
                            <td class="commission_fee_sum">R$<?php echo number_format($items_total[0][0]['commission_fee'], 2, ',', '.'); ?></td>
                            <td class="total_sum">R$<?php echo number_format($items_total[0][0]['total'], 2, ',', '.'); ?></td>
                            <td class="saldo_sum">R$<?php echo number_format($items_total[0][0]['saldo'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php } ?>
                    <?php
                    $v_subtotal = 0;
                    $v_transfer_fee = 0;
                    $v_commission_fee = 0;
                    $v_total = 0;
                    $v_saldo = 0;
                    if ($items) { ?>
                        <?php for ($i = 0; $i < count($items); $i++) {
                            $v_subtotal += $items[$i]['OrderItem']['subtotal_not_formated'];
                            $v_transfer_fee += $items[$i]['OrderItem']['transfer_fee_not_formated'];
                            $v_commission_fee += $items[$i]['OrderItem']['commission_fee_not_formated'];
                            $v_total += $items[$i]['OrderItem']['total_not_formated'];
                            $v_saldo += $items[$i]['OrderItem']['saldo_not_formated'];
                        ?>
                            <tr class="<?php echo $items[$i]["OrderItem"]["working_days"] != $items[$i]["Order"]["working_days"] ? 'table-warning' : ''; ?>">
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" name="alt_linha" class="check_individual" id="">
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Supplier"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUser"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Benefit"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="hidden" class="item_id" value="<?php echo $items[$i]["OrderItem"]["id"]; ?>">
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["manual_quantity"] != 0 ? $items[$i]["OrderItem"]["manual_quantity"] : $items[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["price_per_day"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo $items[$i]["OrderItem"]["subtotal_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["transfer_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 commission_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["commission_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["saldo"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total_saldo"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_inicio_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_fim_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["status_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["motivo_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["pedido_operadora"]; ?></td>
                                <?php if ($order['Order']['status_id'] == 83) { ?>
                                    <td class="fw-bold fs-7 ps-4">
                                        <button class="btn btn-secondary btn-icon btn-sm" onclick="confirm('<h3>Deseja mesmo remover este benefício?</h3>', '<?php echo $this->base . '/orders/removeOrderItem/' . $items[$i]["OrderItem"]["order_id"] . '/' . $items[$i]["OrderItem"]["id"]; ?>')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>Total</td>
                            <td colspan="6"></td>
                            <td class="subtotal_sum">R$<?php echo number_format($v_subtotal, 2, ',', '.'); ?></td>
                            <td class="transfer_fee_sum">R$<?php echo number_format($v_transfer_fee, 2, ',', '.'); ?></td>
                            <td class="commission_fee_sum">R$<?php echo number_format($v_commission_fee, 2, ',', '.'); ?></td>
                            <td class="total_sum">R$<?php echo number_format($v_total, 2, ',', '.'); ?></td>
                            <td class="saldo_sum">R$<?php echo number_format($v_saldo, 2, ',', '.'); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="50">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
                </table>

                <?php echo $this->element("pagination"); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_alterar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
            </div>
            <div class="modal-body">
                <p>Alterar Status Processamento</p>

                <div class="row" style="margin-top:20px;">
                    <label class="mb-2">Status Processamento</label>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-custom form-check-solid">
                                <select name="status_processamento" id="status_processamento" class="form-select mb-3 mb-lg-0">
                                    <option value="ARQUIVO_GERADO">ARQUIVO_GERADO</option>
                                    <option value="CADASTRO_INCONSISTENTE">CADASTRO_INCONSISTENTE</option>
                                    <option value="CADASTRO_PROCESSADO">CADASTRO_PROCESSADO</option>
                                    <option value="CREDITO_INCONSISTENTE">CREDITO_INCONSISTENTE</option>
                                    <option value="CREDITO_PROCESSADO">CREDITO_PROCESSADO</option>
                                    <option value="FALHA_GERACAO_ARQUIVO">FALHA_GERACAO_ARQUIVO</option>
                                    <option value="GERAR_PAGAMENTO">GERAR_PAGAMENTO</option>
                                    <option value="INICIO_PROCESSAMENTO">INICIO_PROCESSAMENTO</option>
                                    <option value="PAGAMENTO_REALIZADO">PAGAMENTO_REALIZADO</option>
                                    <option value="PROCESSAMENTO_PENDENTE">PROCESSAMENTO_PENDENTE</option>
                                    <option value="VALIDACAO_PENDENTE">VALIDACAO_PENDENTE</option>
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
    function trigger_change() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const curr_sup = urlParams.get('sup');

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "reports", "action" => "getSupplierAndCustomer")); ?>',
            type: 'POST',
            data: { },
            success: function(data) {

                var obj = JSON.parse(data);
                var html = '<option value="">Selecione</option>';
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
                $("#sup").select2();
            }
        });
    }
    
    $(document).ready(function() {
        trigger_change();

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

            if ($(".check_all").is(':checked')) {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const order_id = <?php echo $id ?>;
                const v_status_processamento = $('#status_processamento').val();
                const curr_q = urlParams.get('q');
                const curr_sup = urlParams.get('sup');
                const curr_stp = urlParams.get('stp');

                $.ajax({
                    type: 'POST',
                    url: base_url+'/orders/alter_item_status_processamento_order_all',
                    data: {
                        order_id,
                        v_status_processamento,
                        curr_q,
                        curr_sup,
                        curr_stp
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            } else {
                const v_status_processamento = $('#status_processamento').val();
                const checkboxes = $('input[name="alt_linha"]:checked');
                const orderItemIds = [];

                checkboxes.each(function() {
                    orderItemIds.push($(this).parent().parent().find('.item_id').val());
                });

                if (orderItemIds.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: base_url+'/orders/alter_item_status_processamento',
                        data: {
                            orderItemIds,
                            v_status_processamento
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
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
