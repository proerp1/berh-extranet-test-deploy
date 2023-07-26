<script type="text/javascript">
    $(document).ready(function() {
        $("#OrderLastFareUpdate").datepicker({
            language: "pt-BR",
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('#OrderUnitPrice').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>


<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-0 py-3">
        <div class="row">
            <div class="col-9">
                <?php echo $this->element("aba_orders"); ?>
            </div>
            <div class="col-3 pt-4">
                <a href="#" class="btn btn-sm btn-success me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_gerar_arquivo">
                    <i class="fas fa-arrow-right"></i>
                    Enviar SPTrans
                </a>
            </div>
        </div>
    </div>
    <div class="card-body pt-0 py-3 mt-10">
        <div class="row">
            <div class="col-9">
                <h3>Itens</h3>
            </div>
            <div class="col-3">
                <a href="#" class="btn btn-sm btn-primary me-3 mb-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_gerar_arquivo">
                    <i class="fas fa-file"></i>
                    Novo Beneficiário
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th>Beneficiário</th>
                    <th>Benefício</th>
                    <th width="80px">Dias Úteis</th>
                    <th>Valor por dia</th>
                    <th>Subtotal</th>
                    <th>Repasse</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                $transfer_fee = 0;
                $total = 0;
                if ($items) { ?>
                    <?php for ($i = 0; $i < count($items); $i++) {
                        $subtotal += $items[$i]["OrderItem"]["subtotal_not_formated"];
                        $transfer_fee += $items[$i]["OrderItem"]["transfer_fee_not_formated"];
                        $total += $items[$i]["OrderItem"]["total_not_formated"];
                    ?>
                        <tr class="<?php echo $items[$i]["OrderItem"]["working_days"] != $items[$i]["Order"]["working_days"] ? 'table-warning' : ''; ?>">
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUserItinerary"]["benefit_name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><input type="text" class="form-control" value="<?php echo $items[$i]["OrderItem"]["working_days"]; ?>"></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["CustomerUserItinerary"]["price_per_day"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4"></td>
                        <td>R$<?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                        <td>R$<?php echo number_format($transfer_fee, 2, ',', '.'); ?></td>
                        <td>R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>