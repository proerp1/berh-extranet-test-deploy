<?php echo $this->element("../Orders/_abas"); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
            <h3 class="card-title">Detalhes da Operadora</h3>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <a href="<?php echo $this->Html->url(array("controller" => "orders", "action" => "operadoras_detalhes_export/" . $id . "/" . $supplier_id)); ?>" class="btn btn-light-primary me-3">
                    <i class="fas fa-table"></i>
                    Exportar Relatório
                </a>
            </div>
        </div>
    </div>
    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
            <tr class="fw-bolder text-muted bg-light">
                    <th>Fornecedor</th>
                    <th>Benefício</th>
                    <th>Beneficiário</th>
                    <th>Total</th>
                    <th>Data inicio Processamento</th>
                    <th>Data fim Processamento</th>
                    <th>Status Processamento</th>
                    <th>Motivo Processamento</th>
                    <th>Pedido Operadora</th>
                    <th>ID Conta Pagar</th>
                    <th>Status do Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total=0;
                foreach ($suppliersAll as $supplier) {
                    $total += $supplier["OrderItem"]["subtotal_not_formated"];
                    ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Benefit']['name']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['CustomerUser']['name']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo 'R$'.$supplier["OrderItem"]["subtotal"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["data_inicio_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["data_fim_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["status_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["motivo_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["pedido_operadora"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["outcome_id"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["StatusOutcome"]["name"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total, 2, ',', '.'); ?></td>
                    <td colspan="7"></td>
                </tr>
            </tfoot>
        </div>
    </div>
</div>
