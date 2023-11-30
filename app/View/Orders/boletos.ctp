<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base; ?>/orders/edit/<?php echo $id; ?>">Pedido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->base; ?>/orders/boletos/<?php echo $id; ?>">Boletos</a>
    </li>
</ul>
<?php $url_novo = $this->base . "/benefits/add/"; ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "boletos/".$id)); ?>" role="form" id="busca" autocomplete="off">
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-250px min-w-250px rounded-start">Status</th>
                    <th>Beneficiário</th>
                    <th>Período</th>
                    <th>Fornecedor</th>
                    <th class="w-200px min-w-200px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <?php echo $data[$i]["PaymentImportLog"]["processed"] ? 'Processado' : 'Pendente'; ?>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["order_period_from"] . ' - ' . $data[$i]["Order"]["order_period_to"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["nome_fantasia"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <?php if ($data[$i]["PaymentImportLog"]["processed"]) { ?>
                                    <a href="<?php echo $this->base . '/orders/baixar_boleto_fornecedor/' . $data[$i]["PaymentImportLog"]["id"]; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-download"></i>
                                        Baixar boleto
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
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