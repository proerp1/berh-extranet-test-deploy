<div class="modal fade" id="modalContaPaga" tabindex="-1" role="dialog" aria-labelledby="labelModalContaPaga">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelModalContaPaga">Pagar conta</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo $this->Form->create('Outcome', array("id" => "js-form-submit", "class" => "form-horizontal", "action" => '../outcomes/pagar_titulo/', "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
                <input type="hidden" name="data[Outcome][status_id]" value="13">
                <div class="modal-body">
                    <div class="mb-7">
                        <label class="form-label">Valor pago</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <?php echo $this->Form->input('valor_pago', ["type" => "text", "required" => true, "placeholder" => "Valor pago", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                        </div>
                    </div>
                    <div class="mb-7">
                        <label class="form-label">Data de Pagamento</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <?php echo $this->Form->input('data_pagamento', ["type" => "text", "required" => true, "placeholder" => "Data de Pagamento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                        <?php echo $this->Form->input('payment_method_baixa', array("required" => true, "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione", 'options' => ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito',  '7' => 'Débito em conta',  '8' => 'Dinheiro', '2' => 'Transfêrencia']));  ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>