<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        show_recorrencia($(".recorrencia:checked").val());

        $(".recorrencia").on("change", function(){
            var formaPgto = $(this).val();

            show_recorrencia(formaPgto);
        });

        $("#OutcomeValorBruto").on("focusout", function(event){
            var outcomeValorBruto = $(this).val();
            var outcomeValorMulta = $("#OutcomeValorMulta").val();
            
            calc_valor_total(outcomeValorMulta, outcomeValorBruto);
        })

        $("#OutcomeValorMulta").on("focusout", function(event){
            var outcomeValorMulta = $(this).val();
            var outcomeValorBruto = $("#OutcomeValorBruto").val();
            
            calc_valor_total(outcomeValorMulta, outcomeValorBruto);
        })
    })

    function calc_valor_total(outcomeValorMulta, outcomeValorBruto){
        var multa = replaceAll(outcomeValorMulta, ".", "");
        var multa = replaceAll(multa,",", ".");

        var bruto = replaceAll(outcomeValorBruto, ".", "");
        var bruto = replaceAll(bruto, ",", ".");

        if (multa == "") {
            var liquido = bruto;
        } else if (bruto == "") {
            var liquido = multa;
        } else {
            var liquido = ""+(parseFloat(bruto) + parseFloat(multa));  
        }
        

        $("#OutcomeValorTotal").val(retorna_dinheiro(liquido));
    }

    function show_recorrencia(formaPgto){
        if (formaPgto == 1) {
            $("#outcomeRecorrencia").show();
        } else {
            $("#outcomeRecorrencia").hide();
        }
    }
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Outcome', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if(isset($id)){ ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(array('Outcome' => $this->request->data['Outcome'])); ?></textarea>
            <?php } ?>
            <input type="hidden" name="query_string" value="<?php echo $_SERVER['QUERY_STRING'] ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Revenda</label>
                <?php echo $this->Form->input('resale_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número do documento</label>
                <?php echo $this->Form->input('doc_num', ["placeholder" => "Número do documento", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Fornecedor</label>
                <?php echo $this->Form->input('supplier_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição da conta</label>
                <?php echo $this->Form->input('name', ["placeholder" => "Descrição da conta", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor bruto</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor_bruto', ["type" => "text", "placeholder" => "Valor bruto", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor multa</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor_multa', ["type" => "text", "placeholder" => "Valor multa", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor liquido</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor_total', ["type" => "text", "readonly" => true, "placeholder" => "Valor liquido", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta bancária</label>
                <?php echo $this->Form->input('bank_account_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <label class="form-label">Vencimento</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('vencimento', ["type" => "text", "placeholder" => "Vencimento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Despesa</label>
                <?php echo $this->Form->input('expense_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Centro de custo</label>
                <?php echo $this->Form->input('cost_center_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                <?php echo $this->Form->input('payment_method', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito',  '7' => 'Débito em conta',  '8' => 'Dinheiro', '2' => 'Transfêrencia', '9' => 'Desconto']]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Plano de contas</label>
                <?php echo $this->Form->input('plano_contas_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Recorrência</label>
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input recorrencia" type="radio" value="1" name="data[Outcome][recorrencia]" id="recorrSim" <?php echo (isset($this->data['Outcome']) ? ($this->data['Outcome']['recorrencia'] == 1 ? 'checked' : '') : '') ?> />
                    <label class="form-check-label me-3" for="recorrSim">
                        Sim
                    </label>
                    <input class="form-check-input recorrencia" type="radio" value="2" name="data[Outcome][recorrencia]" id="recorrNão" <?php echo (isset($this->data['Outcome']) ? ($this->data['Outcome']['recorrencia'] == 2 ? 'checked' : '') : 'checked') ?> />
                    <label class="form-check-label" for="recorrNão">
                        Não
                    </label>
                </div>
            </div>

            <div id="outcomeRecorrencia">
                <?php if ($form_action != 'edit' || $this->request->data['Outcome']['recorrencia'] == 2){ ?>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Periodicidade</label>
                        <?php echo $this->Form->input('periodicidade', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [1 => "Mensal", 2 => "Bimestral", 3 => "Trimestral", 6 => "Semestral", 12 => "Anual"]]);?>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Quantidade</label>
                        <?php echo $this->Form->input('quantidade', ["placeholder" => "Quantidade", "class" => "form-control mb-3 mb-lg-0"]);?>
                    </div>
                <?php } else { ?>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Parcela</label>
                        <p><?php echo $this->request->data['Outcome']['parcela'].'ª' ?></p>
                    </div>
                <?php } ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observações</label>
                <?php echo $this->Form->input('observation', ["placeholder" => "Observações", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/outcomes/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php if (isset($this->request->data['Status']) && $this->request->data['Status']['id'] == 11): ?>
                        <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/12/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-success">Aprovar conta</a>
                        <?php if ($cancelarConta) { ?>
                            <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/15/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-danger">Cancelar conta</a>
                        <?php } ?>
                    <?php endif ?>
                    <?php if (isset($this->request->data['Status']) && $this->request->data['Status']['id'] == 12): ?>
                        <!-- <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/13/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-success">Conta paga</a> -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-success">Conta paga</a>
                    <?php endif ?>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Pagar conta</h4>
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