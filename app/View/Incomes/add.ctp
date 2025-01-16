<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
         });
        
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

        $("#IncomeValorBruto, #IncomeValorMulta, #IncomeValorDesconto").on("focusout", function(event){
            calc_valor_total();
        })
    })

    function calc_valor_total(){
        var multa = replaceAll($("#IncomeValorMulta").val(), ".", "");
        var multa = replaceAll(multa,",", ".");

        var bruto = replaceAll($("#IncomeValorBruto").val(), ".", "");
        var bruto = replaceAll(bruto, ",", ".");

        var desconto = replaceAll($("#IncomeValorDesconto").val(), ".", "");
        var desconto = replaceAll(desconto, ",", ".");

        if (multa == "") {
            var liquido = parseFloat(bruto);
        } else if (bruto == "") {
            var liquido = parseFloat(multa);
        } else {
            var liquido = ""+(parseFloat(bruto) + parseFloat(multa));  
        }
        
        if (desconto != "") {
            liquido -= parseFloat(desconto);
        }

        $("#IncomeValorTotal").val(retorna_dinheiro(liquido));
    }

    function show_recorrencia(formaPgto){
        if (formaPgto == 1) {
            $("#incomeRecorrencia").show();
        } else {
            $("#incomeRecorrencia").hide();
        }
    }
</script>

<?php $payment_method = ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito', '7' => 'Débito em conta', '8' => 'Dinheiro', '2' => 'Transfêrencia', '9' => 'Desconto', '11' => 'Pix', '10' => 'Outros']; ?>
<?php 
    if(isset($id)){
        echo $this->element("abas_incomes", ['id' => $id]);
    }
