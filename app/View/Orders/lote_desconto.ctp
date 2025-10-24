<?php echo $this->element("../Orders/_abas"); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-6">
        
    </div>
    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
            <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-150px min-w-150px rounded-start">ID</th>
                    <th>Pedido</th>
                    <th class="w-200px min-w-200px rounded-end">Total do Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($items as $item) {
                    $total += $item["Order"]["total_not_formated"];
                    ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $item['OrderDiscountBatchItem']['id']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $item['Order']['id'].' - '.$item['Customer']['nome_secundario']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $item['Order']['total']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="fw-bold fs-7 ps-4" colspan="2">Total</td>
                    <td class="fw-bold fs-7 ps-4">R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </div>
    </div>
</div>
