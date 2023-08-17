<?php $url_novo = $this->base . "/users/add/";  ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">

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
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo $de ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="para" value="<?php echo $para; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Centro de Custo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="cc" id="cc">
                                    <option>Selecione</option>
                                    <?php
                                    foreach ($costCenters as $keyCC => $costCenter) {
                                        $selected = "";
                                        if (isset($_GET["cc"])) {
                                            if ($keyCC == $_GET["cc"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $keyCC . '" ' . $selected . '>' . $costCenter . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Departamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="d" id="d">
                                    <option>Selecione</option>
                                    <?php
                                    foreach ($departments as $keyD => $department) {
                                        $selected = "";
                                        if (isset($_GET["d"])) {
                                            if ($keyD == $_GET["d"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $keyD . '" ' . $selected . '>' . $department . '</option>';
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

                    <button type="submit" class="btn btn-light-primary me-3" name="excel">
                        <i class="fas fa-table"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th>CNPJ</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Departamenrto</th>
                    <th>Código Operadora</th>
                    <th>Código do Benefício (Ìtem)</th>
                    <th>Valor Unitário</th>
                    <th>Quantidade</th>
                    <th>Dias Úteis</th>
                    <th>Var</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($data); $i++) { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerDepartment']["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Supplier']["code"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Benefit']["code"]; ?></td>
                        <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["CustomerUserItinerary"]["unit_price"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["working_days"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["var"]; ?></td>
                        <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["OrderItem"]["subtotal"]; ?></td>
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
        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });
    });
</script>