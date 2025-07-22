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
</style>

<?php echo $this->element("../Orders/_abas"); ?>

<?php echo $this->Form->create('Order', ["id" => "js-form-submit", 'class' => 'order-form', "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <!--begin::Order details-->
            <div class="card card-flush py-4 flex-row-fluid">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>
                            <a href="<?php echo Router::url(['controller' => 'Customers', 'action' => 'edit', $order['Customer']['id']]); ?>" class="customer-link">
                                <?php echo $order['Customer']['nome_secundario']; ?>
                            </a>
                        </h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                <!--begin::Date-->
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil002.svg-->
                                            <span class="svg-icon svg-icon-2 me-2">

                                            </span>
                                            <!--end::Svg Icon-->Dias Úteis
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['working_days']; ?></td>
                                </tr>
                                <!--end::Date-->
                                <!--begin::Payment method-->
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Período
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['order_period_from']; ?> a <?php echo $order['Order']['order_period_to']; ?></td>
                                </tr>
                                <!--end::Payment method-->
                                <!--begin::Date-->
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Liberação do crédito
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['credit_release_date']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Criado em
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['created']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Criado por
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Creator']['name'] != '' ? $order['Creator']['name'] : $order['CustomerCreator']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            N° Pedido
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['id']; ?></td>
                                </tr>
                                <?php if ($order['Order']['economic_group_id'] != null) { ?>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                Grupo Econômico
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end"><?php echo $order['EconomicGroup']['name']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Tipo Dias Úteis
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $order['Order']['working_days_type'] == 1 ? 'Padrão' : 'Cadastro de Beneficiários'; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Tipo Benefício
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $benefit_type_desc; ?></td>
                                </tr>
                                <?php if ($income && $income['Income']['data_pagamento'] != null) { ?>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                Data de pagamento
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end"><?php echo $income['Income']['data_pagamento']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Tipo
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $v_is_partial; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Gestão Eficiente
                                        </div>
                                    </td>
                                    <td class="fw-bolder">
                                        <div class="d-flex justify-content-end gap-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input pedido_complementar" type="radio" name="data[Order][pedido_complementar]" value="1" id="pedidoComp1" <?php echo (isset($order['Order']) ? ($order['Order']['pedido_complementar'] == 1 ? 'checked' : '') : '') ?> />
                                                <label class="form-check-label" for="pedidoComp1">
                                                    Sim
                                                </label>
                                            </div>

                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input pedido_complementar" type="radio" name="data[Order][pedido_complementar]" value="2" id="pedidoComp2" <?php echo (isset($order['Order']) ? ($order['Order']['pedido_complementar'] == 2 ? 'checked' : '') : '') ?> />
                                                <label class="form-check-label" for="pedidoComp2">
                                                    Não
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php if (!empty($order['Order']['updated_ge'])) { ?>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                Gestão Eficiente - Data Alteração
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end"><?php echo $order['Order']['updated_ge']; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if (!empty($order['UpdatedGe']['name'])) { ?>
                                    <tr>
                                        <td class="text-muted">
                                            <div class="d-flex align-items-center">
                                                Gestão Eficiente - Usuário Alteração
                                            </div>
                                        </td>
                                        <td class="fw-bolder text-end"><?php echo $order['UpdatedGe']['name']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Gera Nota Fiscal
                                        </div>
                                    </td>
                                    <td class="fw-bolder">
                                        <div class="d-flex justify-content-end gap-4">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input gera_nfse" type="radio" name="data[Order][gera_nfse]" value="1" id="geraNfse1" <?php echo (isset($order['Order']) ? ($order['Order']['gera_nfse'] == 1 ? 'checked' : '') : '') ?> />
                                                <label class="form-check-label" for="geraNfse1">
                                                    Sim
                                                </label>
                                            </div>

                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input gera_nfse" type="radio" name="data[Order][gera_nfse]" value="0" id="geraNfse2" <?php echo (isset($order['Order']) ? ($order['Order']['gera_nfse'] == 0 ? 'checked' : '') : '') ?> />
                                                <label class="form-check-label" for="geraNfse2">
                                                    Não
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
<!--                                <tr>-->
<!--                                    <td class="text-muted">-->
<!--                                        <div class="d-flex align-items-center">-->
<!--                                            Endereço Entrega-->
<!--                                        </div>-->
<!--                                    </td>-->
<!--                                    <td class="fw-bolder text-end">-->
<!--                                        --><?php //echo $order['CustomerAddress']['address']; ?><!-- <br>-->
<!--                                        --><?php //echo $order['CustomerAddress']['city_data']; ?>
<!--                                    </td>-->
<!--                                </tr>-->
                                <!--end::Date-->
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>

        </div>

        <div class="col-sm-12 col-md-8">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body pt-0 py-3">
                    <?php echo $this->element("aba_orders"); ?>
                </div>
                <!--end::Order details-->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-7 col js_pedido_complementar">
                            <label class="form-label">Observação GE</label>
                            <textarea 
                                name="data[Order][observation_ge]" 
                                class="form-control auto-expand" 
                                style="height: 155px;"
                            ><?php echo $order['Order']['observation_ge']; ?></textarea>
                        </div>


                        <div class="row mb-7">
                            <div class="col-12 col-md-6 mb-5">
                                <label class="form-label">Observação do Pedido</label>
                                <textarea 
                                    name="data[Order][observation]" 
                                    class="form-control auto-expand" 
                                    <?php echo $order['Order']['status_id'] >= 85 ? 'readonly' : ''; ?>
                                ><?php echo $order['Order']['observation']; ?></textarea>
                            </div>

                            <div class="col-12 col-md-6 mb-5">
                                <label class="form-label">Observação da Nota Fiscal</label>
                                <textarea 
                                    name="data[Order][nfse_observation]" 
                                    class="form-control auto-expand" 
                                    <?php echo $order['Order']['status_id'] >= 85 ? 'readonly' : ''; ?>
                                ><?php echo $order['Order']['nfse_observation']; ?></textarea>
                            </div>
                        </div>

                        <?php $is_dt_disabled = !($order['Order']['status_id'] == 85 || $order['Order']['status_id'] == 86 || $order['Order']['status_id'] == 104); ?>

                        <div class="row">
                            <div class="mb-7 col-4">
                                <label class="form-label">Data Finalização</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <?php echo $this->Form->input('end_date', array('type' => 'text', "id" => "conta", "placeholder" => "Data Finalização", "required" => false, "class" => "form-control mb-3 mb-lg-0 ". ($is_dt_disabled ? '' : 'datepicker'), 'readonly' => $is_dt_disabled)); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Vencimento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="text" name="data[Order][due_date]" id="OrderDueDate" required class="form-control <?php echo $order['Order']['status_id'] != 83 ? '' : 'OrderDueDate'; ?>" value="<?php echo $order['Order']['due_date']; ?>" <?php echo $order['Order']['status_id'] != 83 ? 'readonly' : ''; ?>>
                                </div>
                                <?php if (strtotime($order['Order']['due_date_nao_formatado']) < strtotime('today') && $order['Order']['status_id'] == 83) { ?>
                                    <p id="message_classification" style="color: red; margin: 0;">A data de vencimento não pode ser menor que a data de hoje</p>
                                <?php } ?>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Desconto</label>
                                <input type="text" name="data[Order][desconto]" id="OrderUnitPrice" class="form-control" value="<?php echo $order['Order']['desconto']; ?>" <?php echo $order['Order']['status_id'] >= 85 ? 'disabled="disabled"' : ''; ?>>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-12 col" style="text-align: right; margin-bottom: 10px !important;">
                                <div class="dropdown">
                                    <?php /*if ($order['Order']['status_id'] == 83) { ?>
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_desconto">
                                            Aplicar Desconto
                                        </a>
                                    <?php }*/ ?>

                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Relatórios e Ações
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="dropdownMenuButton">
                                        <div class="d-flex flex-column justify-content-start">

                                            <a href="<?php echo $this->base . '/orders/relatorio_beneficio/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Benefícios
                                            </a>
                                            <a href="<?php echo $this->base . '/orders/relatorio_processamento/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Processamento
                                            </a>
                                            <a href="<?php echo $this->base . '/orders/processamentopdf/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Processamento PDF
                                            </a>
                                            <a href="<?php echo $this->base . '/orders/listagem_entrega/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Entrega
                                            </a>
                                            <a href="<?php echo $this->base . '/orders/cobranca/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Cobrança
                                            </a>
                                            <a href="<?php echo $this->base . '/orders/resumo/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                <i class="fas fa-download"></i> Resumo
                                            </a>

                                          <!-- Verificação de status para mostrar os botões adicionais -->
                                            <?php if (($order['Order']['status_id'] == 83 || $order['Order']['status_id'] == 84) && $user['Group']['id'] == 1) { ?>
                                                <a href="#" class="btn btn-sm btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#modal_enviar_confirmado">
                                                    <i class="fas fa-arrow-right"></i> Pagamento Confirmado
                                                </a>
                                            <?php } ?>

                                            <?php if ($order['Order']['status_id'] == 83) { ?>
                                                <button type="button" class="btn btn-sm btn-success me-2 mb-2" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans" <?php echo strtotime($order['Order']['due_date_nao_formatado']) < strtotime('today') && $order['Order']['status_id'] == 83 ? 'disabled' : '' ?>>
                                                    <i class="fas fa-arrow-right"></i> Gerar Boleto
                                                </button>
                                            <?php } ?>

                                            <?php if ($order['Order']['status_id'] == 84 && $income) { ?>
                                                <a href="<?php echo $this->base . '/incomes/gerar_boleto/' . $income["Income"]["id"] . '/1'; ?>" class="btn btn-sm btn-success me-2 mb-2">
                                                    <i class="fas fa-download"></i> Baixar Boleto
                                                </a>
                                            <?php } ?>

                                            <?php if ($gerarNota && $order["Order"]["status_id"] != 83) { ?>
                                                <a href="<?php echo $this->base . '/orders/nota_debito/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-2 mb-2">
                                                    <i class="fas fa-download"></i> Nota de Débito
                                                </a>
                                            <?php } ?>

                                        </div>
                                    </div>

                                    <button type="submit" id="update-order" class="btn btn-sm btn-success me-3 js-salvar" style="padding: 11px 20px; font-size: 15px;" <?php echo ($order['Order']['status_id'] == 87) ? 'disabled="disabled"' : ''; ?>>
                                        Salvar dados
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!--begin::Row-->
<div class="row gy-5 g-xl-10 mt-1">
    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">


                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <div class="m-0">
                        <span class="fw-bold fs-1 text-gray-800">Beneficiários</span>
                    </div>
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2"><?php echo $usersCount; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->

                    <!--end::Follower-->
                </div>
                <!--end::Section-->
                <div class="m-0">
                    <span class="fw-bold fs-1 text-gray-800">Operadoras</span>
                </div>
                <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2"><?php echo $suppliersCount; ?></span>
                <!--end::Number-->
                <!--begin::Follower-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->


    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M18.4 5.59998C21.9 9.09998 21.9 14.8 18.4 18.3C14.9 21.8 9.2 21.8 5.7 18.3L18.4 5.59998Z" fill="currentColor" />
                            <path d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2ZM19.9 11H13V8.8999C14.9 8.6999 16.7 8.00005 18.1 6.80005C19.1 8.00005 19.7 9.4 19.9 11ZM11 19.8999C9.7 19.6999 8.39999 19.2 7.39999 18.5C8.49999 17.7 9.7 17.2001 11 17.1001V19.8999ZM5.89999 6.90002C7.39999 8.10002 9.2 8.8 11 9V11.1001H4.10001C4.30001 9.4001 4.89999 8.00002 5.89999 6.90002ZM7.39999 5.5C8.49999 4.7 9.7 4.19998 11 4.09998V7C9.7 6.8 8.39999 6.3 7.39999 5.5ZM13 17.1001C14.3 17.3001 15.6 17.8 16.6 18.5C15.5 19.3 14.3 19.7999 13 19.8999V17.1001ZM13 4.09998C14.3 4.29998 15.6 4.8 16.6 5.5C15.5 6.3 14.3 6.80002 13 6.90002V4.09998ZM4.10001 13H11V15.1001C9.1 15.3001 7.29999 16 5.89999 17.2C4.89999 16 4.30001 14.6 4.10001 13ZM18.1 17.1001C16.6 15.9001 14.8 15.2 13 15V12.8999H19.9C19.7 14.5999 19.1 16.0001 18.1 17.1001Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Icon-->

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['subtotal']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Subtotal</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M18.4 5.59998C21.9 9.09998 21.9 14.8 18.4 18.3C14.9 21.8 9.2 21.8 5.7 18.3L18.4 5.59998Z" fill="currentColor" />
                            <path d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2ZM19.9 11H13V8.8999C14.9 8.6999 16.7 8.00005 18.1 6.80005C19.1 8.00005 19.7 9.4 19.9 11ZM11 19.8999C9.7 19.6999 8.39999 19.2 7.39999 18.5C8.49999 17.7 9.7 17.2001 11 17.1001V19.8999ZM5.89999 6.90002C7.39999 8.10002 9.2 8.8 11 9V11.1001H4.10001C4.30001 9.4001 4.89999 8.00002 5.89999 6.90002ZM7.39999 5.5C8.49999 4.7 9.7 4.19998 11 4.09998V7C9.7 6.8 8.39999 6.3 7.39999 5.5ZM13 17.1001C14.3 17.3001 15.6 17.8 16.6 18.5C15.5 19.3 14.3 19.7999 13 19.8999V17.1001ZM13 4.09998C14.3 4.29998 15.6 4.8 16.6 5.5C15.5 6.3 14.3 6.80002 13 6.90002V4.09998ZM4.10001 13H11V15.1001C9.1 15.3001 7.29999 16 5.89999 17.2C4.89999 16 4.30001 14.6 4.10001 13ZM18.1 17.1001C16.6 15.9001 14.8 15.2 13 15V12.8999H19.9C19.7 14.5999 19.1 16.0001 18.1 17.1001Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Icon-->

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['transfer_fee']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Repasse</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['commission_fee']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Taxa</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['tpp_fee']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">TPP</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['desconto']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Desconto</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['total']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-sm-6 col-xl-2 mb-xl-10">
        <!--begin::Card widget 2-->
        <div class="card h-lg-100">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">

                <?php
                $total_economia = 0;
                $fee_economia = 0;
                $vl_economia = $order_balances_total[0][0]['total'];

                if ($order['Order']['fee_saldo_not_formated'] != 0 and $vl_economia != 0) {
                    $fee_economia = (($order['Order']['fee_saldo_not_formated'] / 100) * ($vl_economia));
                }

                $vl_economia = ($vl_economia - $fee_economia);
                $total_economia = ($vl_economia + $fee_economia);
                ?>

                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo number_format($vl_economia, 2, ',', '.'); ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Cliente</span>
                    </div>
                    <!--end::Follower-->
                    <br>
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo number_format($fee_economia, 2, ',', '.'); ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Fee Economia</span>
                    </div>
                    <!--end::Follower-->
                    <br>
                    <!--begin::Number-->
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo number_format($total_economia, 2, ',', '.'); ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Economia</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->

</div>


<div class="row">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="row">
                <div class="col-6">
                    <h3>Itens</h3>
                </div>
                <div class="col-6">
                    <?php if ($order['Order']['status_id'] == 83) { ?>
                        <a href="#" class="btn btn-sm btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_lote_usuarios">
                            <i class="fas fa-arrow-up"></i>
                            Beneficiários em lote (CSV)
                        </a>
                    <?php } ?>
                    <?php if ($order['Order']['is_partial'] == 3) { ?>
                        <a href="<?php echo $this->base . '/orders/baixar_beneficiarios/' . $id; ?>" class="btn btn-sm btn-primary me-3" style="float:right">
                            <i class="fas fa-file-excel"></i>
                            Baixar lista de Beneficiários - PIX
                        </a>
                    <?php } ?>
                    <?php if ($order['Order']['status_id'] == 83) { ?>
                        <a href="#" class="btn btn-sm btn-primary me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_add_beneficiarios">
                            <i class="fas fa-user"></i>
                            Novo Beneficiário
                        </a>
                        <a href="#" class="btn btn-sm btn-info me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_add_itinerario">
                            <i class="fas fa-bus"></i>
                            Novo Itinerário
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="table-responsive" id="search_form">
                <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "edit/" . $id . '#search_form')); ?>" role="form" id="busca" autocomplete="off">
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
                        <th width="90px">Dias Úteis</th>
                        <!--<th width="120px">Desconto</th>-->
                        <th width="120px">Quantidade por dia</th>
                        <th>Valor por dia</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Taxa</th>
                        <th class="<?php echo $order['Order']['status_id'] != 83 ? 'rounded-end' : '' ?>">Total</th>
                        <th>Economia</th>
                        <th>Repasse com Economia</th>
                        <th>Relatório beneficio</th>
                        <th>Repasse Compra</th>
                        <th>Data inicio Processamento</th>
                        <th>Data fim Processamento</th>
                        <th>Status Processamento</th>
                        <th>Motivo Processamento</th>
                        <th>Pedido Operadora</th>
                        <th>Primeiro Pedido</th>
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <th class="rounded-end"></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
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
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($order['Order']['status_id'] == 83) { ?>
                                        <input type="hidden" class="item_id" value="<?php echo $items[$i]["OrderItem"]["id"]; ?>">
                                        <input type="hidden" class="user_id" value="<?php echo $items[$i]["OrderItem"]["customer_user_id"]; ?>">
                                        <input type="number" class="form-control working_days_input" value="<?php echo $items[$i]["OrderItem"]["working_days"]; ?>">
                                    <?php } else { ?>
                                        <?php echo $items[$i]["OrderItem"]["working_days"]; ?>
                                    <?php } ?>
                                </td>
                                <!--<td class="fw-bold fs-7 ps-4">
                                    <?php if ($order['Order']['status_id'] == 83) { ?>
                                        <input type="text" class="form-control money_field var_days_input" value="<?php echo $items[$i]["OrderItem"]["var"]; ?>">
                                    <?php } else { ?>
                                        <?php echo $items[$i]["OrderItem"]["var"]; ?>
                                    <?php } ?>
                                </td> !-->
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["manual_quantity"] != 0 ? $items[$i]["OrderItem"]["manual_quantity"] : $items[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["valor_unit"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo $items[$i]["OrderItem"]["subtotal_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["transfer_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 commission_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["commission_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["saldo"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 saldo_transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["saldo_transfer_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["saldo_transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total_saldo"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 repasse_compra_line" data-valor="<?php echo $repasse_compra; ?>"><?php echo 'R$' . number_format($repasse_compra, 2, ',', '.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_inicio_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_fim_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["status_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["motivo_processamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["pedido_operadora"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["first_order"] == 1 ? 'Sim' : 'Não'; ?></td>

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

<div class="modal fade" tabindex="-1" id="modal_enviar_sptrans" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/changeStatusToSent/' . $id; ?>" class="form-horizontal" method="post">
                <div class="modal-body">
                    <p>Tem certeza que deseja gerar o boleto?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success js-salvar">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_enviar_confirmado" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/confirma_pagamento/' . $id; ?>" class="form-horizontal" method="post">
                <div class="modal-body">
                    <p>Tem certeza que deseja confirmar o pagamento?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success js-salvar">Sim</button>
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
                    <select name="customer_user_id" id="customer_user_id" class="form-select mb-3 mb-lg-0">
                        <option value="">Selecione...</option>
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


<div class="modal fade" id="modal_add_itinerario" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Incluir Itinerário</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('CustomerUserItinerary', ['id' => 'js-form-submit', 'url' => '/orders/addItinerary', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
                <input type="hidden" name="customer_id" value="<?php echo $order['Order']['customer_id']; ?>">
                <input type="hidden" name="order_id" value="<?php echo $id; ?>">

                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Beneficiário</label>
                        <?php echo $this->Form->input('customer_user_id', array("id" => "customer_user_id_iti", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-placeholder" => "Selecione", "data-allow-clear" => "true")); ?>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Benefício</label>
                        <?php echo $this->Form->input('benefit_id', array("id" => "benefit_id", "required" => true, "class" => "form-select form-select-solid fw-bolder", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'empty' => 'Selecione')); ?>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                        <?php echo $this->Form->input('working_days', array("id" => "working_days",  "placeholder" => "Dias Úteis", "required" => true, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Valor Unitário</label>
                        <?php echo $this->Form->input('unit_price', array("id" => "unit_price", 'type' => 'text', "placeholder" => "Valor Unitário", "required" => true, "class" => "form-control mb-3 mb-lg-0 money_field"));  ?>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Quantidade</label>
                        <?php echo $this->Form->input('quantity', array("id" => "quantity", "placeholder" => "Quantidade", "required" => true, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Valor por dia</label>
                        <?php echo $this->Form->input('price_per_day', array("id" => "price_per_day", 'type' => 'text', "placeholder" => "Valor por dia", "required" => true, "class" => "form-control mb-3 mb-lg-0 money_field", 'disabled'));  ?>
                    </div>
                </div>

                <div class="mb-7">
                    <div class="col-sm-offset-2 col-sm-9">
                        <button type="button" class="btn btn-light-dark" tabindex="-1" data-bs-dismiss="modal" aria-label="Fechar">Fechar</button>
                        <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_lote_usuarios" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/upload_user_csv/' . $id; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $order['Order']['customer_id']; ?>">
                <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <div class="row" style="margin-bottom:20px;">
                        <label class="mb-2">Tipo Importação</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[tipo_importacao]" value="2" id="tipoBeneficioChk1" checked="checked" />
                                    <label class="form-check-label" for="tipoBeneficioChk1">
                                        Simplificada <span class="badge badge-warning">Atual</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[tipo_importacao]" value="1" id="tipoBeneficioChk2" />
                                    <label class="form-check-label" for="tipoBeneficioChk2">
                                        Completa <span class="badge badge-success">Novo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>Enviar CSV com beneficiários a serem incluídos</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>

                    <div class="row" style="margin-top:20px;display:none">
                        <label class="mb-2">Item Variável no Pedido</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[incluir_valor_unitario]" value="2" id="tipoBeneficioChk1" checked="checked" />
                                    <label class="form-check-label" for="tipoBeneficioChk1">
                                        Não
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[incluir_valor_unitario]" value="1" id="tipoBeneficioChk2" />
                                    <label class="form-check-label" for="tipoBeneficioChk2">
                                        Sim
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a class="btn btn-info btn-sm" style="font-size: 10px" href="<?php echo $this->base; ?>/files/ModeloImportacaoBeneficiariosLote.csv" target="_blank" download>Modelo Simplificada</a>
                        <a class="btn btn-info btn-sm" style="font-size: 10px" href="<?php echo $this->base; ?>/files/ModeloCompletoImportacaoBeneficiariosLote.csv" target="_blank" download>Modelo Completo</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Sim</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_excluir_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Excluir items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="excluir_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_desconto" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_desconto_label">Selecionar Pedidos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="mb-7 col-4">
                    <label class="form-label">Total</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="total_desconto" id="total_desconto" class="form-control" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <?php echo $this->element("table"); ?>
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 w-50px min-w-50px rounded-start"></th>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Data de criação</th>
                                <th>Desconto</th>
                                <th class="rounded-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders) { ?>
                                <?php for ($i=0; $i < count($orders); $i++) { ?>
                                    <tr>
                                        <td class="fw-bold fs-7 ps-4"><input type="checkbox" class="seletor-item" data-desconto="<?php echo $orders[$i]["Order"]["desconto_not_formated"]; ?>" <?php echo $orders[$i]["OrderDiscount"]["id"] ? "checked" : ""; ?> ></td>
                                        <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["Order"]["id"]; ?></td>
                                        <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["Customer"]["nome_primario"]; ?></td>
                                        <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["Order"]["created"] ?></td>
                                        <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $orders[$i]["Order"]["desconto"]; ?></td>
                                        <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $orders[$i]["Order"]["subtotal"]; ?></td>
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
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button id="enviar_desconto" class="btn btn-success">Salvar</button>
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

        $("#js-form-submit.order-form").submit(function (event) {
            const gera_nfse = $('[name="data[Order][gera_nfse]"]:checked').val();
            const nfse_obs = $('[name="data[Order][nfse_observation]"]').val().trim();

            if (gera_nfse === '0' && nfse_obs === '') {
                event.preventDefault();
                alert('Preencha o campo "Observações da Nota fiscal" para atualizar o pedido.')
            }
        })

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