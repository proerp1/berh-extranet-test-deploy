<?php
    $url = $this->here;
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>

<?php
    $url = $this->here;
    echo $this->element("abas_extrato", array('id' => $id, 'url' => $url));
?>

<div class="row mb-xl-5">
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['subtotal'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Subtotal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['transfer_fee'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Repasse</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
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
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($totalOrders[0]['desconto'],2,',','.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Desconto</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
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

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "customers", "action" => "extrato", $id)); ?>" role="form" id="busca" autocomplete="off">
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
                                $statusOptions = [ 83 => 'Inicio',84 => 'Aguardando Pagamento',85 => 'Pagamento Confirmado',86 => 'Em Processamento',87 => 'Finalizado',18 => 'Cancelado'];

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
                    <th>Data Pagamento</th>
                    <th>Data Finalização</th>
                    <th>Subtotal</th>
                    <th>Repasse</th>
                    <th>Taxa</th>
                    <th>Desconto</th>
                    <th>Total</th>
                    <th>Usuário</th>
                    <th>Grupo Econômico</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                    <?php echo $data[$i]["Status"]["name"] ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['created'] ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>     
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["end_date"]; ?></td>     
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerCreator"]["name"] != '' ? $data[$i]["CustomerCreator"]["name"] : $data[$i]["Creator"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['EconomicGroup']['name'] ?></td>
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

<script>
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#f").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);
            $("#de_pagamento").val(null);
            $("#ate_pagamento").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });            
    });
</script>
