 <!-- .row -->
 <style type="text/css">
     .er-count {
         font-size: 20px !important;
     }
 </style>

 <?php $s = isset($_GET['s']) ? $_GET['s'] : ''; ?>

 <div class="row">
     <div class="col-sm-4 mb-5 mb-xl-10">
         <div class="card mb-5 mb-xl-10">
             <div class="card-body pt-9 pb-0">
                 <div class="row">

                     <div class="col-sm-12 mb-5 mb-xl-10">
                         <div class="row">
                             <div class="col">
                                 <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1"><?php echo $is_admin ? 'Todos Executivos' : CakeSession::read('Auth.User.name'); ?></a>
                             </div>
                             <div class="col">
                                 <?php if ($is_admin) { ?>
                                     <?php echo $this->Form->input('seller_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Todos", 'options' => $executivos, 'label' => false, 'selected' => $s]); ?>
                                 <?php } ?>
                             </div>
                         </div>

                         <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                             <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                             <span class="svg-icon svg-icon-4 me-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                     <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="currentColor" />
                                     <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon--><?php echo $is_admin ? '-' : CakeSession::read('Auth.User.Group.name'); ?></a>
                         <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                             <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                             <span class="svg-icon svg-icon-4 me-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                     <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor" />
                                     <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                             <?php echo $is_admin ? '-' : CakeSession::read('Auth.User.username'); ?>
                         </a>
                     </div>

                 </div>
                 <div class="row">
                     <div class="col border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                         <!--begin::Number-->
                         <div class="d-flex align-items-center">
                             <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                             <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                     <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                     <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                             <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="<?php echo $goal; ?>" data-kt-countup-prefix="R$">0</div>
                         </div>
                         <!--end::Number-->
                         <!--begin::Label-->
                         <div class="fw-bold fs-6 text-gray-400">Meta Mensal</div>
                         <!--end::Label-->
                     </div>
                     <!--end::Stat-->
                     <!--begin::Stat-->
                     <div class="col border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                         <!--begin::Number-->
                         <div class="d-flex align-items-center">
                             <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                             <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                     <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                                     <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                             <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="<?php echo $totalSalesRaw; ?>" data-kt-countup-prefix="R$">0</div>
                         </div>
                         <!--end::Number-->
                         <!--begin::Label-->
                         <div class="fw-bold fs-6 text-gray-400">Vendas</div>
                         <!--end::Label-->
                     </div>
                     <!--end::Stat-->
                     <!--begin::Stat-->
                     <div class="col border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                         <!--begin::Number-->
                         <div class="d-flex align-items-center">
                             <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                             <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                     <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                     <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                 </svg>
                             </span>
                             <!--end::Svg Icon-->
                             <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="60" data-kt-countup-prefix="%">0</div>
                         </div>
                         <!--end::Number-->
                         <!--begin::Label-->
                         <div class="fw-bold fs-6 text-gray-400">% Meta</div>
                         <!--end::Label-->
                     </div>
                     <!--end::Stat-->
                 </div>

             </div>
         </div>

         <div class="card">
             <div class="card-body">
                 <h3 class="box-title">Indicadores</h3>
                 <div class="d-flex flex-stack mb-2">
                     <a href="#" class="text-dark fw-bolder text-hover-primary fs-2">R$<?php echo number_format($dailyGoal, 2, ',', '.') ?></a>
                     <span class="text-muted fw-bold text-muted d-block fs-7">Meta Diária</span>
                 </div>
                 <div class="h-10px mb-10 w-100 bg-light mb-3">
                     <div class="bg-success rounded h-10px" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                 </div>
                 <div class="d-flex flex-stack mb-2">
                     <a href="#" class="text-dark fw-bolder text-hover-primary fs-2">R$<?php echo $totalSalesPreview; ?></a>
                     <span class="text-muted fw-bold text-muted d-block fs-7">Vendas previsto</span>
                 </div>
                 <div class="h-10px mb-10 w-100 bg-light mb-3">
                     <div class="bg-success rounded h-10px" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                 </div>
                 <div class="d-flex flex-stack mb-2">
                     <a href="#" class="text-dark fw-bolder text-hover-primary fs-2">R$<?php echo number_format($goalLeft, 2, ',', '.'); ?></a>
                     <span class="text-muted fw-bold text-muted d-block fs-7">Saldo</span>
                 </div>
                 <div class="h-10px mb-10 w-100 bg-light mb-3">
                     <div class="bg-danger rounded h-10px" role="progressbar" style="width: <?php echo $percentageLeft; ?>%;" aria-valuenow="<?php echo $percentageLeft; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                 </div>
                 <div class="d-flex flex-stack mb-2">
                     <a href="#" class="text-dark fw-bolder text-hover-primary fs-2">R$<?php echo $totalSalesEstimate; ?></a>
                     <span class="text-muted fw-bold text-muted d-block fs-7">Orçamentos</span>
                 </div>
                 <div class="h-10px mb-10 w-100 bg-light mb-3">
                     <div class="bg-warning rounded h-10px" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                 </div>

             </div>
         </div>
     </div>


     <div class="col-xl-12 col-xxl-8 mb-5 mb-xl-10">
         <!--begin::Table Widget 3-->
         <div class="card card-flush h-xl-100">
             <!--begin::Card header-->
             <div class="card-header py-7">
                 <!--begin::Tabs-->
                 <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 gap-4 gap-lg-10 gap-xl-15">
                     <li class="nav-item">
                         <a class="fs-4 fw-bolder nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">INÍCIO</a>
                     </li>
                     <li class="nav-item">
                         <a class="fs-4 fw-bolder nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">AGUARDANDO PAGAMENTO</a>
                     </li>
                     <li class="nav-item">
                         <a class="fs-4 fw-bolder nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3">PAGAMENTO CONFIRMADO</a>
                     </li>
                     <li class="nav-item">
                         <a class="fs-4 fw-bolder nav-link" data-bs-toggle="tab" href="#kt_tab_pane_4">EM PROCESSAMENTO</a>
                     </li>
                     <li class="nav-item">
                         <a class="fs-4 fw-bolder nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">FINALIZADO</a>
                     </li>
                 </ul>
             </div>
             <!--end::Card header-->
             <!--begin::Card body-->
             <div class="card-body pt-1">
                 <div class="tab-content" id="myTabContent">
                     <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                         <table id="kt_widget_table_3" class="table table-row-dashed align-middle fs-6 gy-4 my-0 pb-3" data-kt-table-widget-3="all">
                             <thead class="fw-bolder text-muted bg-light">
                                 <tr>
                                     <td>DATA</td>
                                     <td>DE</td>
                                     <td>ATÉ</td>
                                     <td>COMISSÃO</td>
                                     <td>CLIENTE </td>
                                     <td>SUBTOTAL</td>
                                     <td>TOTAL</td>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php if (isset($groupedOrders[83])) { ?>
                                     <?php foreach ($groupedOrders[83] as $order) { ?>
                                         <tr>
                                             <td><?php echo $order['Order']['created']; ?></td>
                                             <td><?php echo $order['Order']['order_period_from']; ?></td>
                                             <td><?php echo $order['Order']['order_period_to']; ?></td>
                                             <td><?php echo $order['Order']['commission_fee']; ?></td>
                                             <td><?php echo $order['Customer']['nome_primario']; ?></td>
                                             <td><?php echo $order['Order']['subtotal']; ?></td>
                                             <td><?php echo $order['Order']['total']; ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                     <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                         <table id="kt_widget_table_3" class="table table-row-dashed align-middle fs-6 gy-4 my-0 pb-3" data-kt-table-widget-3="all">
                             <thead class="fw-bolder text-muted bg-light">
                                 <tr>
                                     <td>DATA</td>
                                     <td>DE</td>
                                     <td>ATÉ</td>
                                     <td>COMISSÃO</td>
                                     <td>CLIENTE </td>
                                     <td>SUBTOTAL</td>
                                     <td>TOTAL</td>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php if (isset($groupedOrders[84])) { ?>
                                     <?php foreach ($groupedOrders[84] as $order) { ?>
                                         <tr>
                                             <td><?php echo $order['Order']['created']; ?></td>
                                             <td><?php echo $order['Order']['order_period_from']; ?></td>
                                             <td><?php echo $order['Order']['order_period_to']; ?></td>
                                             <td><?php echo $order['Order']['commission_fee']; ?></td>
                                             <td><?php echo $order['Customer']['nome_primario']; ?></td>
                                             <td><?php echo $order['Order']['subtotal']; ?></td>
                                             <td><?php echo $order['Order']['total']; ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                     <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                         <table id="kt_widget_table_3" class="table table-row-dashed align-middle fs-6 gy-4 my-0 pb-3" data-kt-table-widget-3="all">
                             <thead class="fw-bolder text-muted bg-light">
                                 <tr>
                                     <td>DATA</td>
                                     <td>DE</td>
                                     <td>ATÉ</td>
                                     <td>COMISSÃO</td>
                                     <td>CLIENTE </td>
                                     <td>SUBTOTAL</td>
                                     <td>TOTAL</td>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php if (isset($groupedOrders[85])) { ?>
                                     <?php foreach ($groupedOrders[85] as $order) { ?>
                                         <tr>
                                             <td><?php echo $order['Order']['created']; ?></td>
                                             <td><?php echo $order['Order']['order_period_from']; ?></td>
                                             <td><?php echo $order['Order']['order_period_to']; ?></td>
                                             <td><?php echo $order['Order']['commission_fee']; ?></td>
                                             <td><?php echo $order['Customer']['nome_primario']; ?></td>
                                             <td><?php echo $order['Order']['subtotal']; ?></td>
                                             <td><?php echo $order['Order']['total']; ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                     <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel">
                         <table id="kt_widget_table_3" class="table table-row-dashed align-middle fs-6 gy-4 my-0 pb-3" data-kt-table-widget-3="all">
                             <thead class="fw-bolder text-muted bg-light">
                                 <tr>
                                     <td>DATA</td>
                                     <td>DE</td>
                                     <td>ATÉ</td>
                                     <td>COMISSÃO</td>
                                     <td>CLIENTE </td>
                                     <td>SUBTOTAL</td>
                                     <td>TOTAL</td>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php if (isset($groupedOrders[86])) { ?>
                                     <?php foreach ($groupedOrders[86] as $order) { ?>
                                         <tr>
                                             <td><?php echo $order['Order']['created']; ?></td>
                                             <td><?php echo $order['Order']['order_period_from']; ?></td>
                                             <td><?php echo $order['Order']['order_period_to']; ?></td>
                                             <td><?php echo $order['Order']['commission_fee']; ?></td>
                                             <td><?php echo $order['Customer']['nome_primario']; ?></td>
                                             <td><?php echo $order['Order']['subtotal']; ?></td>
                                             <td><?php echo $order['Order']['total']; ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                     <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                         <table id="kt_widget_table_3" class="table table-row-dashed align-middle fs-6 gy-4 my-0 pb-3" data-kt-table-widget-3="all">
                             <thead class="fw-bolder text-muted bg-light">
                                 <tr>
                                     <td>DATA</td>
                                     <td>DE</td>
                                     <td>ATÉ</td>
                                     <td>COMISSÃO</td>
                                     <td>CLIENTE </td>
                                     <td>SUBTOTAL</td>
                                     <td>TOTAL</td>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php if (isset($groupedOrders[87])) { ?>
                                     <?php foreach ($groupedOrders[87] as $order) { ?>
                                         <tr>
                                             <td><?php echo $order['Order']['created']; ?></td>
                                             <td><?php echo $order['Order']['order_period_from']; ?></td>
                                             <td><?php echo $order['Order']['order_period_to']; ?></td>
                                             <td><?php echo $order['Order']['commission_fee']; ?></td>
                                             <td><?php echo $order['Customer']['nome_primario']; ?></td>
                                             <td><?php echo $order['Order']['subtotal']; ?></td>
                                             <td><?php echo $order['Order']['total']; ?></td>
                                         </tr>
                                     <?php } ?>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
             <!--end::Card body-->
         </div>
         <!--end::Table Widget 3-->
     </div>


 </div>
 <div class="row">
     <div class="col-md-4">
         <div class="card">
             <div class="card-body">
                 <div class="row">
                     <div class="col">
                         <h3 class="box-title">Forecast Diário</h3>
                     </div>
                     <div class="col">
                         <select class="form-control pull-right row b-none" id="month_proposal">
                             <?php foreach ($propMonths as $month) { ?>
                                 <option value="<?php echo $month[0]['month']; ?>" <?php echo $month[0]['month'] == date('m/Y') ? 'selected="selected"' : ''; ?>><?php echo $month[0]['month']; ?></option>
                             <?php } ?>
                         </select>
                     </div>
                 </div>
                 <?php
                    $proposalsTotal = 0;
                    foreach ($proposals as $proposal) {
                        $proposalsTotal += $proposal['Proposal']['total_price_not_formatted'];
                    }
                    ?>
                 <div class="row sales-report">

                     <div class="col-md-12 col-sm-12 col-xs-12 ">
                         <h1 class="text-right text-success m-t-20" id="total_forecast">R$ <?php echo number_format($proposalsTotal, 2, ',', '.'); ?></h1>
                     </div>
                 </div>
                 <div class="table-responsive">
                     <table class="table">
                         <thead>
                             <tr class="fw-bolder text-muted bg-light">
                                 <th class="ps-4 rounded-start">#</th>
                                 <th>DATA</th>
                                 <th class="rounded-end">VALOR</th>
                             </tr>
                         </thead>
                         <tbody id="proposals_tbl">
                             <?php foreach ($proposals as $k => $proposal) { ?>
                                 <tr>
                                     <td class="fw-bold fs-7 ps-4"><?php echo $k + 1; ?></td>
                                     <td class="fw-bold fs-7 ps-4 txt-oflo"><?php echo $proposal['Proposal']['expected_closing_date']; ?></td>
                                     <td class="fw-bold fs-7 ps-4"><span class="text-success">R$ <?php echo $proposal['Proposal']['total_price']; ?></span></td>
                                 </tr>
                             <?php } ?>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-4">
         <div class="card">
             <div class="card-body">
                 <?php
                    $waitingCredit = 0;
                    if (isset($groupedOrders[86])) {
                        foreach ($groupedOrders[86] as $order) {
                            $waitingCredit += $order['Order']['total_not_formated'];
                        }
                    } ?>

                 <h3 class="box-title">Confirmação financeira</h3>
                 <div class="row sales-report">
                     <div class="col-md-6 col-sm-6 col-xs-6">
                         <h2> </h2>
                         <p>Aguardando liberação de crédito financeiro</p>
                     </div>
                     <div class="col-md-6 col-sm-6 col-xs-6 ">
                         <h1 class="text-right text-info m-t-20">R$<?php echo number_format($waitingCredit, 2, ',', '.'); ?></h1>
                     </div>
                 </div>
                 <div class="table-responsive">
                     <table class="table">
                         <thead class="fw-bolder text-muted bg-light">
                             <tr class="fw-bolder text-muted bg-light">
                                 <th class="ps-4 rounded-start">DE</th>
                                 <th>ATÉ</th>
                                 <th>CLIENTE</th>
                                 <th class="rounded-end">VALOR</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php if (isset($groupedOrders[86])) { ?>
                                 <?php foreach ($groupedOrders[86] as $order) { ?>
                                     <tr>
                                         <td class="fw-bold fs-7 ps-4"><?php echo $order['Order']['order_period_from']; ?></td>
                                         <td class="fw-bold fs-7 ps-4"><?php echo $order['Order']['order_period_to']; ?></td>
                                         <td class="fw-bold fs-7 ps-4"><?php echo $order['Customer']['nome_primario']; ?></td>
                                         <td class="fw-bold fs-7 ps-4"><?php echo $order['Order']['total']; ?></td>
                                     </tr>
                                 <?php } ?>
                             <?php } ?>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-4">
         <div class="card">
             <div class="card-body">

                 <h3 class="box-title">Ranking de produtos</h3>
                 <div class="row sales-report">
                     <div class="col-md-6 col-sm-6 col-xs-6">
                         <h2> </h2>
                         <p>Lista dos 10 fornecedores com mais pedidos</p>
                     </div>
                 </div>
                 <div class="table-responsive">
                     <table class="table">
                         <thead class="fw-bolder text-muted bg-light">
                             <tr class="fw-bolder text-muted bg-light">
                                 <th class="ps-4 rounded-start">#</th>
                                 <th>FORNECEDOR</th>
                                 <th class="rounded-end">VALOR</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php foreach ($topSuppliers as $k => $order) { ?>
                                 <tr>
                                     <td class="fw-bold fs-7 ps-4"><?php echo $k + 1; ?></td>
                                     <td class="fw-bold fs-7 ps-4"><?php echo $order['Supplier']['nome_fantasia']; ?></td>
                                     <td class="fw-bold fs-7 ps-4">R$<?php echo number_format($order[0]['total'], 2, ',', '.'); ?></td>
                                 </tr>
                             <?php } ?>

                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>


 <!-- ============================================================== -->
 <!-- city-weather -->
 <!-- ============================================================== -->

 <script>
     $(document).ready(function() {
         $("#month_proposal").on("change", function() {
             var month = $(this).val();

             $.ajax({
                 url: "<?php echo $this->base; ?>/dashboard/getProposalByMonth",
                 type: "POST",
                 data: {
                     month: month
                 },
                 success: function(data) {
                     var data = JSON.parse(data);
                     var html = '';
                     var total = 0;
                     $.each(data, function(key, value) {
                         total += parseFloat(value.Proposal.total_price_not_formatted);
                         html += '<tr>';
                         html += '<td>' + (key + 1) + '</td>';
                         html += '<td class="txt-oflo">' + value.Proposal.expected_closing_date + '</td>';
                         html += '<td><span class="text-success">R$ ' + value.Proposal.total_price + '</span></td>';
                         html += '</tr>';
                     });
                     $('#proposals_tbl').html(html);
                     $('#total_forecast').html('R$ ' + total.toFixed(2).replace('.', ','));
                 }
             });
         });

         $('#seller_id').on('change', function() {
             var seller_id = $(this).val();
             window.location.href = "<?php echo $this->base; ?>/dashboard/comercial?s=" + seller_id;
         });
     })
 </script>