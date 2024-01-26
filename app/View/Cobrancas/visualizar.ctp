<?php echo $this->Html->script('moeda', ['inline' => false]); ?>

<?php
    $valor_com_multa = $this->data['Income']['valor_total_nao_formatado'] + $juros_multa['juros_multa'];
?>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo $this->here; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $this->base.'/cobrancas/historico/'.$id; ?>">Histórico</a>
    </li>
</ul>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Income', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <input type="hidden" id="cobrar_juros" value="<?php echo $this->data['Customer']['cobrar_juros'] ?>">
            <input type="checkbox" class="check_individual" checked data-id="<?php echo $this->data['Income']['id'] ?>" data-valor="<?php echo $this->data['Income']['valor_total_nao_formatado']; ?>" style="display:none">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <p><?php echo $this->data['Status']['name'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número do documento</label>
                <p><?php echo $this->data['Income']['doc_num'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cliente</label>
                <p><?php echo $this->data['Customer']['nome_primario'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição da conta</label>
                <p><?php echo $this->data['Income']['name'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Valor bruto</label>
                <p>R$ <?php echo $this->data['Income']['valor_bruto'] ?></p>
                <input type="hidden" id="valor_bruto" value="<?php echo $this->data['Income']['valor_bruto_nao_formatado'] ?>">
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Valor multa</label>
                <p>R$ <?php echo /*$this->data['Income']['valor_multa']*/ number_format($juros_multa['juros_multa'], 2, ',', '.')?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Valor liquido</label>
                <p>R$ <?php echo number_format($valor_com_multa, 2, ',', '.') ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta bancária</label>
                <p><?php echo $this->data['BankAccount']['name'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Vencimento</label>
                <p><?php echo $this->data['Income']['vencimento'] ?></p>
                <input type="hidden" id="vencimento_nao_formatado" value="<?php echo $this->data['Income']['vencimento_nao_formatado'] ?>">
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Receita</label>
                <p><?php echo $this->data['Revenue']['name'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Centro de custo</label>
                <p><?php echo $this->data['CostCenter']['name'] ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Recorrência</label>
                <p><?php echo $this->data['Income']['recorrencia'] == 1 ? 'Sim' : 'Não' ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Parcela</label>
                <p><?php echo $this->data['Income']['parcela'].'ª' ?></p>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observações</label>
                <p><?php echo $this->data['Income']['observation'] ?></p>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/cobrancas/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <!-- <th style="width: 8%;">
                            <input type="checkbox" id="check_all"> Marcar todos
                        </th> -->
                        <th class="ps-4 w-250px min-w-250px rounded-start">Descrição</th>
                        <th>Conta bancária</th>
                        <th>Vencimento</th>
                        <th>Parcela</th>
                        <th>Valor a receber R$</th>
                        <th class="w-150px min-w-150px rounded-end">Açōes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($demais_pendencias) { ?>
                        <?php for ($i=0; $i < count($demais_pendencias); $i++) { ?>
                            <tr>
                                <!-- <td>
                                    <input type="checkbox" class="check_conta check_individual" data-id="<?php echo $demais_pendencias[$i]["Income"]["id"] ?>" data-valor="<?php echo $demais_pendencias[$i]["Income"]["valor_total"];?>">
                                </td> -->
                                <td class="fw-bold fs-7 ps-4"><?php echo $demais_pendencias[$i]["Income"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo !empty($demais_pendencias[$i]["Income"]["BankAccount"]) ? $demais_pendencias[$i]["Income"]["BankAccount"]["name"] : ''; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($demais_pendencias[$i]["Income"]["vencimento"])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $demais_pendencias[$i]["Income"]["parcela"].'ª'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($demais_pendencias[$i]["Income"]["valor_total"], 2, ',', '.'); ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/cobrancas/visualizar/'.$demais_pendencias[$i]["Income"]["id"]; ?>" class="btn btn-info btn-xs">
                                        Visualizar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="7">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="#modalNovaCobranca" role="button" class="btn btn-success" data-bs-toggle="modal">Reportar cobrança</a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" tabindex="-1" id="modalNovaCobranca" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reportar cobrança</h4>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo $this->Form->create('ChargesHistory', ["id" => "js-form-submit", "class" => "form-horizontal", "action" => "../cobrancas/save_historico/".$id, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
                <div class="modal-body">
                    <input type="hidden" name="idsContasReceber" id="idsContasReceber" value="">
                    <input type="hidden" name="data[ChargesHistory][customer_id]" value="<?php echo $this->data['Income']['customer_id'] ?>">

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Status da ligação</label>
                        <?php echo $this->Form->input('call_status', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => ["1" => "Com sucesso", "2" => "Sem sucesso"]]); ?>
                    </div>

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Histórico</label>
                        <?php echo $this->Form->input('text', ["placeholder" => "Histórico", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                    </div>

                    <div class="ligacao_sucesso" style="display:none">

                        <div class="mb-7 col">
                            <label class="fw-semibold fs-6 mb-2">Enviar novamente boleto?</label>
                            <?php echo $this->Form->input('resend_billet', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => ["1" => "Sim", "2" => "Não"]]); ?>
                        </div>
                        
                        <div class="mb-7 col">
                            <label class="fw-semibold fs-6 mb-2">Atualizar boleto?</label>
                            <?php echo $this->Form->input('generate_new_income', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => ["1" => "Sim", "2" => "Não"]]); ?>
                        </div>

                        <div class="mb-7 retorno" style="display:none">
                            <label class="form-label">Data de retorno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <?php echo $this->Form->input('return_date', ["type" => "text", "placeholder" => "Data de retorno", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                            </div>
                        </div>

                        <div class="nova_conta" style="display:none">
                            <div class="mb-7">
                                <label class="form-label">Data de vencimento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <?php echo $this->Form->input('due_date', ["type" => "text", "placeholder" => "Data de vencimento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('value', ["type" => "text", "placeholder" => "Valor", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label">Desconto</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('discount', ["type" => "text", "placeholder" => "Desconto", "class" => "form-control format_percent nao_valida mb-3 mb-lg-0"]);  ?>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label">Valor total</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('total_value', ["readonly" => true, "type" => "text", "placeholder" => "Valor total", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <a href="<?php echo $this->base.'/customers/edit/'.$this->request->data['Income']['customer_id']; ?>" target="_blank" class="btn btn-primary">Dados do cliente</a>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<input type="hidden" id="valor_contas_unificadas" value="">

<script type="text/javascript">
    $(document).ready(function(){
        $("#check_all").on('click', function(){
            //$(".check_conta").click();
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        })

        $('#modalNovaCobranca').on('show.bs.modal', function () {
            var valorTotal = 0.00;
            var ids = '';
            $(".check_individual:checked").each(function(index, el) {
                valorTotal += parseFloat($(this).data('valor'));

                ids += $(this).data('id')+',';
            });

            $("#idsContasReceber").val(ids);

            $("#valor_contas_unificadas").val(valorTotal);
            valorTotal = formata_dinheiro(valorTotal, 2, ',', '.');
            $("#ChargesHistoryValue").val(valorTotal);
            $("#ChargesHistoryTotalValue").val(valorTotal);
        })

        // js modal
            $("#ChargesHistoryGenerateNewIncome").on("change", function(){
                var val = $(this).val();

                if (val == 1) {
                    $(".nova_conta").show();
                    $(".nova_conta").find('input').not('.nao_valida').prop('required', true);
                    $(".retorno").show();
                    $(".retorno").find('input').val('');
                    $(".retorno").find('input').prop('required', false);

                    var valorTotal = 0.00;
                    var ids = '';
                    $(".check_individual:checked").each(function(index, el) {
                        valorTotal += parseFloat($(this).data('valor'));
                    });

                    valorTotal = formata_dinheiro(valorTotal, 2, ',', '.');
                    $("#ChargesHistoryValue").val(valorTotal);
                    $("#ChargesHistoryTotalValue").val(valorTotal);
                } else {
                    $(".nova_conta").hide();
                    $(".nova_conta").find('input').val('');
                    $(".nova_conta").find('input').prop('required', false);
                    $(".retorno").show();
                    $(".retorno").find('input').prop('required', true);
                }
            })

            $("#ChargesHistoryCallStatus").on("change", function(){
                var val = $(this).val();

                if (val == 1) {
                    $(".ligacao_sucesso").show();
                    $(".ligacao_sem_sucesso").hide();
                    $("#ChargesHistoryResendBillet").val("");
                    $(".ligacao_sucesso").find('select').prop('required', true);
                    $("input:not([type='hidden'])").prop('disabled',false);
                    $("select:not(#ChargesHistoryCallStatus):not(#ChargesHistoryResendBillet)").prop('disabled',false);
                } else {
                    /* comentado por rodolfo devido a nova regra de cobrança do juros no retorno da fatura
                    $(".ligacao_sucesso").hide();
                    $(".ligacao_sem_sucesso").show();
                    $(".ligacao_sucesso").find('input').val('');
                    $(".ligacao_sucesso").find('select').val('');
                    $("input:not([type='hidden'])").prop('disabled',true);
                    $("select:not(#ChargesHistoryCallStatus):not(#ChargesHistoryResendBillet)").prop('disabled',true);
                    */
                    $(".ligacao_sucesso").show();
                    $(".ligacao_sem_sucesso").hide();
                    $("#ChargesHistoryResendBillet").val("");
                    $(".ligacao_sucesso").find('select').prop('required', true);
                    $("input:not([type='hidden'])").prop('disabled',false);
                    $("select:not(#ChargesHistoryCallStatus):not(#ChargesHistoryResendBillet)").prop('disabled',false);
                }
            });

            $("#ChargesHistoryDiscount").on("change", function(){
                var desconto = $(this).val();
                var valor = $("#ChargesHistoryValue").val();

                calcDiscount(desconto, valor);

            });

            $('.money_exchange').maskMoney({
                decimal: ',',
                thousands: '.',
                precision: 2
            });

            $('.format_percent').maskMoney({
                decimal: '.',
                thousands: '',
                precision: 2
            });

            $(".datepicker").datepicker({format: 'dd/mm/yyyy', weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true, startDate: 'today', daysOfWeekDisabled: "0,6"});
    });

    function calcDiscount(desconto, valor)
    {
        valor = valor.replace('.', '');
        valor = valor.replace(',', '.');

        if (desconto > 30) {
            alert('O desconto não pode ser maior que 30%');
            $(this).val('');
        } else {
            var valor_com_desconto = valor - ((desconto / 100) * valor);
            $("#ChargesHistoryTotalValue").val(formata_dinheiro(valor_com_desconto, 2, ',', '.'));
        }
    }
</script>