?>
<input type="hidden" id="vencimento_js" value="<?php echo isset($id) ? $this->request->data['Income']['vencimento_nao_formatado'] : '' ?>">
<input type="hidden" id="data_agendamento_js" value="<?php echo date('d/m/Y') ?>">
<input type="hidden" id="valor_js" value="<?php echo isset($id) ? $this->request->data['Income']['valor_total_nao_formatado'] : '' ?>">

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Income', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if(isset($id)){ ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(array('Income' => $this->request->data['Income'])); ?></textarea>
            <?php } ?>
            <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' ?>">

            <?php if(isset($id)) { ?>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Status do cliente</label>
                    <p><?php echo !empty($this->request->data['Customer']['Status']) ? $this->request->data['Customer']['Status']['name'] : '' ?></p>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2"></label>
                    <p><a href="<?php echo $this->base.'/customers/edit/'.$this->request->data['Customer']['id'] ?>" class="btn btn-primary" target="_blank">Detalhes do cliente</a></p>
                </div>

                <?php if ($this->request->data['Status']['id'] == 17 || $this->request->data['Status']['id'] == 51){ ?>
                    <hr>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Data de recebimento</label>
                        <p><?php echo $this->request->data['Income']['data_pagamento'] ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Valor pago</label>
                        <p><?php echo $this->request->data['Income']['valor_pago'] ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Método de pagamento</label>
                        <p><?php echo $this->request->data['Income']['payment_method_baixa'] ? $payment_method[$this->request->data['Income']['payment_method_baixa']] : '-' ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Usuário</label>
                        <p><?php echo $this->request->data['UsuarioBaixa']['id'] != null ? $this->request->data['UsuarioBaixa']['name'] : '-' ?></p>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Data da baixa manual</label>
                        <p><?php echo $this->request->data['Income']['data_baixa'] != null ? date('d/m/Y H:i:s', strtotime($this->request->data['Income']['data_baixa'])) : '-' ?></p>
                    </div>

                    <hr>
                <?php } ?>

                <?php if ($this->request->data['Status']['id'] == 18){ ?>
                    <hr>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Usuário cancelamento</label>
                        <p><?php echo $this->request->data['UsuarioCancelamento']['id'] != null ? $this->request->data['UsuarioCancelamento']['name'] : '' ?></p>
                    </div>
                    <hr>
                <?php } ?>
            <?php } ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número do documento</label>
                <?php echo $this->Form->input('doc_num', ["placeholder" => "Número do documento", "class" => "form-control mb-3 mb-lg-0", "readonly" => true]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cliente</label>
                <?php echo $this->Form->input('customer_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Pedido</label>
                <?php echo $this->Form->input('order_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
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
                <label class="form-label">Valor desconto</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('valor_desconto', ["type" => "text", "placeholder" => "Valor desconto", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
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
                <label class="form-label">Data Competência</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('data_competencia', ["type" => "text", "placeholder" => "Data Competência", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <label class="form-label">Vencimento</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('vencimento', ["type" => "text", "placeholder" => "Vencimento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Receita</label>
                <?php echo $this->Form->input('revenue_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Centro de custo</label>
                <?php echo $this->Form->input('cost_center_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                <?php echo $this->Form->input('payment_method', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => $payment_method]);?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Recorrência</label>
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input recorrencia" type="radio" value="1" name="data[Income][recorrencia]" id="recorrSim" <?php echo (isset($this->data['Income']) ? ($this->data['Income']['recorrencia'] == 1 ? 'checked' : '') : '') ?> />
                    <label class="form-check-label me-3" for="recorrSim">
                        Sim
                    </label>
                    <input class="form-check-input recorrencia" type="radio" value="2" name="data[Income][recorrencia]" id="recorrNão" <?php echo (isset($this->data['Income']) ? ($this->data['Income']['recorrencia'] == 2 ? 'checked' : '') : 'checked') ?> />
                    <label class="form-check-label" for="recorrNão">
                        Não
                    </label>
                </div>
            </div>

            <div id="incomeRecorrencia">
                <?php if ($form_action != 'edit' || $this->request->data['Income']['recorrencia'] == 2){ ?>
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
                        <p><?php echo $this->request->data['Income']['parcela'].'ª' ?></p>
                    </div>
                <?php } ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observações</label>
                <?php echo $this->Form->input('observation', ["placeholder" => "Observações", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="javascript:;" onclick="history.go(-1)" class="btn btn-light-dark">Voltar</a>
                    <?php if (isset($id)){ ?>
                        <?php if ($this->request->data['Status']['id'] != 17 || CakeSession::read("Auth.User.Group.id") == 1){ ?>
                            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                        <?php } ?>
                        <?php if (($this->request->data['Status']['id'] == 15 || $this->request->data['Status']['id'] == 16) && $this->request->data['Income']['cnab_gerado'] == 1){ ?>
                            <a href="<?php echo $this->base.'/incomes/gerar_boleto/'.$this->request->data["Income"]["id"].'/1'; ?>" class="btn btn-success">Ver boleto</a>
                            <?php if ($this->request->data["CnabItem"]["id_web"] && $this->request->data["BankAccount"]["bank_id"] != 9){ ?>
                                <a href="<?php echo $this->base.'/boletos/alterar_boleto/'.$this->request->data["CnabItem"]["id_web"]; ?>" class="btn btn-primary">Alterar boleto</a>
                            <?php } ?>
                        <?php } ?>
                        <?php if (in_array($this->request->data['Status']['id'], [15, 16, 19, 51])){ ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-success">Baixar conta</a>
                        <?php } ?>
                        <?php if (($this->request->data['Status']['id'] == 16 || $this->request->data['Status']['id'] == 19) && $cancelarConta) { ?>
                            <a href="<?php echo $this->base.'/incomes/change_status/'.$id.'/18/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-danger">Cancelar conta</a>
                        <?php } ?>
                        <?php if (!$cancelarConta && $this->request->data['Status']['id'] != 58) { ?>
                            <a href="<?php echo $this->base.'/incomes/change_status/'.$id.'/58'; ?>" class="btn btn-danger">Solicitar Cancelamento</a>
                        <?php } else if ($cancelarConta && $this->request->data['Status']['id'] == 58) { ?>
                            <a href="<?php echo $this->base.'/incomes/change_status/'.$id.'/18'; ?>" class="btn btn-danger">Aprovar Cancelamento</a>
                            <a href="<?php echo $this->base.'/incomes/change_status/'.$id.'/15'; ?>" class="btn btn-danger">Reprovar Cancelamento</a>
                        <?php } ?>
                        
                    <?php } else { ?>
                        <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php } ?>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Baixar conta</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo $this->Form->create('Income', array("id" => "js-form-submit", "class" => "form-horizontal", "action" => '../incomes/baixar_titulo/', "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
                <input type="hidden" name="data[Income][status_id]" value="17">
                <div class="modal-body">
                    <div class="mb-7">
                        <label class="form-label">Valor recebido</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <?php echo $this->Form->input('valor_pago', ["type" => "text", "required" => false, "placeholder" => "Valor recebido", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="form-label">Data de Recebimento</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <?php echo $this->Form->input('data_pagamento', ["type" => "text", "required" => true, "placeholder" => "Data de Recebimento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                        <?php echo $this->Form->input('payment_method_baixa', array("required" => true, "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione", 'options' => $payment_method));  ?>
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
