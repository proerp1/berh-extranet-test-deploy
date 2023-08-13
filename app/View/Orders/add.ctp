<script type="text/javascript">
    $(document).ready(function() {
        $("#OrderLastFareUpdate").datepicker({
            language: "pt-BR",
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('#OrderUnitPrice').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>

<?php echo $this->Form->create('Order', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
<div class="row">
    <div class="col-sm-12 col-md-9">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-0 py-3">
                <?php echo $this->element("aba_orders"); ?>
            </div>
            <div class="card-body pt-7 py-3">

                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Cliente</label>
                        <p><?php echo $order['Customer']['nome_primario']; ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Período</label>
                        <p><?php echo $order['Order']['order_period']; ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Dias Úteis</label>
                        <p><?php echo $order['Order']['working_days']; ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Subtotal</label>
                        <p>R$<?php echo $order['Order']['subtotal']; ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Repasse</label>
                        <p>R$<?php echo $order['Order']['transfer_fee']; ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Taxa</label>
                        <p>R$<?php echo $order['Order']['commission_fee']; ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Total</label>
                        <p>R$<?php echo $order['Order']['total']; ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Criado em</label>
                        <p><?php echo date('d/m/Y', strtotime($order['Order']['created'])); ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Criado por</label>
                        <p><?php echo $order['Creator']['name'] != '' ? $order['Creator']['name'] : $order['CustomerCreator']['name']; ?></p>
                    </div>
                </div>

                <?php if ($order['Order']['status_id'] == 83) { ?>
                    <div class="row">
                        <div class="col-12">
                            <a href="#" class="btn btn-sm btn-primary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans">
                                <i class="fas fa-arrow-right"></i>
                                Enviar SPTrans
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($order['Order']['status_id'] == 85) { ?>
                    <div class="row">
                        <div class="col-12">
                            <a href="#" class="btn btn-sm btn-primary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_gera_boleto">
                                <i class="fas fa-file"></i>
                                Gerar Boleto
                            </a>
                        </div>
                    </div>
                <?php } ?>


            </div>


        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Total</label>
                    <p>R$<?php echo $order['Order']['total']; ?></p>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Observação da Nota Fiscal</label>
                    <textarea name="data[Order][observation]" id="" class="form-control" style="height: 175px;" <?php echo $order['Order']['status_id'] >= 86 ? 'disabled="disabled"' : ''; ?>><?php echo $order['Order']['observation']; ?></textarea>
                </div>

                <div class="mb-7 col">
                    <button type="submit" class="btn btn-success" style="float:right" <?php echo $order['Order']['status_id'] >= 86 ? 'disabled="disabled"' : ''; ?>>Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<input type="hidden" id="order_id" value="<?php echo $order['Order']['id']; ?>">

<div class="row mt-10">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="row">
                <div class="col-9">
                    <h3>Itens</h3>
                </div>
                <?php if ($order['Order']['status_id'] == 83) { ?>
                    <div class="col-3">
                        <a href="#" class="btn btn-sm btn-primary me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_add_beneficiarios">
                            <i class="fas fa-file"></i>
                            Novo Beneficiário
                        </a>
                    </div>
                <?php } ?>

            </div>
            <div class="table-responsive">
                <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th>Status SPTrans</th>
                        <th>Beneficiário</th>
                        <th>Benefício</th>
                        <th width="80px">Dias Úteis</th>
                        <th>Valor por dia</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
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
                                <td class="fw-bold fs-7 ps-4">Pendente</td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUser"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserItinerary"]["benefit_name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($order['Order']['status_id'] == 83) { ?>
                                        <input type="hidden" class="item_id" value="<?php echo $items[$i]["OrderItem"]["id"]; ?>">
                                        <input type="hidden" class="user_id" value="<?php echo $items[$i]["OrderItem"]["customer_user_id"]; ?>">
                                        <input type="number" class="form-control working_days_input" value="<?php echo $items[$i]["OrderItem"]["working_days"]; ?>">
                                    <?php } else { ?>
                                        <?php echo $items[$i]["OrderItem"]["working_days"]; ?>
                                    <?php } ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["CustomerUserItinerary"]["price_per_day"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo $items[$i]["OrderItem"]["subtotal_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["transfer_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><button class="btn btn-secondary btn-sm" onclick="confirm('<h3>Deseja mesmo remove este beneficiário?</h3>', '<?php echo $this->base.'/orders/removeOrderItem/'.$items[$i]["OrderItem"]["order_id"].'/'.$items[$i]["OrderItem"]["customer_user_id"]; ?>')"><i class="fa fa-times"></i></button></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="5"></td>
                            <td id="subtotal_sum">R$<?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                            <td id="transfer_fee_sum">R$<?php echo number_format($transfer_fee, 2, ',', '.'); ?></td>
                            <td id="total_sum">R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                        </tr>
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
</div>


<div class="modal fade" tabindex="-1" id="modal_enviar_sptrans" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/changeStatusToSent/' . $id; ?>" class="form-horizontal" method="post">
                <div class="modal-body">
                    <p>Tem certeza que deseja enviar o pedido para a SPTrans?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_gera_boleto" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/changeStatusIssued/' . $id; ?>" class="form-horizontal" method="post">
                <div class="modal-body">
                    <p>Tem certeza que deseja gerar a conta a receber e o boleto?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_add_beneficiarios" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Incluir Beneficiário</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/addCustomerUserToOrder/'; ?>" class="form-horizontal" method="post">
                <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                <input type="hidden" name="working_days" value="<?php echo $order['Order']['working_days']; ?>">
                <div class="modal-body">
                    <label for="customer_user_id">Beneficário</label>
                    <select name="customer_user_id" id="customer_user_id" class="form-select mb-3 mb-lg-0" data-control="select2">
                        <option value="">Selecione...</option>
                        <?php foreach ($customer_users_pending as $k => $user) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $user; ?></option>
                        <?php } ?>
                    </select>
                    <p class="mt-3">Esta lista contém somente os beneficiários ainda não adicionados ao pedido. Para cadastrar novos beneficiários clique <a href="<?php echo $this->base; ?>/customer_users/index/<?php echo $order['Order']['customer_id']; ?>" target="_blank">aqui</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Incluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.working_days_input').on('change', function() {
            const newValue = $(this).val();
            const orderItemId = $(this).parent().find('.item_id').val();
            const line = $(this).parent().parent();

            if (newValue != '' && newValue != undefined && newValue != null) {
                $.ajax({
                    type: 'POST',
                    url: <?php echo $this->base; ?> '/orders/updateWorkingDays', // Adjust the URL to your CakePHP action
                    data: {
                        newValue,
                        orderItemId
                    },
                    dataType: 'json',
                    success: function(response) {
                        line.find('.total_line').html('R$' + response.total);
                        line.find('.subtotal_line').html('R$' + response.subtotal);
                        line.find('.transfer_fee_line').html('R$' + response.transfer_fee);

                        $('#subtotal_sum').html('R$' + response.pedido_subtotal);
                        $('#transfer_fee_sum').html('R$' + response.pedido_transfer_fee);
                        $('#total_sum').html('R$' + response.pedido_total);
                    }
                });
            }
        });

        $('.remove_line').on('click', function() {
            const orderItemId = $(this).parent().parent().find('.item_id').val();
            const userId = $(this).parent().parent().find('.user_id').val();

            if (orderItemId != '' && orderItemId != undefined && orderItemId != null) {
                $.ajax({
                    type: 'POST',
                    url: <?php echo $this->base; ?> '/orders/removeOrderItem', // Adjust the URL to your CakePHP action
                    data: {
                        orderItemId,
                        userId
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
    })
</script>