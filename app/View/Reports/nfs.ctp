<?php echo $this->element("../Orders/_totais_index"); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "nfs")); ?>" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->base.'/reports/nfs/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option value=''></option>
                                    <?php
                                        $statusOptions = [ 83 => 'Inicio',84 => 'Aguardando Pagamento',85 => 'Pagamento Confirmado',86 => 'Em Processamento', 104 => 'Aguardando Liberação de Crédito',87 => 'Finalizado',18 => 'Cancelado'];

                                        foreach ($statusOptions as $statusId => $statusName) {
                                            $selected = ($_GET["t"] ?? '') == $statusId ? 'selected' : '';
                                            echo '<option value="'.$statusId.'" '.$selected.'>'.$statusName.'</option>';
                                        }
                                    ?>
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
                                <label class="form-label fs-5 fw-bold mb-3">NFSe Antecipada:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="antecipada" id="antecipada">
                                    <option value=''>Selecione</option>
                                    <option value='S' <?php echo isset($_GET["antecipada"]) && $_GET["antecipada"] == 'S' ? 'selected' : ""; ?>>Sim</option>
                                    <option value='N' <?php echo isset($_GET["antecipada"]) && $_GET["antecipada"] == 'N' ? 'selected' : ""; ?>>Não</option>
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Código</th>
                        <th>Data de criação</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data Pagamento</th>
                        <th>Data Finalização</th>
                        <th>Subtotal</th>
                        <th>Repasse</th>
                        <th>Taxa</th>
                        <th>Desconto</th>
                        <th>TPP</th>
                        <th>Fee Economia</th>
                        <th>Cliente</th>
                        <th>Economia</th>
                        <th>Total</th>
                        <th>Nome Documento</th>
                        <th>Arquivo</th>
                        <th>NFSe Antecipada</th>
                        <th>Cobrança Cancelada</th>
                        <th>Pedido Cancelado</th>
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
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['created'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["end_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["tpp_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($fee_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($vl_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($total_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['OrderDocument']['name']; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/files/order_document/file_name/'.$data[$i]['OrderDocument']['id'].'/'.$data[$i]['OrderDocument']['file_name']; ?>" target="_blank"><?php echo $data[$i]['OrderDocument']['file_name']; ?></a>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['emitir_nota_fiscal'] == 'A' ? "Sim" : "Não"; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Income']['status_id'] == 18 ? "Sim" : "Não"; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['status_id'] == 94 ? "Sim" : "Não"; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/order_documents/edit/'.$data[$i]["Order"]["id"].'/'.$data[$i]["OrderDocument"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="20">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>
