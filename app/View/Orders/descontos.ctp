<?php echo $this->element("../Orders/_abas"); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "descontos/".$id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <th class="ps-4 w-150px min-w-150px rounded-start">Número</th>
                    <th>Cliente</th>
                    <th>Data de criação</th>
                    <th>Desconto</th>
                    <th class="rounded-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_desconto = 0; ?>
                <?php $total_subtotal = 0; ?>
                <?php if ($orders) { ?>
                    <?php for ($i = 0; $i < count($orders); $i++) { ?>
                        <?php $total_desconto += $orders[$i]["OrderParent"]["desconto_not_formated"]; ?>
                        <?php $total_subtotal += $orders[$i]["OrderParent"]["subtotal_not_formated"]; ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["OrderParent"]["id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["Customer"]["nome_primario"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $orders[$i]["OrderParent"]["created"] ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $orders[$i]["OrderParent"]["desconto"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $orders[$i]["OrderParent"]["subtotal"]; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Total</td>
                        <td colspan="2"></td>
                        <td class="fw-bold fs-7 ps-4 desconto"><?php echo 'R$' . number_format($total_desconto, 2, ',', '.'); ?></td>
                        <td class="fw-bold fs-7 ps-4 subtotal"><?php echo 'R$' . number_format($total_subtotal, 2, ',', '.'); ?></td>
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

<script>
    $( document ).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>
