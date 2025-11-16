<?php echo $this->element("../Orders/_totais_index"); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->base.'/orders/index/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>
                   
                    <?php if ($filtersFilled) { ?>
                        <?php if ($isSingleCustomer) { ?>
                            <button type="button" id="js_unificar_nota" class="btn btn-sm btn-primary me-3 d-flex align-items-center justify-content-center fs-6">
                                <i class="fas fa-download"></i>
                                Unificar nota de débito
                            </button>
                        <?php } ?>
                        
                        <a href="<?php echo $this->base . '/orders/relatorio_processamento_index?' . $queryString; ?>" class="btn btn-sm btn-primary me-3 d-flex align-items-center justify-content-center fs-6">
                            <i class="fas fa-download"></i>
                            Relatorio de Processamento
                        </a>
                    <?php } ?>
                    
                    <a href="#" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modal_gerar_arquivo">
                        <i class="fas fa-file"></i>
                        Novo Pedido
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>

                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t[]" id="t" multiple>
                                    <option value=''></option>
                                    <?php
                                        $statusOptions = [83 => "Início", 84 => "Aguardando Pagamento", 85 => "Pagamento Confirmado", 86 => "Em Processamento", 104 => "Aguardando Liberação de Crédito", 87 => "Finalizado", 94 => "Cancelado"];

                                        foreach ($statusOptions as $statusId => $statusName) {
                                            $selected = ($_GET["t"] ?? '') && in_array($statusId, $_GET["t"]) ? 'selected' : '';
                                            echo '<option value="'.$statusId.'" '.$selected.'>'.$statusName.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Cliente:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="cliente[]" id="cliente" multiple="multiple">
                                    <option value=""></option>
                                    <?php foreach ($customers as $id => $nome): ?>
                                        <option value="<?= $id ?>" 
                                            <?= isset($_GET['cliente']) && in_array($id, (array)$_GET['cliente']) ? 'selected' : '' ?>>
                                            <?= h($nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                           
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-group input-daterange" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data Pagamento:</label>
                                <div class="input-group input-daterange" id="datepicker">
                                    <input class="form-control" id="de_pagamento" name="de_pagamento" value="<?php echo isset($_GET["de_pagamento"]) ? $_GET["de_pagamento"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate_pagamento" name="ate_pagamento" value="<?php echo isset($_GET["ate_pagamento"]) ? $_GET["ate_pagamento"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Tipo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="tipo[]" id="tipo" multiple>
                                    <option value=''></option>
                                    <option value="2" <?php echo isset($_GET['tipo']) && in_array('2', $_GET['tipo']) ? 'selected' : ''; ?>>Automático</option>
                                    <option value="4" <?php echo isset($_GET['tipo']) && in_array('4', $_GET['tipo']) ? 'selected' : ''; ?>>Emissão</option>
                                    <option value="1" <?php echo isset($_GET['tipo']) && in_array('1', $_GET['tipo']) ? 'selected' : ''; ?>>Importação</option>
                                    <option value="3" <?php echo isset($_GET['tipo']) && in_array('3', $_GET['tipo']) ? 'selected' : ''; ?>>PIX</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Gestão Eficiente:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="ge" id="ge">
                                    <option value=''></option>
                                    <option value="1" <?php echo isset($_GET['ge']) && $_GET['ge'] == '1' ? 'selected' : ''; ?>>Sim</option>
                                    <option value="2" <?php echo isset($_GET['ge']) && $_GET['ge'] == '2' ? 'selected' : ''; ?>>Não</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">NFSe Antecipada:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="antecipada" id="antecipada">
                                    <option value=''></option>
                                    <option value="S" <?php echo isset($_GET['antecipada']) && $_GET['antecipada'] == 'S' ? 'selected' : ''; ?>>Sim</option>
                                    <option value="N" <?php echo isset($_GET['antecipada']) && $_GET['antecipada'] == 'N' ? 'selected' : ''; ?>>Não</option>
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
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <?php if ($filtersFilled) { ?>
                            <?php if ($isSingleCustomer) { ?>
                                <th class="ps-4 w-80px min-w-80px rounded-start">
                                    <input type="checkbox" class="check_all">
                                </th>
                            <?php } ?>
                        <?php } ?>
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Código</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data de criação</th>
                        <th>Data Pagamento</th>
                        <th>Data Finalização</th>
                        <th>Vencimento</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Taxa</th>
                        <th>Desconto</th>
                        <th>TPP</th>
                        <th>Fee Economia</th>
                        <th>Cliente</th>
                        <th>Economia</th>
                        <th data-priority="1"><?php echo $this->Paginator->sort('Order.total', 'Total'); ?> <?php echo $this->Paginator->sortKey() == 'Order.total' ? "<i class='fas fa-sort-".($this->Paginator->sortDir() == 'asc' ? 'up' : 'down')."'></i>" : ''; ?></th>
                        <th>Usuário</th>
                        <th>Grupo Econômico</th>
                        <th>Tipo</th>
                        <th>Gestão Eficiente</th>
                        <th>NFSe Antecipada</th>
                        <th>Condição de pagamento</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <?php 
                                $fee_economia = 0;
                                $total_economia = 0;
                                $vl_economia = $data[$i][0]["total_balances"];
                                $fee_saldo = $data[$i]["Order"]["fee_saldo_not_formated"];

                                if ($fee_saldo != 0 and $vl_economia != 0) {
                                    $fee_economia = (($fee_saldo / 100) * ($vl_economia));
                                }

                                $vl_economia = ($vl_economia - $fee_economia);
                                $total_economia = ($vl_economia + $fee_economia);

                                $v_is_partial = "";
                                if ($data[$i]['Order']['is_partial'] == 1) {
                                    $v_is_partial = "Importação";
                                } elseif ($data[$i]['Order']['is_partial'] == 2) {
                                    $v_is_partial = "Automático";
                                } elseif ($data[$i]['Order']['is_partial'] == 3) {
                                    $v_is_partial = "PIX";
                                } elseif ($data[$i]['Order']['is_partial'] == 4) {
                                    $v_is_partial = "Emissão";
                                }
                            ?>
                            <tr>
                                <?php if ($filtersFilled) { ?>
                                    <?php if ($isSingleCustomer) { ?>
                                        <td class="fw-bold fs-7 ps-4">
                                            <input type="checkbox" name="alt_linha" class="check_individual" value="<?php echo $data[$i]["Order"]["id"] ?>">
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><a href="<?php echo $this->base.'/orders/edit/'.$data[$i]["Order"]["id"]; ?>"><?php echo $data[$i]["Order"]["id"]; ?></a></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date("d/m/Y H:i:s", strtotime($data[$i]['Order']['created_nao_formatado'])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>     
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["end_date"]; ?></td>  
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["due_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["tpp_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($fee_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($vl_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($total_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerCreator"]["name"] != '' ? $data[$i]["CustomerCreator"]["name"] : $data[$i]["Creator"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['EconomicGroup']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $v_is_partial ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]['pedido_complementar'] == 1 ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]['emitir_nota_fiscal'] == 'A' ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["desc_condicao_pagamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base . '/orders/edit/' . $data[$i]["Order"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <?php if ($data[$i]["Status"]["id"] == '83' || CakeSession::read('Auth.User.group_id') == 1) { ?>
                                        <a href="javascript:" onclick="verConfirm_Pedido('<?php echo $this->base . '/orders/delete/' . $data[$i]["Order"]["id"]; ?>', 'Deseja excluir o pedido <?php echo $data[$i]["Order"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                            Excluir
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" id="modal_unificar_nota" tabindex="-1" aria-labelledby="modalUnificarNotaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUnificarNotaLabel">Unificar Nota de Débito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_unificar_nota">
                    <div class="mb-3">
                        <label for="valor_unificado" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor_unificado" name="valor_unificado">
                    </div>
                    <div class="mb-3">
                        <label for="motivo_unificado" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivo_unificado" name="motivo_unificado" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_salvar_unificar">Gerar Nota</button>
            </div>
        </div>
    </div>
</div>

<?php echo $this->element("../Orders/_modal_adicionar"); ?>

<script id="template_order" type="text/x-handlebars-template">
    <option value="{{id}}">{{name}}</option>
</script>

<script>
    function verConfirm_Pedido(locate, str){
        string =  '<div id="myModal" class="modal fade in" >'+
                                '<div class="modal-dialog">'+
                                    '<div class="modal-content">'+
                                            '<div class="modal-header bg-danger">'+
                                                '<h3 id="myModalLabel" class="text-white"><i class="fa fa-trash-o fa-3"></i> '+str+'</h3>'+
                                                '<button type="button" class="close btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>'+
                                            '</div>'+
                                            '<div class="modal-body">'+
                                                'Importante: Esta não poderá ser desfeita.'+
                                            '</div>'+
                                            '<div class="modal-footer">'+
                                                '<button class="btn" data-bs-dismiss="modal" aria-hidden="true">N&atilde;o</button>'+
                                                '<button class="btn btn-danger" onClick="javascript: window.location ='+ " '" + locate +"' "+'">Sim</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                            '</div>';

        $(string).modal("show");
    }
</script>

<?php echo $this->Html->script('orders'); ?>
