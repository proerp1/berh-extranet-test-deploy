<script type="text/javascript">
    $(document).ready(function() {
        $("#OrderLastFareUpdate").datepicker({
            language: "pt-BR",
            format: 'dd/mm/yyyy',
            daysOfWeekDisabled: [0, 6],
            autoclose: true
        });

        $(".OrderDueDate").datepicker({
            format: 'dd/mm/yyyy',
            weekStart: 1,
            startDate: "today",
            orientation: "bottom auto",
            autoclose: true,
            language: "pt-BR",
            todayHighlight: true,
            daysOfWeekDisabled: [0, 6],
            toggleActive: true
        });

        $('.OrderDueDate').mask('99/99/9999');

        $('#OrderUnitPrice').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>

<style>
    tbody tr th:first-child {
        padding: 0px 10px !important;
    }
    .working_days_input {
        width: 60px;
    }

    .customer-link {
        color: #0082d2;
        text-decoration: none;
    }

    .customer-link:hover {
        color: #ED0677;
    }

    .nowrap {
        white-space: nowrap;
    }
</style>

<?php echo $this->element("abas_outcomes", array('id' => $id)); ?>

<div class="row">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="row">
                <div class="col-3">
                    <h3>Itens</h3>
                </div>
                <div class="col-9">
                    <?php if ($order['Order']['is_partial'] == 3) { ?>
                        <?php if (count($pendingPix)) { ?>
                            <a href="<?php echo $this->base . '/outcomes/enviar_btg/' . $id; ?>" class="btn btn-sm btn-success me-3" style="float:right">
                                <i class="fas fa-dollar-sign"></i>
                                Liberar PIX
                            </a>
                        <?php } ?>
                        <a href="<?php echo $this->base . '/orders/baixar_beneficiarios/' . $order['Order']['id']; ?>" class="btn btn-sm btn-primary me-3" style="float:right">
                            <i class="fas fa-file-excel"></i>
                            Baixar lista de Beneficiários - PIX
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="table-responsive" id="search_form">
                <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "payments/" . $id . '#search_form')); ?>" role="form" id="busca" autocomplete="off">
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

                <div class="row">
                    <div class="col-11" style="width: 88%">
                        <?php echo $this->element("pagination"); ?>
                    </div>
                    <div class="col-1" style="width: 12%">
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <a href="#" id="excluir_sel" class="btn btn-danger btn-sm" style="float:right; margin-bottom: 10px">Excluir Selecionados</a>
                        <?php } ?>
                    </div>
                </div>


                <?php echo $this->element("table"); ?>
                <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-80px min-w-80px rounded-start">
                        <input type="checkbox" class="check_all">
                    </th>
                    <th>Beneficiário</th>
                    <th>Benefício</th>
                    <th>Valor</th>
                    <th>Data Pagamento</th>
                    <th>Tipo Chave</th>
                    <th>Chave</th>
                    <th>Status Pix</th>
                    <th>Data Envio Banco</th>
                    <th>Cod Banco</th>
                    <th>Nome Banco</th>
                    <th>Conta Corrente</th>
                    <th>Digito</th>
                    <th>Agência</th>
                    <th>Digito</th>
                    <th>Ações</th>
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

                        $repasse_compra = ($items[$i]["OrderItem"]["transfer_fee_not_formated"] - $items[$i]["OrderItem"]["saldo_transfer_fee_not_formated"]);
                        ?>
                        <tr class="<?php echo $items[$i]["OrderItem"]["working_days"] != $items[$i]["Order"]["working_days"] ? 'table-warning' : ''; ?>">
                            <td class="fw-bold fs-7 ps-4">
                                <input type="checkbox" name="del_linha" class="check_individual" id="">
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Benefit"]["name"]; ?></td>
                            <!--Valor--> <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo $items[$i]["OrderItem"]["subtotal_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                            <!--Data de Pagamento--> <td class="fw-bold fs-7 ps-4"><?php echo date("d/m/Y", strtotime($items[$i]['Outcome']['data_pagamento'])); ?></td>
                            <?php $pix_types = ['' => '-', 'cnpj' => 'CNPJ', 'cpf' => 'CPF', 'email' => 'E-mail', 'celular' => 'Celular', 'chave' => 'Chave', 'qr code' => 'Qr Code', 'aleatoria' => 'Aleatória'] ?>
                            <!--Tipo Chave--> <td class="fw-bold fs-7 ps-4"><?php echo $pix_types[$items[$i]["CustomerUserBankAccounts"]["pix_type"]]; ?></td>
                            <!--Chave--> <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserBankAccounts"]["pix_id"]; ?></td>
                            <!--Status Pix--> <td class="fw-bold fs-7 ps-4">
                                <span class="badge <?php echo $items[$i]["PixStatus"]["label"]; ?>">
                                    <?php echo $items[$i]["PixStatus"]["name"]; ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["bank_sent_date"] ? date("d/m/Y", strtotime($items[$i]['OrderItem']['bank_sent_date'])) : '-'; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["BankCode"]["code"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["BankCode"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserBankAccounts"]["acc_number"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserBankAccounts"]["acc_digit"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserBankAccounts"]["branch_number"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserBankAccounts"]["branch_digit"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 nowrap">
                              <?php if ($items[$i]['PixStatus']['id'] == 111) { ?>
                                  <a href="<?php echo $this->base.'/outcomes/marcar_pix_pago/'.$items[$i]['OrderItem']['id'] ?>" class="btn btn-success btn-sm">Pago</a>
                              <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Total</td>
                        <td colspan="5"></td>
                        <td class="subtotal_sum">R$<?php echo $order['Order']['subtotal']; ?></td>
                        <td class="transfer_fee_sum">R$<?php echo $order['Order']['transfer_fee']; ?></td>
                        <td class="commission_fee_sum">R$<?php echo $order['Order']['commission_fee']; ?></td>
                        <td class="total_sum">R$<?php echo $order['Order']['total']; ?></td>
                        <td class="saldo_sum">R$<?php echo $order['Order']['saldo']; ?></td>
                        <td class="saldo_transfer_fee_sum">R$<?php echo $order['Order']['saldo_transfer_fee']; ?></td>
                        <td class="total_saldo_sum">R$<?php echo $order['Order']['total_saldo']; ?></td>
                        <td class="repasse_compra_sum">R$<?php echo number_format(($order["Order"]["transfer_fee_not_formated"] - $order["Order"]["saldo_transfer_fee_not_formated"]), 2, ',', '.'); ?></td>
                        <td colspan="5"></td>
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <td>&nbsp;</td>
                        <?php } ?>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="<?php echo $order['Order']['status_id'] == 83 ? 10 : 9 ?>">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
                </tbody>
                </table>

                <?php echo $this->element("pagination"); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
<?php echo $this->Html->script('itinerary'); ?>

<script>
    $(document).ready(function() {
        var should_scroll = <?php echo isset($this->params['named']['page']) ? 'true' : 'false'; ?>;
        if (should_scroll) {
            $('html, body').animate({
                scrollTop: $("#excluir_sel").offset().top - 150
            }, 100);
        }
        $('.money_field').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $('#benefit_id').select2({
            dropdownParent: $('#modal_add_itinerario')
        });

        $('.working_days_input').on('change', function() {
            const newValue = $(this).val();
            const orderItemId = $(this).parent().find('.item_id').val();
            const line = $(this).parent().parent();

            if (newValue.includes('-')) {
                $(this).val(0);
                alert('número negativo não permitido');
                return;
            }

            if (newValue != '' && newValue != undefined && newValue != null) {
                $.ajax({
                    type: 'POST',
                    url: base_url + '/orders/updateWorkingDays', // Adjust the URL to your CakePHP action
                    data: {
                        newValue,
                        orderItemId,
                        'campo': 'working_days'
                    },
                    dataType: 'json',
                    success: function(response) {
                        line.find('.total_line').html('R$' + response.total);
                        line.find('.subtotal_line').html('R$' + response.subtotal);
                        line.find('.transfer_fee_line').html('R$' + response.transfer_fee);
                        line.find('.commission_fee_line').html('R$' + response.commission_fee);

                        $('.subtotal_sum').html('R$' + response.pedido_subtotal);
                        $('.transfer_fee_sum').html('R$' + response.pedido_transfer_fee);
                        $('.commission_fee_sum').html('R$' + response.pedido_commission_fee);
                        $('.total_sum').html('R$' + response.pedido_total);
                    }
                });
            }
        });

        $('.var_days_input').on('change', function() {
            let newValue = $(this).val();
            const orderItemId = $(this).parent().parent().find('.item_id').val();
            const line = $(this).parent().parent();

            if (newValue.includes('-')) {
                $(this).val('0,00');
                alert('número negativo não permitido');
                return;
            }

            if (newValue == '') {
                newValue = 0;
                $(this).val('0,00');
            }

            $.ajax({
                type: 'POST',
                url: base_url + '/orders/updateWorkingDays', // Adjust the URL to your CakePHP action
                data: {
                    newValue,
                    orderItemId,
                    'campo': 'var'
                },
                dataType: 'json',
                success: function(response) {
                    line.find('.total_line').html('R$' + response.total);
                    line.find('.subtotal_line').html('R$' + response.subtotal);
                    line.find('.transfer_fee_line').html('R$' + response.transfer_fee);
                    line.find('.commission_fee_line').html('R$' + response.commission_fee);

                    $('.subtotal_sum').html('R$' + response.pedido_subtotal);
                    $('.transfer_fee_sum').html('R$' + response.pedido_transfer_fee);
                    $('.commission_fee_sum').html('R$' + response.pedido_commission_fee);
                    $('.total_sum').html('R$' + response.pedido_total);
                }
            });
        });

        $('.remove_line').on('click', function() {
            const orderItemId = $(this).parent().parent().find('.item_id').val();
            const userId = $(this).parent().parent().find('.user_id').val();

            if (orderItemId != '' && orderItemId != undefined && orderItemId != null) {
                $.ajax({
                    type: 'POST',
                    url: base_url + '/orders/removeOrderItem', // Adjust the URL to your CakePHP action
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

        $('#customer_user_id').select2({
            ajax: {
                url: base_url + '/orders/listOfCustomerUsers',
                dataType: 'json',
                data: function(params) {
                    var query = {
                        search: params.term,
                        customer_id: <?php echo $order['Order']['customer_id']; ?>
                    }

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            },
            dropdownParent: $('#modal_add_beneficiarios')
        });

        $('#customer_user_id_iti').select2({
            ajax: {
                url: base_url + '/orders/listOfCustomerUsers',
                dataType: 'json',
                data: function(params) {
                    var query = {
                        search: params.term,
                        customer_id: <?php echo $order['Order']['customer_id']; ?>
                    }

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            },
            dropdownParent: $('#modal_add_itinerario')
        });

        $('#excluir_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="del_linha"]:checked').length > 0) {
                $('#modal_excluir_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a ser excluído');
            }
        });

        $('#excluir_confirm').on('click', function(e) {
            e.preventDefault();

            const orderId = <?php echo $id; ?>;
            const checkboxes = $('input[name="del_linha"]:checked');
            const orderItemIds = [];

            checkboxes.each(function() {
                orderItemIds.push($(this).parent().parent().find('.item_id').val());
            });

            if (orderItemIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url + '/orders/removeOrderItem',
                    data: {
                        orderItemIds,
                        orderId
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

        $(".check_all").on("change", function() {
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });

        function fnc_calc_total() {
            let total = 0;

            $('.seletor-item:checked').each(function () {
                total += parseFloat($(this).data('desconto'));
            });


            $('#total_desconto').val(total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).replace("R$", "").trim());
        }

        fnc_calc_total();

        $('.seletor-item').on('change', function () {
            fnc_calc_total();
        });

        $('#enviar_desconto').on('click', function () {
            const order_id = <?php echo $id; ?>;
            let total_desconto = $('#total_desconto').val();
            let orders_select = [];

            $('.seletor-item:checked').each(function () {
                let linha = $(this).closest('tr');
                let order_parent = linha.find('td:eq(1)').text().trim();

                orders_select.push({
                    order_parent: order_parent,
                });
            });

            $.ajax({
                type: 'POST',
                url: base_url + '/orders/aplicar_desconto',
                data: {
                    order_id,
                    total_desconto,
                    orders_select
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (err) {
                    alert('Erro ao enviar os dados');
                }
            });
        });

        $('.pedido_complementar').on('click', function () {
            $('.js_pedido_complementar textarea').prop('required', true);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textareas = document.querySelectorAll('.auto-expand');

        textareas.forEach(textarea => {
            // inicializa com altura ajustada
            textarea.style.height = 'auto';
            textarea.style.overflowY = 'hidden';
            textarea.style.height = textarea.scrollHeight + 'px';

            // atualiza ao digitar
            textarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    });
</script>