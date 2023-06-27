<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "clientes")); ?>" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->base.'/reports/clientes/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <div class="h-500px menu menu-sub menu-sub-dropdown overflow-auto w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Plano:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="p" id="p">
                                    <option></option>
                                    <?php
                                        foreach ($plans as $planId => $planDesc) {
                                            $selected = "";
                                            if (isset($_GET["p"]) && $planId == $_GET["p"]) {
                                                $selected = "selected";
                                            }
                                            echo '<option value="'.$planId.'" '.$selected.'>'.$planDesc.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Vendedor:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($vendedores); $a++){
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($vendedores[$a]['Seller']['id'] == $_GET["t"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$vendedores[$a]['Seller']['id'].'" '.$selected.'>'.$vendedores[$a]['Seller']['nome_fantasia'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($statuses); $a++){
                                            $selected = "";
                                            if (isset($_GET["s"])) {
                                                if($statuses[$a]['Status']['id'] == $_GET["s"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$statuses[$a]['Status']['id'].'" '.$selected.'>'.$statuses[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Estado:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="e" id="e">
                                    <option></option>
                                    <?php
                                        foreach($estados as $key => $value){
                                            $selected = "";
                                            if (isset($_GET["e"])) {
                                                if($key == $_GET["e"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Cidade:</label>
                                <div class="col d-flex align-items-center">
                                    <span class="position-absolute ms-6">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-solid ps-15" id="c" name="c" value="<?php echo isset($_GET["c"]) ? $_GET["c"] : ""; ?>" placeholder="Buscar" />
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data cadastro:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data cancelamento:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="canc_de" name="canc_de" value="<?php echo isset($_GET["canc_de"]) ? $_GET["canc_de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="canc_ate" name="canc_ate" value="<?php echo isset($_GET["canc_ate"]) ? $_GET["canc_ate"] : ""; ?>">
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Data Cadastro</th>
                        <th>Código</th>
                        <th>Nome fantasia</th>
                        <th>Cidade</th>
                        <th>UF</th>
                        <th>Plano</th>
                        <th>Valor</th>
                        <th>Data plano</th>
                        <th>Vendedor</th>
                        <th class="w-200px min-w-200px rounded-end">Data Cancelamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Statuses"]["label"] ?>'>
                                        <?php echo $data[$i]["Statuses"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]["Customer"]["created"])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["cidade"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["estado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Plan']['description']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['PlanCustomer']['mensalidade']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($data[$i]["PlanCustomer"]["created"] ? date('d/m/Y', strtotime($data[$i]["PlanCustomer"]["created"])) : ''); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Seller"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($data[$i][0]["dataCancelamento"] ? date('d/m/Y', strtotime($data[$i][0]["dataCancelamento"])) : ''); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="10">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);
            $("#p").val(null);
            $("#t").val(null);
            $("#s").val(null);
            $("#e").val(null);
            $("#c").val(null);
            $("#de").val(null);
            $("#ate").val(null);
            $("#canc_de").val(null);
            $("#canc_ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>