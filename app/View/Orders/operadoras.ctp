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
        <a class="nav-link" href="<?php echo $this->base; ?>/orders/saldos/<?php echo $id; ?>">Saldo</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
            <div class="row">
                <div class="col d-flex align-items-center">
                    
                </div>
            </div>
        </div>
        <div class="card-toolbar" style="text-align: right;">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_gerar_pagamento">
                    Gerar Pagamento
                </a>
            </div>
        </div>
    </div>

        
    <div class="card-body pt-0 py-3">
        
        <div class="table-responsive">
            
            <?php echo $this->element("table"); ?>
            <thead>
            <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-250px min-w-250px rounded-start">Fornecedor</th>
                    <th class="ps-4 w-250px min-w-250px rounded-start">Saldo</th>
                    <th class="ps-4 w-250px min-w-250px rounded-start">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_saldo=0;
                $total=0;
                foreach ($suppliersAll as $supplier) {
                    $total_saldo+=$supplier[0]['total_saldo'];
                    $total+=$supplier[0]['subtotal'];
                    ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['total_saldo'],2,',','.'); ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['subtotal'],2,',','.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th  class="fw-bold fs-5 ps-4">Total</th>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total_saldo, 2, ',', '.'); ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </div>
    </div>
</div>
    

    

<div class="modal fade" tabindex="-1" id="modal_gerar_pagamento" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/orders/gerar_pagamento/' . $id; ?>" class="form-horizontal" method="post">
                <div class="modal-body">
                    <p>Tem certeza que deseja gerar o pagamento?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary js-salvar">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

    



