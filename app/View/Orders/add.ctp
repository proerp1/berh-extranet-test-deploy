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
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Order', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cliente</label>
                <p><?php echo $order['Customer']['nome_primario']; ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Período</label>
                <p><?php echo $order['Order']['order_period']; ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Dias Úteis</label>
                <p><?php echo $order['Order']['working_days']; ?></p>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Subtotal</label>
                <p>R$<?php echo $order['Order']['subtotal']; ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Repasse</label>
                <p>R$<?php echo $order['Order']['transfer_fee']; ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Comissão</label>
                <p>R$<?php echo $order['Order']['commission_fee']; ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Total</label>
                <p>R$<?php echo $order['Order']['total']; ?></p>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Criado em</label>
                <p><?php echo date('d/m/Y', strtotime($order['Order']['created'])); ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Criado por</label>
                <p><?php echo $order['Creator']['name']; ?></p>
            </div>
        </div>

        </form>
    </div>

</div>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-0 py-3 mt-10">
        <h3>Itens</h3>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th>Cliente</th>
                    <th>Benefício</th>
                    <th width="80px">Dias Úteis</th>
                    <th>Valor por dia</th>
                    <th>Subtotal</th>
                    <th>Repasse</th>
                    <th>Total</th>
                    <th class="w-200px min-w-200px rounded-end">Ações</th>
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
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->base . '/orders/edit/' . $items[$i]["OrderItem"]["id"]; ?>" class="btn btn-info btn-sm">
                                    Editar
                                </a>
                                <a href="javascript:" onclick="verConfirm('<?php echo $this->base . '/orders/delete/' . $items[$i]["OrderItem"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                    Excluir
                                </a>
                            </td>
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