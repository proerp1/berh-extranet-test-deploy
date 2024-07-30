<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
        <a class="nav-link " href="<?php echo $this->base; ?>/orders/edit/<?php echo $id; ?>">Pedido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="<?php echo $this->base; ?>/orders/boletos/<?php echo $id; ?>">Boletos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base; ?>/orders/operadoras/<?php echo $id; ?>">Operadoras</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base; ?>/orders/saldos/<?php echo $id; ?>">Economia</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">        
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
                </tr>
            </thead>
            <tbody>
                <?php 
                $total=0;
                foreach ($suppliersAll as $supplier) {
                    $total += $supplier["OrderItem"]["total_not_formated"];
                    ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Benefit']['name']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['CustomerUser']['name']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo 'R$'.$supplier["OrderItem"]["total"]; ?></td>                        
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["data_inicio_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["data_fim_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["status_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["motivo_processamento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier["OrderItem"]["pedido_operadora"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </div>
    </div>
</div>
