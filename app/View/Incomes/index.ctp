<?php $url_novo = $this->base."/incomes/add/?".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>
<?php $url_exportar = $this->base."/incomes/?exportar=true&".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>
<?php echo $this->element("abas_contas_receber"); ?>

<div class="row gy-5 g-xl-10">
    <div class="col-lg-4 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-info"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_income[0]["total_income"], 2, ",", '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Contas a receber</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-info rounded">
                        <div class="bg-info rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
    <?php
$clientes = [];
foreach ($data as $item) {
    $id = $item["Customer"]["id"];
    $nome = $item["Customer"]["nome_secundario"];
    if (!isset($clientes[$id])) {
        $clientes[$id] = $nome;
    }
}
?>

    <form action="<?php echo $this->Html->url(array( "controller" => "incomes", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
        <input type="hidden" name="t" value="<?php echo isset($_GET["t"]) ? $_GET["t"] : ""; ?>">
        <div class="card-header border-0 pt-6 mb-3">
            <div class="card-title">
                <div class="row">
                    <div class="col-md-12 d-flex align-items-center my-1">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="top-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $url_exportar; ?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">Novo</a>
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Cliente:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" multiple name="c[]" id="c" data-placeholder="Selecione o(s) cliente(s)">
                                        <?php
                                            foreach ($clientes as $id => $nome_secundario) {
                                                $selected = '';
                                                if (isset($_GET['c']) && is_array($_GET['c']) && in_array($id, $_GET['c'])) {
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="'.$id.'" '.$selected.'>'.$nome_secundario.'</option>';
                                            }
                                        ?>
                                    </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status Cliente:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sc" id="sc">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($statusCliente); $a++){
                                            $selected = "";
                                            if (isset($_GET["sc"])) {
                                                if($statusCliente[$a]['Status']['id'] == $_GET["sc"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$statusCliente[$a]['Status']['id'].'" '.$selected.'>'.$statusCliente[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                                <div class="mb-10">
                                    <label class="form-label fs-5 fw-bold mb-3">Status Financeiro:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                        <option></option>
                                        <?php
                                            for($a = 0; $a < count($status); $a++){
                                                $selected = "";
                                                if (isset($_GET["t"])) {
                                                    if($status[$a]['Status']['id'] == $_GET["t"]){
                                                        $selected = "selected";
                                                    }
                                                }
                                                echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            
                                <div class="mb-10">
                                    <label class="form-label fs-5 fw-bold mb-3">Forma de pagamento:</label>
                                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="payment_method" id="payment_method">
                                        <option></option>
                                        <?php
                                            $payment_method = [
                                                '1' => 'Boleto',
                                                '3' => 'Cartão de crédito',
                                                '6' => 'Crédito em conta corrente',
                                                '5' => 'Cheque',
                                                '4' => 'Depósito',
                                                '7' => 'Débito em conta',
                                                '8' => 'Dinheiro',
                                                '2' => 'Transfêrencia',
                                                '9' => 'Desconto',
                                                '11' => 'Pix',
                                                '10' => 'Outros'
                                            ];
                                            foreach ($payment_method as $key => $label) {
                                                $selected = (isset($_GET['payment_method']) && $_GET['payment_method'] == $key) ? 'selected' : '';
                                                echo "<option value=\"$key\" $selected>$label</option>";
                                            }
                                        ?>
                                    </select>
                                </div>


                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Franquias:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="f" id="f">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($codFranquias); $a++){
                                            $selected = "";
                                            if (isset($_GET["f"])) {
                                                if($codFranquias[$a]['Resale']['id'] == $_GET["f"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$codFranquias[$a]['Resale']['id'].'" '.$selected.'>'.$codFranquias[$a]['Resale']['nome_fantasia'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Vencimento:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Criação:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="created_de" name="created_de" value="<?php echo isset($_GET["created_de"]) ? $_GET["created_de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="created_ate" name="created_ate" value="<?php echo isset($_GET["created_ate"]) ? $_GET["created_ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Competência:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="comp_de" name="comp_de" value="<?php echo isset($_GET["comp_de"]) ? $_GET["comp_de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="comp_ate" name="comp_ate" value="<?php echo isset($_GET["comp_ate"]) ? $_GET["comp_ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data pagamento:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="pagamento_de" name="pagamento_de" value="<?php echo isset($_GET["pagamento_de"]) ? $_GET["pagamento_de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="pagamento_ate" name="pagamento_ate" value="<?php echo isset($_GET["pagamento_ate"]) ? $_GET["pagamento_ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">NFSe Emitida:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="nfse" id="nfse">
                                    <option></option>
                                    <option value="S" <?php echo isset($_GET['nfse']) && $_GET['nfse'] == 'S' ? 'selected' : '' ?>>Sim</option>
                                    <option value="N" <?php echo isset($_GET['nfse']) && $_GET['nfse'] == 'N' ? 'selected' : '' ?>>Não</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">NFSe Antecipada:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="nfse_antecipada" id="nfse_antecipada">
                                    <option></option>
                                    <option value="S" <?php echo isset($_GET['nfse_antecipada']) && $_GET['nfse_antecipada'] == 'S' ? 'selected' : '' ?>>Sim</option>
                                    <option value="N" <?php echo isset($_GET['nfse_antecipada']) && $_GET['nfse_antecipada'] == 'N' ? 'selected' : '' ?>>Não</option>
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">Descrição</th>
                        <th>Status</th>
                        <th>Pedido</th>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Conta bancária</th>
                        <th>Data pagamento</th>
                        <th>Data de criação</th>
                        <th>Nota Fiscal</th>
                        <th>Antecipada?</th>
                        <th>Vencimento</th>
                        <th>Parcela</th>
                        <th data-priority="1"><?php echo $this->Paginator->sort('Income.valor_total', 'Valor a receber R$'); ?> <?php echo $this->Paginator->sortKey() == 'Income.valor_total' ? "<i class='fas fa-sort-".($this->Paginator->sortDir() == 'asc' ? 'up' : 'down')."'></i>" : ''; ?></th>
                        <th>Valor pago R$</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $row = $data[$i];

                                $nfses = '-';
                                if ($data[$i][0]['nfses']) {
                                    $nfses = collect(explode(',', $data[$i][0]['nfses']))->map(function ($nfse_type) use ($row) {
                                        $order_id = $row['Order']['id'];
                                        $type = $nfse_type == 'tpp' ? "1" : "2";
                                        return "$order_id-$type";
                                    })->join(', ');
                                }
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["BankAccount"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date("d/m/Y H:i:s", strtotime($data[$i]['Income']['created_nao_formatado'])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $nfses; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['emitir_nota_fiscal'] == 'A' ? "Sim" : "Não"; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["parcela"].'ª'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                <?php echo isset($payment_method[$data[$i]['Income']['payment_method']]) ? $payment_method[$data[$i]['Income']['payment_method']] : '-'; ?></td>

                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/incomes/edit/'.$data[$i]["Income"]["id"].'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                        <?php if($data[$i]["Status"]["id"]!= 17){?>

                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/incomes/delete/'.$data[$i]["Income"]["id"].'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#sc").val(null).trigger('change');
            $("#t").val(null).trigger('change');
            $("#f").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);
            $("#comp_de").val(null);
            $("#comp_ate").val(null);
            $("#pagamento_de").val(null);
            $("#pagamento_ate").val(null);
            $("#created_de").val(null);
            $("#created_ate").val(null);
            $("#c").val(null);
            $("#payment_method").val(null).trigger('change');



            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>
