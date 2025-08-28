
<?php
    if (isset($id)) {
        $url = $this->here;
        echo $this->element("abas_outcomes", array('id' => $id, 'url' => $url));
    }
?>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>

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

        $("#OutcomeVencimento").on("change", function(){
            var vencimento = $("#vencimento_js").val();
            //var data = $('#data_agendamento_js').val();
            var valor = $("#valor_js").val();
            var cobrar_juros = 'N';
            var el = $(this);

            if (data != '' && vencimento != '') {
                $.ajax({
                    url: "<?php echo $this->base?>/outcomes/calc_juros_multa_by_date/",
                    type: "post",
                    data: {data: data, valor: valor, cobrar_juros: cobrar_juros, vencimento: vencimento},
                    dataType: "json",
                    beforeSend: function(xhr){
                        $(".loading_img").remove();
                        el.parent().parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
                    },
                    success: function(data){
                        $(".loading_img").remove();

                        $("#OutcomeValorMulta").val(data.juros);
                        $("#OutcomeValorTotal").val(data.total);
                    }
                });
            };
        });

        $("#OutcomeValorBruto, #OutcomeValorMulta, #OutcomeValorDesconto").on("focusout", function(event){
            calc_valor_total();
        })
    })

   
   

    
    function calc_valor_total(){
        var multa = replaceAll($("#OutcomeValorMulta").val(), ".", "");
        var multa = replaceAll(multa,",", ".");

        var bruto = replaceAll($("#OutcomeValorBruto").val(), ".", "");
        var bruto = replaceAll(bruto, ",", ".");

        var desconto = replaceAll($("#OutcomeValorDesconto").val(), ".", "");
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
            <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Revenda</label>
                <?php echo $this->Form->input('resale_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número do documento</label>
                <?php echo $this->Form->input('doc_num', ["type" => "text", "placeholder" => "Número do documento", "class" => "form-control mb-3 mb-lg-0"]);?>
            </div>


            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Fornecedor</label>
                <?php echo $this->Form->input('supplier_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Pedido</label>
                <?php echo $this->Form->input('order_ids', ["multiple" => true, "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => $orders]);?>
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
                    <?php echo $this->Form->input('valor_total', ["type" => "text", "readonly" => true, "placeholder" => "Valor liquido", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta bancária</label>
                <?php echo $this->Form->input('bank_account_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'default' => 4]);?>
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
                <?php echo $this->Form->input('payment_method', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito',  '7' => 'Débito em conta',  '8' => 'Dinheiro', '2' => 'Transfêrencia', '11' => 'Pix', '9' => 'Desconto']]);?>
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
                    <?php if (isset($id)){ ?>
                        <?php if ($this->request->data['Status']['id'] != 13){ ?>
                            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                        <?php } else { ?>
                            <a href="<?php echo $this->base.'/outcomes/reabrir_conta/'.$id.'/11/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-success">Reabrir conta</a>
                        <?php } ?>
                        <?php if (isset($this->request->data['Status']) && in_array($this->request->data['Status']['id'], [11, 103])): ?>
                            <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/12/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-success">Aprovar conta</a>
                            <?php if ($cancelarConta) { ?>
                                <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/14/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-danger">Cancelar conta</a>
                            <?php } ?>
                        <?php endif ?>
                        <?php if (isset($this->request->data['Status']) && in_array($this->request->data['Status']['id'], [11, 12])): ?>
                            <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/103/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-warning">Marcar como Pendente</a>
                        <?php endif ?>
                        <?php if (isset($this->request->data['Status']) && in_array($this->request->data['Status']['id'], [103])): ?>
                            <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/11/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-warning">Programado</a>
                        <?php endif ?>

                        <?php if (isset($this->request->data['Status']) && in_array($this->request->data['Status']['id'], [12,103])): ?>
                            <!-- <a href="<?php echo $this->base.'/outcomes/change_status/'.$id.'/13/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-success">Conta paga</a> -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalContaPaga" class="btn btn-success">Conta paga</a>
                        <?php endif ?>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php } ?>
                </div>
            </div>

        </form>
    </div>
</div>

<?php echo $this->element('../Outcomes/modal_conta_paga') ?>
