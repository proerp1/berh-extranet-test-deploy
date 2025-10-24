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
                <div class="d-flex justify-content-end gap-2" data-kt-customer-table-toolbar="base">
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_desconto">
                        <i class="fas fa-plus"></i>
                        Novo Desconto
                    </a>
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">ID</th>
                        <th>Tipo de Desconto</th>
                        <th>Observação</th>
                        <th>Qtd Pedidos</th>
                        <th>Valor Total</th>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_quantidade_pedidos = 0;
                    $total_valor = 0;
                    ?>
                    <?php if ($batches) { ?>
                        <?php foreach ($batches as $batch) { ?>                            
                            <?php
                            $total_quantidade_pedidos += $batch['OrderDiscountBatch']['quantidade_pedidos'];
                            $total_valor += $batch['OrderDiscountBatch']['valor_total_not_formated'];
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['OrderDiscountBatch']['id']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['OrderDiscountBatch']['discount_type']; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php 
                                        if (!empty($batch['OrderDiscountBatch']['observacao'])) {
                                            echo mb_strlen($batch['OrderDiscountBatch']['observacao']) > 50 
                                                ? mb_substr($batch['OrderDiscountBatch']['observacao'], 0, 50) . '...' 
                                                : $batch['OrderDiscountBatch']['observacao'];
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['OrderDiscountBatch']['quantidade_pedidos']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['OrderDiscountBatch']['valor_total']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['OrderDiscountBatch']['created']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $batch['UserCreator']['name']; ?></td>
                                <td>
                                    <a href="<?php echo $this->base.'/orders/lote_desconto/'.$id.'/'.$batch['OrderDiscountBatch']['id']; ?>" class="btn btn-info btn-sm">
                                        Visualizar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/orders/delete_lote_desconto/'.$id.'/'.$batch['OrderDiscountBatch']['id']; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4">Total</td>
                        <td colspan="2"></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $total_quantidade_pedidos; ?></td>
                        <td class="fw-bold fs-7 ps-4">R$<?php echo number_format($total_valor, 2, ',', '.'); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_desconto" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Novo Desconto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <form id="form_desconto">
                    <div class="row mb-7">
                        <div class="col-md-12 mb-5">
                            <label class="form-label required">Pedidos</label>
                            <?php echo $this->Form->input('pedidos', ["multiple" => true, "label" => false, "id" => "select_pedidos", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "required" => true, "empty" => "Selecione", 'options' => $orders]);?>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <div class="col-md-6">
                            <label class="form-label required">Tipo de Desconto</label>
                            <select name="discount_type" id="discount_type" class="form-select" required>
                                <option value="">Selecione o tipo...</option>
                                <option value="REEBOLSO">REEBOLSO</option>
                                <option value="ECONOMIA = CREDITA CONTA">ECONOMIA = CREDITA CONTA</option>
                                <option value="AJUSTE = CREDITA E DEBITA">AJUSTE = CREDITA E DEBITA</option>
                                <option value="INCONSISTENCIA = SOMENTE CREDITA">INCONSISTENCIA = SOMENTE CREDITA</option>
                                <option value="SALDO">SALDO</option>
                                <option value="BOLSA DE CREDITO">BOLSA DE CREDITO</option>
                                <option value="CONTESTACAO GE = SOMENTE DEBITA">CONTESTACAO GE = SOMENTE DEBITA</option>
                                <option value="RECEITA DERIVADA = SOMENTE CREDITA">RECEITA DERIVADA = SOMENTE CREDITA</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Valor Total</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_total" id="valor_total" class="form-control" placeholder="0,00" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <div class="col-md-12">
                            <label class="form-label">Observação</label>
                            <textarea name="observacao" id="observacao" class="form-control" rows="3" placeholder="Digite uma observação sobre este desconto"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" id="btn_salvar_lote" class="btn btn-success">
                    <i class="fas fa-save"></i> Criar Desconto
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $('#valor_total').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $('#btn_salvar_lote').on('click', function () {
            const form = $('#form_desconto')[0];
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const order_id = <?php echo $id; ?>;
            const pedidosSelecionados = $('#select_pedidos').val() || [];
            const discount_type = $('#discount_type').val();
            const observacao = $('#observacao').val();
            const valor_total_str = $('#valor_total').val();
            const valor_total = parseFloat(valor_total_str.replace(/\./g, '').replace(',', '.'));

            if (pedidosSelecionados.length === 0) {
                alert('Selecione pelo menos um pedido para o lote');
                return;
            }

            if (valor_total <= 0) {
                alert('O valor total do lote deve ser maior que zero');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Criando lote...');

            $.ajax({
                type: 'POST',
                url: base_url + '/orders/criar_lote_desconto',
                data: {
                    order_id: order_id,
                    pedidos: pedidosSelecionados,
                    discount_type: discount_type,
                    observacao: observacao,
                    valor_total: valor_total
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function (err) {
                    alert('Erro ao enviar os dados');
                }
            });
        });

        $('#modal_desconto').on('hidden.bs.modal', function () {
            $('#form_desconto')[0].reset();
            $('#select_pedidos').val(null).trigger('change');
        });
    });
</script>
