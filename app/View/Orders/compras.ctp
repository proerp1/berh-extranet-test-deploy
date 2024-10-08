<?php echo $this->element("../Orders/_abas"); ?>

<div class="row">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="row">
                <div class="col-6">
                    <h3>Itens</h3>
                </div>
                <div class="col-6">
                    <a href="#" id="alterar_sel" class="btn btn-sm btn-primary me-3 mb-3" style="float:right">
                        <i class="fas fa-edit"></i>
                        Alterar Status Processamento
                    </a>
                </div>
            </div>
            <div class="table-responsive" id="search_form">
                <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "compras/" . $id . '#search_form')); ?>" role="form" id="busca" autocomplete="off">
                    <div class="card-header border-0 pt-6 pb-6" style="padding-left: 0px;">
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
                        <!--<th width="120px">Desconto</th>-->
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
                    <tr>
                    <td>Total</td>
                        <td colspan="6"></td>
                        <td class="subtotal_sum">R$<?php echo $order['Order']['subtotal']; ?></td>
                        <td class="transfer_fee_sum">R$<?php echo $order['Order']['transfer_fee']; ?></td>
                        <td class="commission_fee_sum">R$<?php echo $order['Order']['commission_fee']; ?></td>
                        <td class="total_sum">R$<?php echo $order['Order']['total']; ?></td>
                        <td class="saldo_sum">R$<?php echo $order['Order']['saldo']; ?></td>
                        <td class="total_saldo_sum">R$<?php echo $order['Order']['total_saldo']; ?></td>
                        <td colspan="6"></td>
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <td>&nbsp;</td>
                        <?php } ?>
                    </tr>
                    <?php
                    $subtotal = 0;
                    $transfer_fee = 0;
                    $total = 0;
                    if ($items) { ?>
                        <?php for ($i = 0; $i < count($items); $i++) {
                            $subtotal += $items[$i]["OrderItem"]["subtotal_not_formated"];
                            $transfer_fee += $items[$i]["OrderItem"]["transfer_fee_not_formated"];
                            $total += $items[$i]["OrderItem"]["total_not_formated"];
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
                                <!--<td class="fw-bold fs-7 ps-4">
                                    <?php if ($order['Order']['status_id'] == 83) { ?>
                                        <input type="text" class="form-control money_field var_days_input" value="<?php echo $items[$i]["OrderItem"]["var"]; ?>">
                                    <?php } else { ?>
                                        <?php echo $items[$i]["OrderItem"]["var"]; ?>
                                    <?php } ?>
                                </td> !-->
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
                            <td class="subtotal_sum">R$<?php echo $order['Order']['subtotal']; ?></td>
                            <td class="transfer_fee_sum">R$<?php echo $order['Order']['transfer_fee']; ?></td>
                            <td class="commission_fee_sum">R$<?php echo $order['Order']['commission_fee']; ?></td>
                            <td class="total_sum">R$<?php echo $order['Order']['total']; ?></td>
                            <td class="saldo_sum">R$<?php echo $order['Order']['saldo']; ?></td>
                            <td class="total_saldo_sum">R$<?php echo $order['Order']['total_saldo']; ?></td>
                            <td colspan="6"></td>
                            <?php if ($order['Order']['status_id'] == 83) { ?>
                                <td>&nbsp;</td>
                            <?php } ?>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Alterar Status Processamento</p>

                <div class="row" style="margin-top:20px;">
                    <label class="mb-2">Status Processamento</label>
                    <div class="row">
                        <div class="col">
                            <div class="form-check form-check-custom form-check-solid">
                                <select name="status_processamento" id="status_processamento" class="form-select mb-3 mb-lg-0">
                                    <option value="INICIO_PROCESSAMENTO">INICIO_PROCESSAMENTO</option>
                                    <option value="ARQUIVO_GERADO">ARQUIVO_GERADO</option>
                                    <option value="CADASTRO_PROCESSADO">CADASTRO_PROCESSADO</option>
                                    <option value="CADASTRO_INCONSISTENTE">CADASTRO_INCONSISTENTE</option>
                                    <option value="CREDITO_PROCESSADO">CREDITO_PROCESSADO</option>
                                    <option value="CREDITO_INCONSISTENTE">CREDITO_INCONSISTENTE</option>
                                    <option value="FALHA_GERACAO_ARQUIVO">FALHA_GERACAO_ARQUIVO</option>
                                    <option value="PROCESSAMENTO_PENDENTE">PROCESSAMENTO_PENDENTE</option>
                                    <option value="GERAR_PAGAMENTO">GERAR_PAGAMENTO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="alterar_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
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

            const orderId = <?php echo $id; ?>;
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
                        orderId,
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
