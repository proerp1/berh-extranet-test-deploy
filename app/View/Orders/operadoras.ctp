<?php echo $this->element("../Orders/_abas"); ?>

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
                <a href="#" class="btn btn-primary" id="pag_sel">
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
                <th class="ps-4 w-80px min-w-80px rounded-start">
                    <input type="checkbox" class="check_all">
                </th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Fornecedor</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Status</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Subtotal</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Valor Conta a Pagar</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Diferença</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Economia</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">N° Pedido</th>
                <th class="ps-4 w-250px min-w-250px rounded-start">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_saldo=0;
                $total=0;
                $total_outcomes=0;
                $total_diferenca=0;
                foreach ($suppliersAll as $supplier) {
                    $total_saldo+=$supplier[0]['total_saldo'];
                    $total+=$supplier[0]['subtotal'];
                    $total_outcomes+=$supplier[0]['total_outcomes'];

                    $valor_diferenca = 0;
                    if ($supplier[0]['subtotal'] != 0 and $supplier[0]['total_outcomes'] != 0) {
                        $valor_diferenca = ($supplier[0]['subtotal'] - $supplier[0]['total_outcomes']);
                    }
                    
                    $total_diferenca+=$valor_diferenca;
                    ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4">
                            <?php if ($supplier[0]['count_outcomes'] == 0) { ?>
                                <input type="checkbox" name="js_id_line" class="check_line" id="">
                            <?php } ?>
                        </td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Supplier']['razao_social']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['OrderItem']['status_processamento']; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['subtotal'],2,',','.'); ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['total_outcomes'],2,',','.'); ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($valor_diferenca,2,',','.'); ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($supplier[0]['total_saldo'],2,',','.'); ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $supplier['Order']['id']; ?></td>
                        <td class="fw-bold fs-7 ps-4">
                            <input type="hidden" class="supplier_id" value="<?php echo $supplier['Supplier']['id']; ?>">
                            <a href="<?php echo $this->base.'/orders/operadoras_detalhes/'.$supplier['Order']['id'].'/'.$supplier['Supplier']['id']; ?>" class="btn btn-info btn-sm">
                                Detalhes
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th  class="fw-bold fs-5 ps-4" colspan="3">Total</th>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total, 2, ',', '.'); ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total_outcomes, 2, ',', '.'); ?></td>
                    <td class="fw-bold fs-7 ps-4"><?php echo number_format($total_diferenca, 2, ',', '.'); ?></td>
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
            <div class="modal-body">
                <p>Tem certeza que deseja gerar o pagamento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="pag_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#pag_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="js_id_line"]:checked').length > 0) {
                $('#modal_gerar_pagamento').modal('show');
            } else {
                alert('Selecione ao menos um registro');
            }
        });

        $('#pag_confirm').on('click', function(e) {
            e.preventDefault();

            const orderId = <?php echo $id; ?>;
            const checkboxes = $('input[name="js_id_line"]:checked');
            const suppliersIds = [];

            checkboxes.each(function() {
                suppliersIds.push($(this).parent().parent().find('.supplier_id').val());
            });

            if (suppliersIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: base_url+'/orders/gerar_pagamento',
                    data: {
                        suppliersIds,
                        orderId
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_line").prop('checked', true);
            } else {
                $(".check_line").prop('checked', false);
            }
        });
    });
</script>
