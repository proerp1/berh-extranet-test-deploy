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

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base; ?>/orders/edit/<?php echo $id; ?>">Pedido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base; ?>/orders/boletos/<?php echo $id; ?>">Boletos</a>
    </li>
</ul>

<?php echo $this->Form->create('Order', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

<div class="row">
    <div class="col-sm-12 col-md-4">
        <!--begin::Order details-->
        <div class="card card-flush py-4 flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2><?php echo $order['Customer']['nome_secundario']; ?></h2>
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
                                        <!--begin::Svg Icon | path: icons/duotune/files/fil002.svg-->
                                        <span class="svg-icon svg-icon-2 me-2">

                                        </span>
                                        <!--end::Svg Icon-->N° Pedido
                                    </div>
                                </td>
                                <td class="fw-bolder text-end"><?php echo $order['Order']['id']; ?></td>
                            </tr>
                            <?php if ($order['Order']['economic_group_id'] != null) { ?>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil002.svg-->
                                            <span class="svg-icon svg-icon-2 me-2">

                                            </span>
                                            <!--end::Svg Icon-->Grupo Econômico
                                        </div>
                                    </td>
                                    <td class="fw-bolder text-end"><?php echo $economic_group['EconomicGroup']['name']; ?></td>
                                </tr>
                            <?php } ?>
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



                    <div class="mb-7 col">
                        <label class="form-label">Observação da Nota Fiscal</label>
                        <textarea name="data[Order][observation]" id="" class="form-control" style="height: 175px;" <?php echo $order['Order']['status_id'] >= 85 ? 'disabled="disabled"' : ''; ?>><?php echo $order['Order']['observation']; ?></textarea>
                    </div>

                    <?php $is_dt_disabled = (($order['Order']['status_id'] == 85 || $order['Order']['status_id'] == 86) ? '' : 'disabled'); ?>

                    <div class="row">
                        <div class="mb-7 col-6">
                            <label class="form-label">Data Finalização</label>
                            <?php echo $this->Form->input('end_date', array('type' => 'text', "id" => "conta", "placeholder" => "Data Finalização", "required" => false, "class" => "form-control mb-3 mb-lg-0 datepicker", 'disabled' => $is_dt_disabled)); ?>
                        </div>

                        <div class="mb-7 col-6">
                            <label class="form-label">Desconto</label>
                            <input type="text" name="data[Order][desconto]" id="OrderUnitPrice" class="form-control" value="<?php echo $order['Order']['desconto']; ?>" <?php echo $order['Order']['status_id'] >= 85 ? 'disabled="disabled"' : ''; ?>>
                        </div>

                    </div>


                    <div class="row">
                        <div class="mb-7 col" style="text-align: right;">
                            <?php if ($order['Order']['status_id'] == 83) { ?>
                                <a href="#" class="btn btn-sm btn-success me-3" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans">
                                    <i class="fas fa-arrow-right"></i>
                                    Gerar Boleto
                                </a>
                            <?php } ?>

                            <?php if ($order['Order']['status_id'] == 84 && $income) { ?>
                                <a href="<?php echo $this->base . '/incomes/gerar_boleto/' . $income["Income"]["id"] . '/1'; ?>" class="btn btn-sm btn-success me-3">
                                    <i class="fas fa-download"></i>
                                    Baixar Boleto
                                </a>
                            <?php } ?>

                            <?php if ($gerarNota) { ?>
                                <a href="<?php echo $this->base . '/orders/nota_debito/' . $order["Order"]["id"]; ?>" class="btn btn-sm btn-primary me-3">
                                    <i class="fas fa-download"></i>
                                    Gerar nota de débito
                                </a>
                            <?php } ?>

                            <button type="submit" class="btn btn-sm btn-success me-3 js-salvar" <?php echo $order['Order']['status_id'] >= 87 ? 'disabled="disabled"' : ''; ?>>Salvar dados</button>

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
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['commission_fee']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Taxa</span>
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
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$<?php echo $order['Order']['desconto']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Desconto</span>
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

</div>
<!--end::Row-->


<div class="row">
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-0 py-3 mt-10">
            <div class="row">
                <div class="col-8">
                    <h3>Itens</h3>
                </div>
                <?php if ($order['Order']['status_id'] == 83) { ?>
                    <div class="col-4">
                        <a href="#" class="btn btn-sm btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_lote_usuarios">
                            <i class="fas fa-arrow-up"></i>
                            Beneficiários em lote (CSV)
                        </a>
                        <a href="#" class="btn btn-sm btn-primary me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_add_beneficiarios">
                            <i class="fas fa-user"></i>
                            Novo Beneficiário
                        </a>
                        <a href="#" class="btn btn-sm btn-info me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_add_itinerario">
                            <i class="fas fa-bus"></i>
                            Novo Itinerário
                        </a>
                    </div>
                <?php } ?>

            </div>
            <div class="table-responsive" id="search_form">
                <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "edit/" . $id . '#search_form')); ?>" role="form" id="busca" autocomplete="off">
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
                    </div>
                </form>
                <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th>Beneficiário</th>
                        <th>Benefício</th>
                        <th width="90px">Dias Úteis</th>
                        <th width="120px">Desconto</th>
                        <th width="120px">Quantidade por dia</th>
                        <th>Valor por dia</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th class="<?php echo $order['Order']['status_id'] != 83 ? 'rounded-end' : '' ?>">Total</th>
                        <?php if ($order['Order']['status_id'] == 83) { ?>
                            <th class="rounded-end"></th>
                        <?php } ?>
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
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($order['Order']['status_id'] == 83) { ?>
                                        <input type="text" class="form-control money_field var_days_input" value="<?php echo $items[$i]["OrderItem"]["var"]; ?>">
                                    <?php } else { ?>
                                        <?php echo $items[$i]["OrderItem"]["var"]; ?>
                                    <?php } ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["CustomerUserItinerary"]["price_per_day"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo $items[$i]["OrderItem"]["subtotal_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["transfer_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4 total_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
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
                            <td colspan="<?php echo $order['Order']['status_id'] == 83 ? 7 : 6 ?>"></td>
                            <td id="subtotal_sum">R$<?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                            <td id="transfer_fee_sum">R$<?php echo number_format($transfer_fee, 2, ',', '.'); ?></td>
                            <td id="total_sum">R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="<?php echo $order['Order']['status_id'] == 83 ? 10 : 9 ?>">Nenhum registro encontrado</td>
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
            <form action="<?php echo $this->base . '/orders/upload_user_csv/'.$id; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $order['Order']['customer_id']; ?>">
                <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Enviar CSV com beneficiários a serem incluídos</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
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
                scrollTop: $("#total_sum").offset().top
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

            if (newValue != '' && newValue != undefined && newValue != null) {
                $.ajax({
                    type: 'POST',
                    url: <?php echo $this->base; ?> '/orders/updateWorkingDays', // Adjust the URL to your CakePHP action
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

                        $('#subtotal_sum').html('R$' + response.pedido_subtotal);
                        $('#transfer_fee_sum').html('R$' + response.pedido_transfer_fee);
                        $('#total_sum').html('R$' + response.pedido_total);
                    }
                });
            }
        });

        $('.var_days_input').on('change', function() {
            let newValue = $(this).val();
            const orderItemId = $(this).parent().parent().find('.item_id').val();
            const line = $(this).parent().parent();

            if (newValue == '') {
                newValue = 0;
                $(this).val('0,00');
            }

            $.ajax({
                type: 'POST',
                url: <?php echo $this->base; ?> '/orders/updateWorkingDays', // Adjust the URL to your CakePHP action
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

                    $('#subtotal_sum').html('R$' + response.pedido_subtotal);
                    $('#transfer_fee_sum').html('R$' + response.pedido_transfer_fee);
                    $('#total_sum').html('R$' + response.pedido_total);
                }
            });
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


    })
</script>