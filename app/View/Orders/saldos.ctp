<?php echo $this->element("../Orders/_abas"); ?>

<div class="row gy-5 g-xl-10">
    <div class="col-lg-4 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($order_balances_total[0][0]['total'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total Economia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($order_balances_total2[0][0]['total'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total Ajuste</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($order_balances_total3[0][0]['total'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total Inconsistencia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

        <div class="row">
            <div class="col-12">
                <a href="#" class="btn btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_importar_saldo">
                    <i class="fas fa-arrow-up"></i>
                    Importar (CSV)
                </a>

                <a href="<?php echo $this->base.'/orders/saldos/'.$id.'?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" style="float:right" class="btn btn-light-primary me-3">
                    <i class="fas fa-file-excel"></i>
                    Exportar
                </a>
            </div>
        </div>
        <br>
        <br>

        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-150px min-w-150px rounded-start">CPF</th>
                    <th>Beneficiário</th>
                    <th>Código Benefício</th>
                    <th>Item ID</th>
                    <th>Benefício</th>
                    <th>Pedido Operadora</th>
                    <th>Tipo</th>
                    <th>Observação</th>
                    <th class="w-150px min-w-150px rounded-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <?php 
                            $total += $data[$i]["OrderBalance"]["total_not_formated"]; 

                            $tr_class = "";
                            if ($data[$i]["OrderBalance"]["total_not_formated"] < 0) {
                                $tr_class = 'table-danger';
                            }

                            $tipo = "";
                            if ($data[$i]["OrderBalance"]["tipo"] == '1') {
                                $tipo = 'Credita conta';
                            } elseif ($data[$i]["OrderBalance"]["tipo"] == '2') {
                                $tipo = 'Credita e Debita';
                            } elseif ($data[$i]["OrderBalance"]["tipo"] == '3') {
                                $tipo = 'Somente Credita';
                            }
                        ?>
                        <tr class="<?php echo $tr_class; ?>">
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["document"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["code"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["pedido_operadora"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $tipo; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalance"]["observacao"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["OrderBalance"]["total"]; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="5"></td>
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
                    <p>Enviar CSV com as movimentações a serem incluídos</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloImportacaoMovimentacao.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>
