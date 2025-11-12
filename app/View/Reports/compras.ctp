<?php echo $this->element("../Reports/_abas_compras"); ?>

<?php echo $this->element("../Reports/_totais_compras"); ?>

<input type="hidden" id="conditions-data" value="<?php echo $conditionsJson; ?>">

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "compras")); ?>" role="form" id="busca" autocomplete="off">
        <input type="hidden" name="aba" id="aba" value="<?php echo $aba; ?>">
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
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <div class="btn-group me-3">
                        <button type="button" class="btn btn-light-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-table"></i>
                            Exportar Relatórios
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo $this->here.'?excel_pedidos&'.$_SERVER['QUERY_STRING'] ?>">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Relatório Pedidos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $this->here.'?excel_simples&'.$_SERVER['QUERY_STRING'] ?>">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Exportar Simples
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $this->here.'?excel&'.$_SERVER['QUERY_STRING'] ?>">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Exportar Completo
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="btn-group me-3">
                        <button type="button" class="btn btn-light-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-table"></i>
                            Gerador de Arquivos
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item btn-gerar-arquivo" href="#" data-tipo="credito">
                                    <i class="fas fa-file-export me-2"></i>
                                    Arquivo Crédito
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item btn-gerar-arquivo" href="#" data-tipo="cadastro">
                                    <i class="fas fa-file-export me-2"></i>
                                    Arquivo Cadastro
                                </a>
                            </li>
                        </ul>
                    </div>

                    <a href="#" id="alterar_sel" class="btn btn-primary me-3">
                        <i class="fas fa-edit"></i>
                        Alterar Status Processamento
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>

                        <div class="px-7 py-5">
                            
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <span class="input-group-text" style="padding: 5px;"> de </span>
                                    <input class="form-control" id="de" name="de" value="<?php echo $de ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="para" value="<?php echo $para; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Clientes:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option value="">Selecione</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Fornecedores:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sup" id="sup">
                                    <option value="">Selecione</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Número(s) de Pedido:</label>
                                <input type="text" class="form-control form-control-solid fw-bolder" name="num" id="num" placeholder="Digite o(s) pedido(s) separado(s) por virgula" value="<?php echo isset($_GET['num']) ? $_GET['num'] : ''; ?>">
                            </div>
                            <div id="selectedNumbers"></div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status Pedido:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="st[]" id="st" multiple>
                                    <option value="">Selecione</option>
                                    <?php
                                    foreach ($statuses as $keySt => $status) {
                                        $selected = "";
                                        if (isset($_GET["st"])) {
                                            if (in_array($keySt, $_GET["st"])) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $keySt . '" ' . $selected . '>' . $status . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status Processamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="stp[]" id="stp" multiple>
                                    <option value="">Selecione</option>
                                    <option value="ARQUIVO_GERADO" <?php echo isset($_GET['stp']) && in_array('ARQUIVO_GERADO', $_GET['stp']) ? 'selected' : ''; ?>>ARQUIVO_GERADO</option>
                                    <option value="CADASTRO_INCONSISTENTE" <?php echo isset($_GET['stp']) && in_array('CADASTRO_INCONSISTENTE', $_GET['stp']) ? 'selected' : ''; ?>>CADASTRO_INCONSISTENTE</option>
                                    <option value="CADASTRO_PROCESSADO" <?php echo isset($_GET['stp']) && in_array('CADASTRO_PROCESSADO', $_GET['stp']) ? 'selected' : ''; ?>>CADASTRO_PROCESSADO</option>
                                    <option value="CARTAO_NOVO" <?php echo isset($_GET['stp']) && in_array('CARTAO_NOVO', $_GET['stp']) ? 'selected' : ''; ?>>CARTAO_NOVO</option>
                                    <option value="CARTAO_NOVO_CREDITO_INCONSISTENTE" <?php echo isset($_GET['stp']) && in_array('CARTAO_NOVO_CREDITO_INCONSISTENTE', $_GET['stp']) ? 'selected' : ''; ?>>CARTAO_NOVO_CREDITO_INCONSISTENTE</option>
                                    <option value="CARTAO_NOVO_PROCESSADO" <?php echo isset($_GET['stp']) && in_array('CARTAO_NOVO_PROCESSADO', $_GET['stp']) ? 'selected' : ''; ?>>CARTAO_NOVO_PROCESSADO</option>
                                    <option value="CREDITO_INCONSISTENTE" <?php echo isset($_GET['stp']) && in_array('CREDITO_INCONSISTENTE', $_GET['stp']) ? 'selected' : ''; ?>>CREDITO_INCONSISTENTE</option>
                                    <option value="CREDITO_PROCESSADO" <?php echo isset($_GET['stp']) && in_array('CREDITO_PROCESSADO', $_GET['stp']) ? 'selected' : ''; ?>>CREDITO_PROCESSADO</option>
                                    <option value="FALHA_GERACAO_ARQUIVO" <?php echo isset($_GET['stp']) && in_array('FALHA_GERACAO_ARQUIVO', $_GET['stp']) ? 'selected' : ''; ?>>FALHA_GERACAO_ARQUIVO</option>
                                    <option value="GERAR_PAGAMENTO" <?php echo isset($_GET['stp']) && in_array('GERAR_PAGAMENTO', $_GET['stp']) ? 'selected' : ''; ?>>GERAR_PAGAMENTO</option>
                                    <option value="INICIO_PROCESSAMENTO" <?php echo isset($_GET['stp']) && in_array('INICIO_PROCESSAMENTO', $_GET['stp']) ? 'selected' : ''; ?>>INICIO_PROCESSAMENTO</option>
                                    <option value="PAGAMENTO_REALIZADO" <?php echo isset($_GET['stp']) && in_array('PAGAMENTO_REALIZADO', $_GET['stp']) ? 'selected' : ''; ?>>PAGAMENTO_REALIZADO</option>
                                    <option value="PROCESSAMENTO_PENDENTE" <?php echo isset($_GET['stp']) && in_array('PROCESSAMENTO_PENDENTE', $_GET['stp']) ? 'selected' : ''; ?>>PROCESSAMENTO_PENDENTE</option>
                                    <option value="VALIDACAO_PENDENTE" <?php echo isset($_GET['stp']) && in_array('VALIDACAO_PENDENTE', $_GET['stp']) ? 'selected' : ''; ?>>VALIDACAO_PENDENTE</option>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Tipo de Benefício:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="bt[]" id="bt" multiple>
                                    <option value="">Selecione</option>
                                    <?php foreach ($benefitTypes as $id => $name): ?>
                                        <option value="<?php echo $id; ?>" <?php echo (isset($_GET['bt']) && in_array($id, $_GET['bt'])) ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Primeiro Pedido:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="first_order" id="first_order">
                                    <option value="">Todos</option>
                                    <option value="sim" <?php echo isset($_GET['first_order']) && $_GET['first_order'] == 'sim' ? 'selected' : ''; ?>>Sim</option>
                                    <option value="nao" <?php echo isset($_GET['first_order']) && $_GET['first_order'] == 'nao' ? 'selected' : ''; ?>>Não</option>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Condição de pagamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="cond_pag" id="cond_pag">
                                    <option value=''></option>
                                    <option value="1" <?php echo isset($_GET['cond_pag']) && $_GET['cond_pag'] == '1' ? 'selected' : ''; ?>>Pré pago</option>
                                    <option value="2" <?php echo isset($_GET['cond_pag']) && $_GET['cond_pag'] == '2' ? 'selected' : ''; ?>>Faturado</option>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status de Pagamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="stpg[]" id="stpg" multiple>
                                    <option value="">Selecione</option>
                                    <?php
                                    foreach ($status_pag as $keySt => $status) {
                                        $selected = "";
                                        if (isset($_GET["stpg"])) {
                                            if (in_array($keySt, $_GET["stpg"])) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $keySt . '" ' . $selected . '>' . $status . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $items ? $this->element("pagination") : '' ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-80px min-w-80px rounded-start">
                        <input type="checkbox" class="check_all">
                    </th>
                    <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                    <th>Código</th>
                    <th>Data de criação</th>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Fornecedor</th>
                    <th>Beneficiário</th>
                    <th>Benefício</th>
                    <th>Tipo Benefício</th>
                    <th>Primeira Compra</th>
                    <th width="90px">Dias Úteis</th>
                    <th width="120px">Quantidade por dia</th>
                    <th>Valor por dia</th>
                    <th>Subtotal</th>
                    <th>Repasse</th>
                    <th>Taxa</th>
                    <th>Total</th>
                    <th>Economia</th>
                    <th>Relatório beneficio</th>
                    <th>Data inicio Processamento</th>
                    <th>Data fim Processamento</th>
                    <th>Status Processamento</th>
                    <th>Motivo Processamento</th>
                    <th>Pedido Operadora</th>
                    <th>Data Entrega</th>
                    <th>ID Conta Pagar</th>
                    <th>Status do Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($items_total[0])) { ?>
                    <tr>
                        <td>Total</td>
                        <td colspan="13"></td>
                        <td class="subtotal_sum">R$<?php echo number_format($items_total[0][0]['subtotal'], 2, ',', '.'); ?></td>
                        <td class="transfer_fee_sum">R$<?php echo number_format($items_total[0][0]['transfer_fee'], 2, ',', '.'); ?></td>
                        <td class="commission_fee_sum">R$<?php echo number_format($items_total[0][0]['commission_fee'], 2, ',', '.'); ?></td>
                        <td class="total_sum">R$<?php echo number_format($items_total[0][0]['total'], 2, ',', '.'); ?></td>
                        <td class="saldo_sum">R$<?php echo number_format($items_total[0][0]['saldo'], 2, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
                <?php
                $v_subtotal = 0;
                $v_transfer_fee = 0;
                $v_commission_fee = 0;
                $v_total = 0;
                $v_saldo = 0;
                if ($items) { ?>
                    <?php for ($i = 0; $i < count($items); $i++) {
                        $v_subtotal += $items[$i]['OrderItem']['subtotal_not_formated'];
                        $v_transfer_fee += $items[$i]['OrderItem']['transfer_fee_not_formated'];
                        $v_commission_fee += $items[$i]['OrderItem']['commission_fee_not_formated'];
                        $v_total += $items[$i]['OrderItem']['total_not_formated'];
                        $v_saldo += $items[$i]['OrderItem']['saldo_not_formated'];
                    ?>
                        <tr class="<?php echo $items[$i]["OrderItem"]["working_days"] != $items[$i]["Order"]["working_days"] ? 'table-warning' : ''; ?>">
                            <td class="fw-bold fs-7 ps-4">
                                <?php echo !$items[$i]["OrderItem"]["outcome_id"] ? '<input type="checkbox" name="alt_linha" class="check_individual" id="">' : ''; ?>
                            </td>
                            <td class="fw-bold fs-7 ps-4">
                                <span class='badge <?php echo $items[$i]["Status"]["label"] ?>'>
                                    <?php echo $items[$i]["Status"]["name"] ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Customer"]["codigo_associado"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]['OrderItem']['created'] ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Order"]["id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Customer"]["nome_primario"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Supplier"]["nome_fantasia"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["Benefit"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["BenefitType"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["first_order"] == 1 ? 'Sim' : 'Não'; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <input type="hidden" class="item_id" value="<?php echo $items[$i]["OrderItem"]["id"]; ?>">
                                <input type="hidden" class="supplier_id" value="<?php echo $items[$i]["Supplier"]["id"]; ?>">
                                <input type="hidden" class="supplier_boleto" value="<?php echo $items[$i]["Supplier"]["tipo_boleto"]; ?>">
                                <?php echo $items[$i]["OrderItem"]["working_days"]; ?>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["manual_quantity"] != 0 ? $items[$i]["OrderItem"]["manual_quantity"] : $items[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $items[$i]["OrderItem"]["price_per_day"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 subtotal_line" data-valor="<?php echo (($items[$i]['OrderItem']['subtotal_not_formated'] - $items[$i]['OrderItem']['saldo_not_formated'])); ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["subtotal"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 transfer_fee_line" data-valor="<?php echo ($items[$i]['OrderItem']['transfer_fee_not_formated'] - $items[$i]['OrderItem']['saldo_transfer_fee_not_formated']); ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["transfer_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 commission_fee_line" data-valor="<?php echo $items[$i]["OrderItem"]["commission_fee_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["commission_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 total_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["saldo"]; ?></td>
                            <td class="fw-bold fs-7 ps-4 total_saldo_line" data-valor="<?php echo $items[$i]["OrderItem"]["total_saldo_not_formated"]; ?>"><?php echo 'R$' . $items[$i]["OrderItem"]["total_saldo"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_inicio_processamento"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_fim_processamento"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["status_processamento"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["motivo_processamento"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["pedido_operadora"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["data_entrega"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["OrderItem"]["outcome_id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $items[$i]["StatusOutcome"]["name"]; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Total</td>
                        <td colspan="12"></td>
                        <td class="subtotal_sum">R$<?php echo number_format($v_subtotal, 2, ',', '.'); ?></td>
                        <td class="transfer_fee_sum">R$<?php echo number_format($v_transfer_fee, 2, ',', '.'); ?></td>
                        <td class="commission_fee_sum">R$<?php echo number_format($v_commission_fee, 2, ',', '.'); ?></td>
                        <td class="total_sum">R$<?php echo number_format($v_total, 2, ',', '.'); ?></td>
                        <td class="saldo_sum">R$<?php echo number_format($v_saldo, 2, ',', '.'); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="50">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php if ($buscar) { ?>
            <?php echo $this->element("pagination"); ?>            
        <?php } ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_alterar_sel" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar Status Processamento</h4>
            </div>
            <div class="modal-body">
                <div class="row mb-7">
                    <div class="col-md-6">
                        <label class="mb-2">Status Processamento</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <select name="status_processamento" id="status_processamento" class="form-select mb-3 mb-lg-0">
                                        <option value="ARQUIVO_GERADO">ARQUIVO_GERADO</option>
                                        <option value="CADASTRO_INCONSISTENTE">CADASTRO_INCONSISTENTE</option>
                                        <option value="CADASTRO_PROCESSADO">CADASTRO_PROCESSADO</option>
                                        <option value="CARTAO_NOVO">CARTAO_NOVO</option>
                                        <option value="CARTAO_NOVO_CREDITO_INCONSISTENTE">CARTAO_NOVO_CREDITO_INCONSISTENTE</option>
                                        <option value="CARTAO_NOVO_PROCESSADO">CARTAO_NOVO_PROCESSADO</option>
                                        <option value="CREDITO_INCONSISTENTE">CREDITO_INCONSISTENTE</option>
                                        <option value="CREDITO_PROCESSADO">CREDITO_PROCESSADO</option>
                                        <option value="FALHA_GERACAO_ARQUIVO">FALHA_GERACAO_ARQUIVO</option>
                                        <option value="GERAR_PAGAMENTO">GERAR_PAGAMENTO</option>
                                        <option value="INICIO_PROCESSAMENTO">INICIO_PROCESSAMENTO</option>
                                        <option value="PAGAMENTO_REALIZADO">PAGAMENTO_REALIZADO</option>
                                        <option value="PROCESSAMENTO_PENDENTE">PROCESSAMENTO_PENDENTE</option>
                                        <option value="VALIDACAO_PENDENTE">VALIDACAO_PENDENTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="mb-2">Pedido Operadora</label>
                        <div class="row">
                            <div class="col">
                                <input type="text" name="pedido_operadora" id="pedido_operadora" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-7">
                    <div class="col-md-6">
                        <label class="mb-2">Data Entrega</label>
                        <div class="row">
                            <div class="col">
                                <input type="text" name="data_entrega" id="data_entrega" class="form-control datepicker">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 js_div_pagamento">
                        <label class="mb-2">Data Vencimento</label>
                        <div class="row">
                            <div class="col">
                                <input type="text" name="data_vencimento" id="data_vencimento" class="form-control datepicker">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-7">
                    <div class="col-md-6 js_div_pagamento">
                        <label class="mb-2">Forma de Pagamento</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <select name="forma_pagamento" id="forma_pagamento" class="form-select mb-3 mb-lg-0">
                                        <option value="1">Boleto</option>
                                        <option value="3">Cartão de crédito</option>
                                        <option value="6">Crédito em conta corrente</option>
                                        <option value="5">Cheque</option>
                                        <option value="4">Depósito</option>
                                        <option value="7">Débito em conta</option>
                                        <option value="8">Dinheiro</option>
                                        <option value="2">Transfêrencia</option>
                                        <option value="11">Pix</option>
                                        <option value="9">Desconto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-7">
                    <div class="col-md-6 js_div_boleto_item">
                        <label class="mb-2">Boleto Total Item</label>
                        <div class="row">
                            <div class="col">
                                <a class="btn btn-default btn-primary me-3">
                                    <input type="file" name="file_item" required="required" notempty="1" data-ui-file-upload="1" class="btn-primary" title="Escolha o documento" id="file_item">
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 js_div_boleto_repasse">
                        <label class="mb-2">Boleto Repasse</label>
                        <div class="row">
                            <div class="col">
                                <a class="btn btn-default btn-primary me-3">
                                    <input type="file" name="file_repasse" required="required" notempty="1" data-ui-file-upload="1" class="btn-primary" title="Escolha o documento" id="file_repasse">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-7 js_div_motivo">
                    <label class="mb-2">Motivo</label>
                    <div class="row">
                        <div class="col">
                            <textarea name="motivo" id="motivo" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div> 

                <div class="row mb-7 js_div_pagamento">
                    <label class="mb-2">Observações</label>
                    <div class="row">
                        <div class="col">
                            <textarea name="observacoes" id="observacoes" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>                
            </div>

            <div class="row mb-7 js_div_valores_totais" style="display: none;">
                <div class="col-12">
                    <div class="card bg-light-primary">
                        <div class="card-body p-5">
                            <h5 class="card-title mb-4">Resumo dos Valores</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="fw-bold text-muted mb-1">Compra Operadora:</label>
                                    <div class="fs-4 fw-bolder text-gray-800" id="display_subtotal">R$ 0,00</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold text-muted mb-1">Compra Repasse Operadora:</label>
                                    <div class="fs-4 fw-bolder text-gray-800" id="display_transfer_fee">R$ 0,00</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold text-muted mb-1">Total:</label>
                                    <div class="fs-4 fw-bolder text-primary" id="display_total">R$ 0,00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" id="canc_confirm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="alterar_confirm">Sim</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_arquivo_confirmacao" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p id="modal_arquivo_mensagem"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_arquivo_confirm" class="btn btn-success">Sim</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showTotalsLoading() {
        $('.total-value').html('<div class="spinner-border spinner-border-sm" role="status"></div>');
    }

    function loadTotals() {
        $.ajax({
            url: '/reports/getTotalOrders',
            method: 'POST',
            data: {
                conditions: $('#conditions-data').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateBasicTotalsDisplay(response.totals);
                    
                    loadEconomia();
                } else {
                    showTotalsError();
                }
            },
            error: function() {
                showTotalsError();
            }
        });
    }

    function loadEconomia() {
        $('#economia-value').html('<div class="spinner-border spinner-border-sm text-warning" role="status"><span class="sr-only">Calculando economia...</span></div>');
        
        $.ajax({
            url: '/reports/getTotalEconomia',
            method: 'POST',
            data: {
                conditions: $('#conditions-data').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#economia-value').text(formatMoney(response.economia));
                } else {
                    $('#economia-value').html('<span class="text-danger">Erro</span>');
                }
            },
            error: function() {
                $('#economia-value').html('<span class="text-danger">Erro ao calcular</span>');
            }
        });
    }

    function updateBasicTotalsDisplay(totals) {
        $('#subtotal-value').text(formatMoney(totals.subtotal));
        $('#repasse-value').text(formatMoney(totals.transfer_fee));
        $('#tpp-value').text(formatMoney(totals.total_tpp));
        $('#taxa-value').text(formatMoney(totals.commission_fee));
        $('#desconto-value').text(formatMoney(totals.desconto));
        $('#total-value').text(formatMoney(totals.total));
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value || 0);
    }

    function showTotalsError() {
        $('.total-value').html('<span class="text-danger">Erro ao carregar</span>');
    }
    
    function trigger_date_change() {
        var v_ini = $("#de").val();
        var v_end = $("#ate").val();

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const curr_c = urlParams.get('c');
        const curr_sup = urlParams.get('sup');

        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "reports", "action" => "getSupplierAndCustomerByDate")); ?>',
            type: 'POST',
            data: {
                ini: v_ini,
                end: v_end
            },
            success: function(data) {

                var obj = JSON.parse(data);
                var html = '<option>Selecione</option>';
                var sel = '';
                for (var i = 0; i < obj.customers.length; i++) {
                    if (obj.customers[i].Customer.id == curr_c) {
                        sel = 'selected';
                    } else {
                        sel = '';
                    }
                    html += '<option value="' + obj.customers[i].Customer.id + '" '+sel+'>' + obj.customers[i].Customer.nome_primario + '</option>';
                }
                $("#c").html(html);

                html = '<option>Selecione</option>';
                var sel_sup = '';
                for (var i = 0; i < obj.suppliers.length; i++) {
                    if (obj.suppliers[i].Supplier.id == curr_sup) {
                        sel_sup = 'selected';
                    } else {
                        sel_sup = '';
                    }
                    html += '<option value="' + obj.suppliers[i].Supplier.id + '" '+sel_sup+'>' + obj.suppliers[i].Supplier.nome_fantasia + '</option>';
                }
                $("#sup").html(html);

                // reload select2
                $("#c").select2();
                $("#sup").select2();
            }
        });
    }

    function fnc_dt_range() {
        $('.filter').attr('disabled', false);

        var dataInicialStr = $('#de').val();
        var dataFinalStr = $('#ate').val();

        var regexData = /^(\d{2})\/(\d{2})\/(\d{4})$/;

        var matchInicial = dataInicialStr.match(regexData);
        var matchFinal = dataFinalStr.match(regexData);

        if (matchInicial && matchFinal) {
            var dataInicial = new Date(matchInicial[3], matchInicial[2] - 1, matchInicial[1]);
            var dataFinal = new Date(matchFinal[3], matchFinal[2] - 1, matchFinal[1]);

            var diff = (dataFinal - dataInicial);
            var diffDays = (diff / (1000 * 60 * 60 * 24));

            if (diffDays > 365 || diffDays < 0) {
                alert('A data final deve ser no máximo 1 ano após a data inicial.');
                $('.filter').attr('disabled', true);

                return false;
            }
        } else {
            alert('Formato de data inválido. Use o formato dd/mm/yyyy.');
            $('.filter').attr('disabled', true);

            return false;
        }
    }

    function enviarArquivo(tipo, btnConfirm) {
        btnConfirm.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        
        let postData = {
            conditions: $('#conditions-data').val(),
            tipo: tipo
        };

        if ($(".check_all").is(':checked')) {
            const notOrderItemIds = [];
            $('input[name="alt_linha"]:not(:checked)').each(function() {
                notOrderItemIds.push($(this).parent().parent().find('.item_id').val());
            });

            postData.notOrderItemIds = JSON.stringify(notOrderItemIds);
        } else {
            const orderItemIds = [];
            $('input[name="alt_linha"]:checked').each(function() {
                orderItemIds.push($(this).parent().parent().find('.item_id').val());
            });

            postData.orderItemIds = JSON.stringify(orderItemIds);
        }

        $.ajax({
            type: 'POST',
            url: base_url + '/reports/send_json_order_items',
            data: postData,
            dataType: 'json',
            success: function(response) {
                $('#modal_arquivo_confirmacao').modal('hide');

                if (response.success) {
                    if (response.file_url) {
                        const link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = '';
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    let msg = response.message || 'Erro ao processar solicitação.';

                    if (response.errors && response.errors.length > 0) {
                        msg += '\n\nErros encontrados:\n\n';
                        response.errors.forEach((erro, i) => {
                            if (erro.mensagem) {
                                msg += (i + 1) + '. CPF: ' + erro.cpf + '\n';
                                msg += '   Nome: ' + erro.nome + '\n';
                                msg += '   Operadora: ' + erro.id_codigo_operadora + '\n';
                                msg += '   Erro: ' + erro.mensagem + '\n\n';
                            }
                        });
                    }

                    alert(msg);
                }

                btnConfirm.prop('disabled', false).html('Sim');
            },
            error: function(xhr) {
                let payload = xhr.responseJSON;
                if (!payload && xhr.responseText) {
                    try { payload = JSON.parse(xhr.responseText); } catch (e) {}
                }

                let msg = (payload && payload.message)
                    ? payload.message
                    : ('Falha ao enviar para o robô (HTTP ' + xhr.status + ')');

                if (payload && Array.isArray(payload.errors) && payload.errors.length > 0) {
                    msg += '\n\nErros encontrados:\n\n';
                    payload.errors.forEach(function(erro, i) {
                        msg += (i + 1) + '. CPF: ' + erro.cpf + '\n';
                        msg += '   Nome: ' + erro.nome + '\n';
                        msg += '   Operadora: ' + erro.id_codigo_operadora + '\n';
                        msg += '   Erro: ' + erro.mensagem + '\n\n';
                    });
                }

                alert(msg);
                btnConfirm.prop('disabled', false).html('Sim');
            }
        });
    }

    function alterarStatusProcessamento(btnAlter) {
        btnAlter.prop('disabled', true);

        $("#canc_confirm").prop('disabled', true);

        const v_status_processamento = $('#status_processamento').val();
        const v_pedido_operadora = $('#pedido_operadora').val();
        const v_data_entrega = $('#data_entrega').val();
        const v_data_vencimento = $('#data_vencimento').val();
        const v_forma_pagamento = $('#forma_pagamento').val();
        const v_motivo = $('#motivo').val();
        const v_observacoes = $('#observacoes').val();
        const file_item = $('#file_item')[0].files[0];
        const file_repasse = $('#file_repasse')[0].files[0];
        
        let formData = new FormData();
        
        formData.append('conditions', $('#conditions-data').val());
        formData.append('v_status_processamento', v_status_processamento);
        formData.append('v_pedido_operadora', v_pedido_operadora);
        formData.append('v_data_entrega', v_data_entrega);
        formData.append('v_data_vencimento', v_data_vencimento);
        formData.append('v_forma_pagamento', v_forma_pagamento);
        formData.append('v_motivo', v_motivo);
        formData.append('v_observacoes', v_observacoes);
        
        if (file_item) {
            formData.append('file_item', file_item);
        }
        if (file_repasse) {
            formData.append('file_repasse', file_repasse);
        }

        if ($(".check_all").is(':checked')) {
            const notOrderItemIds = [];
            $('input[name="alt_linha"]:not(:checked)').each(function() {
                notOrderItemIds.push($(this).parent().parent().find('.item_id').val());
            });
            formData.append('notOrderItemIds', JSON.stringify(notOrderItemIds));
        } else {
            const orderItemIds = [];
            $('input[name="alt_linha"]:checked').each(function() {
                orderItemIds.push($(this).parent().parent().find('.item_id').val());
            });
            formData.append('orderItemIds', JSON.stringify(orderItemIds));
        }

        $.ajax({
            type: 'POST',
            url: base_url + '/reports/alter_item_status_processamento',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Erro ao processar: ' + (response.message || 'Erro desconhecido'));
                    btnAlter.prop('disabled', false);
                    $("#canc_confirm").prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                alert('Erro na requisição: ' + error);
                btnAlter.prop('disabled', false);
                $("#canc_confirm").prop('disabled', false);
            }
        });
    }

    function limparModalAlterarStatus() {
        const $modal = $('#modal_alterar_sel');
        
        $modal.find('input').not('[type="hidden"]').val('');
        $modal.find('textarea').val('');
        $modal.find('select').each(function() {
            $(this).prop('selectedIndex', 0);
            
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).val(null).trigger('change');
            }
        });
        
        $modal.find('.alert').remove();
        
        $('#alterar_confirm').prop('disabled', false);
        $('#canc_confirm').prop('disabled', false);
        
        $('#display_subtotal').text('R$ 0,00');
        $('#display_transfer_fee').text('R$ 0,00');
        $('#display_total').text('R$ 0,00');
        
        $('.js_div_valores_totais').hide();
        $('.js_div_pagamento').hide();
        $('.js_div_motivo').hide();
        $('.js_div_boleto_item').hide();
        $('.js_div_boleto_repasse').hide();
    }

    $(document).ready(function() {
        $(".datepicker").mask("99/99/9999");

        showTotalsLoading();
        loadTotals();
        trigger_date_change();

        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);
            $("#bt").val(null).trigger('change');


            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });

        $('#de').on('change', function() {
            fnc_dt_range();
            trigger_date_change();
        });
        
        $('#ate').on('change', function() {
            fnc_dt_range();
            trigger_date_change();
        });

        $('#tp').on('change', function() {
            $("#busca").submit();
        });

        $('#alterar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="alt_linha"]:checked').length > 0) {
                $('#modal_alterar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a ser alterado.');
            }
        });

        $('#alterar_confirm').on('click', function(e) {
            e.preventDefault();
            alterarStatusProcessamento($(this));
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });

        $('#status_processamento').on('change', function() {
            const v_status = $(this).val();
            const v_op_status = [
                'CARTAO_NOVO',
                'CARTAO_NOVO_CREDITO_INCONSISTENTE',
                'CADASTRO_INCONSISTENTE',
                'CREDITO_INCONSISTENTE',
                'PAGAMENTO_REALIZADO',
                'CARTAO_NOVO_PROCESSADO',
                'GERAR_PAGAMENTO'
            ];

            if (v_op_status.includes(v_status)) {
                $('.js_div_motivo').show();
            } else {
                $('.js_div_motivo').hide();
            }

            const v_op_status_venc = [
                'CARTAO_NOVO_PROCESSADO',
                'GERAR_PAGAMENTO'
            ];

            if (v_op_status_venc.includes(v_status)) {
                $('#display_subtotal').html('<i class="fas fa-spinner fa-spin"></i>');
                $('#display_transfer_fee').html('<i class="fas fa-spinner fa-spin"></i>');
                $('#display_total').html('<i class="fas fa-spinner fa-spin"></i>');
                $('.js_div_valores_totais').show();
                $('.js_div_pagamento').show();
                
                let formData = new FormData();
        
                formData.append('conditions', $('#conditions-data').val());

                if ($(".check_all").is(':checked')) {
                    const notOrderItemIds = [];
                    $('input[name="alt_linha"]:not(:checked)').each(function() {
                        notOrderItemIds.push($(this).parent().parent().find('.item_id').val());
                    });
                    formData.append('notOrderItemIds', JSON.stringify(notOrderItemIds));
                } else {
                    const orderItemIds = [];
                    $('input[name="alt_linha"]:checked').each(function() {
                        orderItemIds.push($(this).parent().parent().find('.item_id').val());
                    });
                    formData.append('orderItemIds', JSON.stringify(orderItemIds));
                }
                
                $.ajax({
                    url: base_url + '/reports/get_total_items',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            
                            $('#display_subtotal').text('R$ ' + data.soma_subtotal.toLocaleString('pt-BR', { 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            }));
                            $('#display_transfer_fee').text('R$ ' + data.soma_transfer_fee.toLocaleString('pt-BR', { 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            }));
                            $('#display_total').text('R$ ' + data.soma_total.toLocaleString('pt-BR', { 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            }));
                            
                            if (data.tipo_boleto == 1) {
                                $('.js_div_boleto_item').show();
                                $('.js_div_boleto_repasse').hide();
                            } else if (data.tipo_boleto == 2) {
                                $('.js_div_boleto_item').show();
                                $('.js_div_boleto_repasse').show();
                            } else {
                                $('.js_div_boleto_item').hide();
                                $('.js_div_boleto_repasse').hide();
                            }
                            
                            if (!data.valid) {
                                $('#modal_alterar_sel .alert').remove();
                                
                                const alertHtml = `
                                    <div class="alert alert-danger alert-dismissible alert_supplier fade show" role="alert">
                                        <strong>Atenção!</strong> Todos os fornecedores devem ser iguais para criar conta a pagar.
                                    </div>
                                `;
                                
                                $('#modal_alterar_sel .modal-body').prepend(alertHtml);
                                $("#alterar_confirm").prop('disabled', true);
                            } else {
                                $('#modal_alterar_sel').find('.alert_supplier').remove();
                                $("#alterar_confirm").prop('disabled', false);
                            }
                        } else {
                            alert(response.message || 'Erro ao calcular totais');
                            $('#display_subtotal').text('R$ 0,00');
                            $('#display_transfer_fee').text('R$ 0,00');
                            $('#display_total').text('R$ 0,00');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Erro ao calcular totais: ' + error);
                        $('#display_subtotal').text('R$ 0,00');
                        $('#display_transfer_fee').text('R$ 0,00');
                        $('#display_total').text('R$ 0,00');
                    }
                });
            } else {
                $('.js_div_pagamento').hide();
                $('.js_div_boleto_item').hide();
                $('.js_div_boleto_repasse').hide();
                $('.js_div_valores_totais').hide();
                $('#modal_alterar_sel').find('.alert_supplier').remove();
                $("#alterar_confirm").prop('disabled', false);
            }
        });

        $('#status_processamento').trigger('change');

        $('#modal_alterar_sel').on('show.bs.modal', function () {
            limparModalAlterarStatus();
        });

        $('#modal_alterar_sel').on('hidden.bs.modal', function () {
            limparModalAlterarStatus();
        });

        let tipoArquivo = '';

        $('.btn-gerar-arquivo').on('click', function(e) {
            e.preventDefault();
            
            if ($('input[name="alt_linha"]:checked').length === 0) {
                alert('Selecione ao menos um item a ser alterado.');
                return;
            }

            tipoArquivo = $(this).data('tipo');
            
            const mensagem = tipoArquivo === 'credito' 
                ? 'Confirma o envio dos dados filtrados para geração do arquivo de crédito?'
                : 'Confirma o envio dos dados filtrados para geração do arquivo de cadastro?';
            
            $('#modal_arquivo_mensagem').text(mensagem);
            $('#modal_arquivo_confirmacao').modal('show');
        });

        $('#btn_arquivo_confirm').on('click', function(e) {
            e.preventDefault();
            enviarArquivo(tipoArquivo, $(this));
        });
    });
</script>

<style>
    table tr th a {
        color: #009ef7;
        display: block;
        width: 100%;
        height: 100%;
    }
</style>
