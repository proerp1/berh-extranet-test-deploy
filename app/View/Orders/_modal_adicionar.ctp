<div class="modal fade" id="modal_gerar_arquivo" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gerar Pedido</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form autocomplete="off" action="<?php echo $this->base . '/orders/createOrder' ?>" id="order_creation_form" class="form-horizontal" method="post">
                <input type="hidden" class="flag_gestao_economico">
                <input autocomplete="off" name="hidden" type="text" style="display:none;">
                <div class="modal-body">
                    <div class="row mb-7 ">
                        <div class="col-6">
                            <label class="fw-semibold fs-6 mb-2 required">Cliente</label>
                            <?php echo $this->Form->input('customer_id', array("id" => "customer_id", "required" => false, 'label' => false, "class" => "form-select form-select-solid fw-bolder", "data-control" => "select2", "data-placeholder" => "Selecione", "data-allow-clear" => "true", "empty" => "Selecione", "options" => $customers)); ?>
                        </div>
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
                    </div>
                    <div class="row mb-7 ">
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2">Agendamento do crédito previsto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <?php echo $this->Form->input('credit_release_date', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 datepicker", 'div' => false, 'label' => false]);  ?>
                            </div>
                            <p id="message_classification" style="color: red; margin: 0; display:none">Data do período inicial e agendamento deverá ser maior que hoje e maior que 5 dias úteis</p>
                        </div>
                        <div class="col">
                            <label class="fw-semibold fs-6 mb-2 required">Data de vencimento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <?php echo $this->Form->input('due_date', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 duedate_datepicker", 'div' => false, 'label' => false, "required" => true, 'default' => date('d/m/Y', strtotime(' + 30 day'))]);  ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-7">
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
                    <div class="row mb-7 div-new-order">
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
                            <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                            <?php echo $this->Form->input('working_days', ["class" => "form-control mb-3 mb-lg-0", 'required' => true, 'div' => false, 'label' => false]); ?>
                            <p id="message_wd" style="color: red; margin: 0; display:none"></p>
                        </div>
                    </div>
                    <div class="row mb-7 div-new-order">
                        <div class="col">
                            <label class="mb-2">Criação de Pedidos</label>
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
                    </div>
                    <div class="row mb-7 div-new-order">
                        <div class="col">
                            <label class="mb-2">Pedido Parcial</label>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input is_partial" type="radio" name="data[is_partial]" value="2" id="partialOrderChk2" checked="checked" />
                                        <label class="form-check-label" for="partialOrderChk2">
                                            Todos beneficiários
                                        </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input is_partial" type="radio" name="data[is_partial]" value="1" id="partialOrderChk1" />
                                        <label class="form-check-label" for="partialOrderChk1">
                                            Parcial
                                        </label>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input is_partial" type="radio" name="data[is_partial]" value="3" id="partialOrderChk3" />
                                        <label class="form-check-label" for="partialOrderChk3">
                                            PIX
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
                    <div class="row mb-7 div-new-order pedido_comp">
                        <div class="col">
                            <label class="mb-2">Pedido Complementar</label>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Gerar</button>
                </div>
            </form>
        </div>
    </div>
</div>