<?php
    $url = $this->here;
    echo $this->element("abas_extrato_report", array('url' => $url));
?>

<?php if ($totalOrders) { ?>
<div class="row mb-xl-5">
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2"><?php echo $totalOrders[0]['qtde_pedidos'] ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Qtde. Pedidos</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2"><?php echo $totalOrders[0]['qtde_order_customers'] ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Qtde. Colaboradores</span>
                    </div>
                </div>

                <?php if (isset($first_order[0]['data_criacao'])) { ?>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2"><?php echo date("d/m/Y", strtotime($first_order[0]['data_criacao'])) ?></span>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Data do Primeiro Pedido</span>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="m-0">
                        <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['subtotal'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Volume</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['transfer_fee'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Repasse</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['commission_fee'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Taxa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['vl_tpp'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">TPP</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['desconto'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Desconto</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['total_balances'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total Economia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-xl-5">
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_bal_ajuste_cred,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Ajuste Creditado</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_bal_ajuste_deb,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Ajuste Debitado</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_bal_inconsistencia,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Inconsistência</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_fee_economia,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">VLB (Fee economia)</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_vl_economia,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">VLC</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_vlca,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">VLCA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_repasse_economia,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Repasse Economia</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_diferenca_repasse,2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Diferença Repasse</span>
                    </div>
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['total'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
    <div class="row mb-xl-5">
        <div class="col">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <div class="m-0">
                        <span class="fw-bold fs-2">Selecione um período para ver o extrato!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "extrato", $tipo)); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15  mb-3 mb-lg-0" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar por Grupo Econômico" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <a href="<?php echo $this->here.'/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t[]" id="t" multiple>
                                    <?php
                                    $statusOptions = [83 => "Início", 84 => "Aguardando Pagamento", 85 => "Pagamento Confirmado", 86 => "Em Processamento", 104 => "Aguardando Liberação de Crédito", 87 => "Finalizado", 94 => "Cancelado"];

                                    foreach ($statusOptions as $statusId => $statusName) {
                                        $selected = ($_GET["t"] ?? '') == $statusId ? 'selected' : '';
                                        echo '<option value="'.$statusId.'" '.$selected.'>'.$statusName.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Clientes:</label>
                                <select class="form-select form-select-solid fw-bolder" multiple data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c[]" id="c[]">
                                    <option value=''></option>
                                    <?php
                                        $ids = $_GET["c"] ?? [];
                                        foreach ($customers as $customerId => $customerName) {
                                            $selected = in_array($customerId, $ids) ? 'selected' : '';
                                            echo '<option value="'.$customerId.'" '.$selected.'>'.$customerName.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Pedido:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="o" id="o">
                                    <option value=''></option>
                                    <?php
                                    foreach ($orders as $id => $customerName) {
                                        $selected = ($_GET["o"] ?? '') == $id ? 'selected' : '';
                                        echo '<option value="'.$id.'" '.$selected.'>'.$id.' - '.$customerName.'</option>';
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
                                    <input class="form-control" id="pagamento_de" name="pagamento_de" value="<?php echo isset($_GET["pagamento_de"]) ? $_GET["pagamento_de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="pagamento_ate" name="pagamento_ate" value="<?php echo isset($_GET["pagamento_ate"]) ? $_GET["pagamento_ate"] : ""; ?>">
                                </div>
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

    <?php if (!empty($data)) { ?>
        <div class="card-body pt-0 py-3">
            <?php echo $this->element("pagination"); ?>
            <br>
            <div class="table-responsive">
                <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Usuário</th>
                        <th>Grupo Econômico</th>
                        <th>Data de criação</th>
                        <th>Número</th>
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
                        <th>Saldo</th>

                        <th>Repasse Economia</th>
                        <th>Valor Pedido Compra</th>
                        <th>Repasse Pedido Compra</th>
                        <th>Diferença Repasse</th>

                        <th>Ajuste Creditado</th>
                        <th>Ajuste Debitado</th>
                        <th class="w-150px min-w-150px rounded-end">Inconsistência</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="16" class="fw-bold fs-7 ps-4"></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo number_format($saldo,2,',','.') ?></td>
                    </tr>
                    <?php if ($data) { ?>
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <?php
                                $data_extrato = $data[$i]['Order']['extrato'];

                                $v_fee_economia             = $data_extrato['v_fee_economia'];
                                $v_vl_economia              = $data_extrato['v_vl_economia'];
                                $v_total_economia           = $data_extrato['v_total_economia'];
                                $v_perc_repasse             = $data_extrato['v_perc_repasse'];
                                $v_repasse_economia         = $data_extrato['v_repasse_economia'];
                                $v_valor_pedido_compra      = $data_extrato['v_valor_pedido_compra'];
                                $v_repasse_pedido_compra    = $data_extrato['v_repasse_pedido_compra'];
                                $v_diferenca_repasse        = $data_extrato['v_diferenca_repasse'];
                                $v_saldo                    = $data_extrato['v_saldo'];
                                
                                $saldo = $saldo + ($v_saldo);
                                
                                $v_total_bal_ajuste_cred    = $data_extrato['v_total_bal_ajuste_cred'];
                                $v_total_bal_ajuste_deb     = $data_extrato['v_total_bal_ajuste_deb'];
                                $v_total_bal_inconsistencia = $data_extrato['v_total_bal_inconsistencia'];
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerCreator"]["name"] != '' ? $data[$i]["CustomerCreator"]["name"] : $data[$i]["Creator"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['EconomicGroup']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['created'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["end_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["tpp_fee"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_fee_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_vl_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_total_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($saldo,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_repasse_economia,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_valor_pedido_compra,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_repasse_pedido_compra,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_diferenca_repasse,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_total_bal_ajuste_cred,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_total_bal_ajuste_deb,2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($v_total_bal_inconsistencia,2,',','.'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <th colspan="16" class="fw-bold fs-7 ps-4">Total:</th>
                    <th class="fw-bold fs-7 ps-4"><?php echo 'R$' . number_format($saldo,2,',','.'); ?></th>
                </tfoot>
                </table>
            </div>
            <?php echo $this->element("pagination"); ?>
        </div>
    <?php } ?>
</div>

<script>
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#c").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });            
    });
</script>
