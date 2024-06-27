<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
        <a class="nav-link " href="<?php echo $this->base; ?>/orders/edit/<?php echo $id; ?>">Pedido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base; ?>/orders/boletos/<?php echo $id; ?>">Boletos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base; ?>/orders/operadoras/<?php echo $id; ?>">Operadoras</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base; ?>/orders/saldos/<?php echo $id; ?>">Economia</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "saldos/".$id)); ?>" role="form" id="busca" autocomplete="off">
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
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>

        <?php if (count($data) == 0) { ?>
            <div class="row">
                <div class="col-12">
                    <a href="#" class="btn btn-sm btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_importar_saldo">
                        <i class="fas fa-arrow-up"></i>
                        Importar saldo (CSV)
                    </a>
                </div>
            </div>
            <br>
            <br>
        <?php } ?>

        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th>CPF</th>
                    <th>Beneficiário</th>
                    <th>Código Benefício</th>
                    <th>Benefício</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <?php $total += $data[$i]["OrderBalance"]["total_not_formated"]; ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["document"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["benefit_id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["total"]; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4"></td>
                        <td>R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_importar_saldo" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="<?php echo $this->base . '/orders/upload_saldo_csv/' . $id; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $order['Order']['customer_id']; ?>">
                <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Enviar CSV com os saldos a serem incluídos</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloImportacaoSaldo.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>