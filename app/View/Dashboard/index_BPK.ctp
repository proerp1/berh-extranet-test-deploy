<?php 
    if ($receber_hoje == null) {
        $receber_hoje = 0;
    }

    if($recebidas == null) {
        $recebidas = 0;
    }
?>

<?php if(Cakesession::read("Auth.User.Group.id") == 1){ ?>
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-sm-6 col-xl-6 mb-xl-10">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100">
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <i class="fas fa-dollar-sign fa-3x text-info"></i>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($receber_hoje, 2, ",", '.') ?></span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Contas à Receber Hoje</span>
                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="h-8px mx-3 w-100 bg-light-info rounded">
                            <div class="bg-info rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->
        </div>
        <!--end::Col-->

        <div class="col-sm-6 col-xl-6 mb-xl-10">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100">
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <i class="fas fa-dollar-sign fa-3x text-success"></i>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($recebidas, 2, ",", '.') ?></span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Contas Recebidas Hoje</span>
                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="h-8px mx-3 w-100 bg-light-success rounded">
                            <div class="bg-success rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->
        </div>
    </div>

    <div class="card mb-5 mb-xl-8">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Contratos Vendidos:</span>
            </h3>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table table-hover align-middle gs-0 gy-4">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-4 min-w-125px rounded-start">Data</th>
                            <th>Código Associado</th>
                            <th class="min-w-325px">Nome Fantasia</th>
                            <th class="min-w-125px">Cidade</th>
                            <th class="min-w-125px">Estado</th>
                            <th class="min-w-125px">Vendedor</th>
                            <th class="min-w-125px rounded-end">Mensalidade do Plano R$</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody>
                        <?php $total = 0; ?>
                        <?php if(!empty($contratos_vendidos)){ ?>
                            <?php foreach ($contratos_vendidos as $contrato_vendido) { ?>
                                <?php $total += $contrato_vendido["Plan"]["id"] != null ? $contrato_vendido["Plan"]["value"] : 0 ?>
                                <tr>
                                    <td class="fw-bold fs-7"><?php echo date('d/m/Y', strtotime($contrato_vendido["Customer"]["created"])); ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["codigo_associado"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["nome_primario"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["cidade"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["estado"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Seller"]["nome_fantasia"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Plan"]["value"]; ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?> 
                            <tr>
                                <td colspan="7" class="fw-bold fs-7">Nenhum registro encontrado</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total contratos: <?php echo count($contratos_vendidos) ?></td>
                            <td colspan="4"></td>
                            <td colspan="2">Total plano R$: <?php echo number_format($total, 2, ',','.') ?></td>
                        </tr>
                    </tfoot>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
    </div>

    <div class="card mb-5 mb-xl-8">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Títulos Pagos:</span>
            </h3>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table table-hover align-middle gs-0 gy-4">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-4 min-w-125px rounded-start">Data</th>
                            <th>Código Associado</th>
                            <th class="min-w-325px">Nome Fantasia</th>
                            <th class="min-w-125px rounded-end">Valor R$</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody>
                        <?php $total = 0; ?>
                        <?php if(!empty($contratos_vendidos)){ ?>
                            <?php foreach ($contratos_vendidos as $contrato_vendido) { ?>
                                <?php $total += $contrato_vendido["Plan"]["id"] != null ? $contrato_vendido["Plan"]["value"] : 0 ?>
                                <tr>
                                    <td class="fw-bold fs-7"><?php echo date('d/m/Y', strtotime($contrato_vendido["Customer"]["created"])); ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["codigo_associado"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["nome_primario"]; ?></td>
                                    <td class="fw-bold fs-7"><?php echo $contrato_vendido["Customer"]["cidade"]; ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?> 
                            <tr>
                                <td colspan="4" class="fw-bold fs-7">Nenhum registro encontrado</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total títulos: <?php echo count($titulos_pagos) ?></td>
                        </tr>
                    </tfoot>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
    </div>
<?php } ?>