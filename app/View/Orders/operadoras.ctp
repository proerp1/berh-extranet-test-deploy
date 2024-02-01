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
</ul>
</ul>
<?php $url_novo = $this->base . "/benefits/add/"; ?>
<div class="card-body pt-0 py-3">
    <?php echo $this->element("table"); ?>
    <thead>
        <tr class="fw-bolder text-muted bg-light">
            <th class="ps-4 w-250px min-w-250px rounded-start">Fornecedor</th>
            <th class="ps-4 w-250px min-w-250px rounded-start">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total=0;
        foreach ($suppliersAll as $supplier) {
            $total+=$supplier[0]['subtotal'];
            ?>
            <tr>
                <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['subtotal'],2,',','.'); ?></td>
            </tr>
        <?php } ?>
    </tbody>
    </div>

    <tfoot>
    <tr>
        <th  class="fw-bold fs-5 ps-4">Total</th>
        <td class="fw-bold fs-7 ps-4"><?php echo number_format($total, 2, ',', '.'); ?></td>
    </tr>
</tfoot>

    <div class="mb-7 col" style="text-align: right;">
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_gerar_pagamento">
                                    Gerar Pagamento
                                </a>

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

    



