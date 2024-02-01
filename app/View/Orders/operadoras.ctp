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
        <?php foreach ($suppliersAll as $supplier) { ?>
            <tr>
                <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                <td class="fw-bold fs-7 ps-4"><?php echo $supplier[0]['subtotal']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
    </div>
</div>


