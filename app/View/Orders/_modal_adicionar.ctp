<div class="modal fade" id="modal_gerar_arquivo" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gerar Pedido</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form autocomplete="off" action="<?php echo $this->base . '/orders/createOrder' ?>" id="order_creation_form" class="form-horizontal" method="post">
                <input type="hidden" class="flag_gestao_economico">
                <input autocomplete="off" name="hidden" type="text" style="display:none;">
                <div class="modal-body">

                    <div class="row mb-7">
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2 required">Cliente</label>
                            <?php echo $this->Form->input('customer_id', array("id" => "customer_id", "required" => false, 'label' => false, "class" => "form-select form-select-solid fw-bolder", "data-control" => "select2", "data-placeholder" => "Selecione", "data-allow-clear" => "true", "empty" => "Selecione", "options" => $customers)); ?>
                        </div>
                    </div>

                    <div class="row mb-7 div-new-order">
                        <div class="col">
                            <label class="mb-2">Modalidade</label>
                            <div class="row" style=" margin-top: 10px; margin-bottom: 10px; ">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[is_consolidated]" value="1" id="flexRadioChecked1" checked="checked" />
                                        <label class="form-check-label" for="flexRadioChecked1">
                                            Por Cliente
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[is_consolidated]" value="2" id="flexRadioChecked2" />
                                        <label class="form-check-label" for="flexRadioChecked2">
                                            Por Grupo Econômico
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mt-5 opcao_grupo_economico" style="display:none">
                            <select name="grupo_especifico" id="grupo_selecionado" class="form-control">
                            </select>
                        </div>

                        <div class="col">
                            <label class="mb-2">Tipo de Pedido</label>
                            <div class="row">
                                <select name="data[is_partial]" id="is_partial" data-control="select2" class="form-select mb-3 mb-lg-0 select2-hidden-accessible is_partial" data-select2-id="select2-data-tipo_pessoa" tabindex="-1" aria-hidden="true">
                                    <option value="2">Automático</option>
                                    <option value="4">Emissão</option>
                                    <option value="1">Importação</option>
                                    <option value="3">PIX</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-7 js-pedido_parc">
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2 required">Período</label>
                            <div class="input-group">
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="period_from" role="presentation" autocomplete="off" name="period_from">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="period_to" role="presentation" autocomplete="off" name="period_to">
                                </div>
                            </div>
                            <p id="message_classification_period" style="color: red; margin: 0; display:none"></p>
                        </div>
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2">Agendamento do crédito previsto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <?php echo $this->Form->input('credit_release_date', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 credit_datepicker", 'div' => false, 'label' => false, 'readonly']);  ?>
                            </div>
                            <p id="message_classification" style="color: red; margin: 0; display:none">Data do período inicial e agendamento deverá ser maior que hoje e maior que 5 dias úteis</p>
                        </div>
                        <?php
                            $vencTimestamp = strtotime('+30 days');
                            $week = date('w', $vencTimestamp);

                            // Ajusta a data de vencimento se cair no fim de semana
                            if ($week == 6) { // Sábado
                                $vencTimestamp = strtotime('+2 days', $vencTimestamp);
                            } elseif ($week == 0) { // Domingo
                                $vencTimestamp = strtotime('+1 day', $vencTimestamp);
                            }

                            $venc = date('d/m/Y', $vencTimestamp);
                        ?>
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2 required">Data de vencimento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <?php echo $this->Form->input('due_date', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 duedate_datepicker", 'div' => false, 'label' => false, "required" => true, 'default' => $venc]);  ?>
                            </div>
                        </div>
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                            <?php echo $this->Form->input('working_days', ["class" => "form-control mb-3 mb-lg-0 working_days", 'required' => true, 'div' => false, 'label' => false]); ?>
                            <p id="message_wd" style="color: red; margin: 0; display:none"></p>
                        </div>
                    </div>

                    <div class="row mb-7 div-new-order js-pedido_parc">
                        <div class="col">
                            <label class="mb-2">Utilizar Dias Úteis</label>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[working_days_type]" value="1" id="diasUteisChk1" checked="checked" />
                                        <label class="form-check-label" for="diasUteisChk1">
                                            Padrão
                                        </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[working_days_type]" value="2" id="diasUteisChk2" />
                                        <label class="form-check-label" for="diasUteisChk2">
                                            Cadastro de Beneficiários
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col">
                            <label class="mb-2">Tipo Benefício</label>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[is_beneficio]" value="1" id="tipoBeneficioChk1" checked="checked" />
                                        <label class="form-check-label" for="tipoBeneficioChk1">
                                            Todos
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="data[is_beneficio]" value="2" id="tipoBeneficioChk2" />
                                        <label class="form-check-label" for="tipoBeneficioChk2">
                                            Único
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row opcao_tipo_beneficio" style="display:none">
                                <div class="col mt-5">
                                    <select name="benefit_type" id="tipo_beneficio" class="form-control">
                                        <?php 
                                            foreach ($benefit_types as $benefit_type_id => $benefit_type) {
                                                echo '<option value="' . $benefit_type_id . '">' . $benefit_type . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-7 js-pedido_parc">
                        <div class="col-6">
                            <label class="mb-2">Clona pedido anterior?</label>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input clone_order" type="radio" name="data[clone_order]" value="1" id="cloneOrder1" />
                                        <label class="form-check-label" for="cloneOrder1">
                                            Sim
                                        </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input clone_order" type="radio" name="data[clone_order]" value="2" id="cloneOrder2" checked />
                                        <label class="form-check-label" for="cloneOrder2">
                                            Não
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col d-none">
                            <label class="fw-semibold fs-6 mb-2 required">Pedido</label>
                            <?php echo $this->Form->input('clone_order_id', ["id" => "clone_order_select", "required" => false, 'label' => false, "class" => "form-select form-select-solid fw-bolder", "data-control" => "select2", "data-placeholder" => "Selecione", "data-allow-clear" => "true"]); ?>
                        </div>
                    </div>

                    <div class="row mb-7 div-new-order pedido_comp">
                        <div class="col">
                            <label class="mb-2">Gestão Eficiente</label>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input pedido_complementar" type="radio" name="data[pedido_complementar]" value="1" id="pedidoComp1" checked />
                                        <label class="form-check-label" for="pedidoComp1">
                                            Sim
                                        </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input pedido_complementar" type="radio" name="data[pedido_complementar]" value="2" id="pedidoComp2" />
                                        <label class="form-check-label" for="pedidoComp2">
                                            Não
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-secondary" role="alert">
                        <strong>Tipo de Pedido</strong><br>
                        Automático: No cadastro de beneficiário atualize dias úteis e inative benefícios e(ou) beneficiários que não deverão fazer parte do pedido. Após executar essa manutenção pode realizar o pedido preenchendo os campos de acordo com o modal escolhido. Não precisa importar planilha para gerar o pedido.<br>
                        <br>
                        Emissão: Apenas para geração de novos cartões ou remissões.<br>
                        Importação: Opção válida para importação de planilha dentro do pedido.<br>
                        PIX: Opção válida para importação de planilha dentro do pedido.<br>
                        <br>
                        <strong>Modalidade</strong><br>
                        Por Cliente: a emissão ocorrerá em um único pedido independente de grupo econômico cadastrado e associado ao beneficiário.<br>
                        Por Grupo Econômico: a emissão ocorrerá de acordo com o(s) grupo(s) cadastrado(s) e beneficiário(s) associado(s).<br>
                        <br>                        
                        <strong>Relatórios</strong><br>
                        Pedidos com “status finalizado ou cancelado” demonstram que o processo foi concluído em todas as etapas. Todos os demais status envolvendo os processamentos poderão sofrer atualizações ao longo desses processamentos. 
                        <br>
                        <br>
                        <strong>Calendário de Datas</strong><br>
                        Período: Obrigatório informar a primeira e a última data de utilização do benefício considerando sempre as regras de disponibilização dos créditos. <br>
                        Agendamento do crédito previsto:  a contagem inicia após o pagamento do boleto. <br>
                        Data de vencimento: considere a data em que deseja a disponibilização dos créditos e informe a data que pretende pagar. Os processos iniciam após a identificação do pagamento via compensação bancária.<br>
                        Utilizar dias úteis: se optar por padrão informe no campo Dias úteis.<br>  
                        Utilizar dias úteis: se optar por cadastro de beneficiário realize primeiro a manutenção dos dias no cadastro antes de iniciar a geração do pedido.<br>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Gerar</button>
                </div>
            </form>
        </div>
    </div>
</div>
