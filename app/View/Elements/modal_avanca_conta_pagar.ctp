<div class="modal fade" tabindex="-1" id="modal_avancar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Avançar os items selecionados?</p>

                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Status</label>
                  <?php
                  $options = [
                    '11' => 'Programado',
                    '12' => 'Aprovado',
                    '116' => 'Em Processamento',
                    '13' => 'Pago',
                    '14' => 'Cancelado',
                    '103' => 'Pendente',
                  ];
                  ?>
                    <select name="avancar_status" id="avancar_status" class="form-select">
                      <?php foreach ($options as $id => $name) { ?>
                        <?php if (isset($filter_status_id) && $id != $filter_status_id) { ?>
                            <option value="<?= $id ?>"><?= $name ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                </div>

                <div id="inputs_pago" style="display: none">
                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Data Pagamento</label>
                        <input type="text" name="avancar_data_pagamento" id="avancar_data_pagamento" class="form-control datepicker mb-3 mb-lg-0">
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                        <?php $metodos = ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito',  '7' => 'Débito em conta',  '8' => 'Dinheiro', '2' => 'Transfêrencia'] ?>
                        <select name="avancar_forma_de_pagamento" id="avancar_forma_de_pagamento" class="form-select">
                          <?php foreach ($metodos as $id => $name) { ?>
                            <?php if (isset($filter_status_id) && $id != $filter_status_id) { ?>
                                  <option value="<?= $id ?>"><?= $name ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                    </div>

                    <?php if ($show_valor_pago) { ?>
                        <div class="mb-7">
                            <label class="fw-semibold fs-6 mb-2">Valor Pago</label>
                            <input type="text" name="avancar_valor_pago" id="avancar_valor_pago" class="form-control money_exchange mb-3 mb-lg-0">
                        </div>
                    <?php } ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="avancar_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#avancar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_avancar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item para avançar');
            }
        });

        $('#avancar_confirm').on('click', function(e) {
            e.preventDefault();

            const checkboxes = $('input[name="item_ck"]:checked');
            const status = $('#avancar_status').val()
            const data_pagamento = $('#avancar_data_pagamento').val();
            const valor_pago = $('#avancar_valor_pago') ? $('#avancar_valor_pago').val() : null;
            const forma_de_pagamento = $('#avancar_forma_de_pagamento').val();
            const outcomeIds = [];

            checkboxes.each(function() {
                outcomeIds.push($(this).data('id'));
            });

            if (outcomeIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/outcomes/avanca_lote',
                    data: {
                        outcomeIds,
                        status,
                        data_pagamento,
                        valor_pago,
                        forma_de_pagamento,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $('#avancar_status').on('change', function (e) {
            $('#inputs_pago').toggle($(this).val() == 13)
        })
    })
</script>