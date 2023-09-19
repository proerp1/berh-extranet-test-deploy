<?php $url_novo = $this->base . "/users/add/";  ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
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
                                <label class="form-label fs-5 fw-bold mb-3">Clientes:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option>Selecione</option>
                                    <?php
                                    foreach ($customers as $keyCst => $customer) {
                                        $selected = "";
                                        if (isset($_GET["c"])) {
                                            if ($keyCst == $_GET["c"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $keyCst . '" ' . $selected . '>' . $customer . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
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
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table");
            $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
            $is_first = strpos($_SERVER['REQUEST_URI'], '?') !== false ? false : true;
            $qs = $is_first ? $qs : $qs . '&';
            $o = isset($_GET['o']) ? $_GET['o'] : '';
            $dir = isset($_GET['dir']) ? $_GET['dir'] : 'u';

            $o_icon = [
                'nome' => 'fa-sort',
                'cpf' => 'fa-sort',
                'dep' => 'fa-sort',
                'cod_o' => 'fa-sort',
                'cod_b' => 'fa-sort',
                'val_u' => 'fa-sort',
                'qty' => 'fa-sort',
                'wd' => 'fa-sort',
                'var' => 'fa-sort',
                'total' => 'fa-sort',
            ];
            $dir_icon = [
                'nome' => '&dir=u',
                'cpf' => '&dir=u',
                'dep' => '&dir=u',
                'cod_o' => '&dir=u',
                'cod_b' => '&dir=u',
                'val_u' => '&dir=u',
                'qty' => '&dir=u',
                'wd' => '&dir=u',
                'var' => '&dir=u',
                'total' => '&dir=u',
            ];
            if (isset($o_icon[$o])) {
                $o_icon[$o] = $dir == 'u' ? 'fa-sort-up' : 'fa-sort-down';
                $dir_icon[$o] = $dir == 'u' ? '&dir=d' : '&dir=u';
                $option = $is_first ? 'o=' . $o : '&o=' . $o;
                $option2 = '&dir=' . $dir;
                $qs = str_replace([$option, $option2], '', $qs);
            }

            ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th>CNPJ</th>
                    <th>
                        Cliente
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=nome<?php echo $dir_icon['nome']; ?>">
                            <i class="fa <?php echo $o_icon['nome']; ?>"></i>
                            Nome
                        </a> -->
                        Nome
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=cpf<?php echo $dir_icon['cpf']; ?>">
                            <i class="fa <?php echo $o_icon['cpf']; ?>"></i>
                            CPF
                        </a> -->
                        CPF
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=dep<?php echo $dir_icon['dep']; ?>">
                            <i class="fa <?php echo $o_icon['dep']; ?>"></i>
                            Departamenrto
                        </a> -->
                        Departamento
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=cod_o<?php echo $dir_icon['cod_o']; ?>">
                            <i class="fa <?php echo $o_icon['cod_o']; ?>"></i>
                            Código Operadora
                        </a> -->
                        Código Operadora
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=cod_b<?php echo $dir_icon['cod_b']; ?>">
                            <i class="fa <?php echo $o_icon['cod_b']; ?>"></i>
                            Código do Benefício (Ìtem)
                        </a> -->
                        Código do Benefício (Ìtem)
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=val_u<?php echo $dir_icon['val_u']; ?>">
                            <i class="fa <?php echo $o_icon['val_u']; ?>"></i>
                            Valor Unitário
                        </a> -->
                        Valor Unitário
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=qty<?php echo $dir_icon['qty']; ?>">
                            <i class="fa <?php echo $o_icon['qty']; ?>"></i>
                            Quantidade
                        </a> -->
                        Quantidade
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=wd<?php echo $dir_icon['wd']; ?>">
                            <i class="fa <?php echo $o_icon['wd']; ?>"></i>
                            Dias Úteis
                        </a> -->
                        Dias Úteis
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=var<?php echo $dir_icon['var']; ?>">
                            <i class="fa <?php echo $o_icon['var']; ?>"></i>
                            Var
                        </a> -->
                        Var
                    </th>
                    <th>
                        <!-- <a href="/reports/?<?php echo $qs; ?>o=total<?php echo $dir_icon['total']; ?>">
                            <i class="fa <?php echo $o_icon['total']; ?>"></i>
                            Total
                        </a> -->
                        Total
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php for ($i = 0; $i < count($data); $i++) {
                    $total += $data[$i]["OrderItem"]["subtotal_not_formated"];
                ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
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
                <tr>
                    <td colspan="10"></td>
                    <td>Total</td>
                    <td>R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    function trigger_cst_change() {
        var v_cst_id = $('#c').val()
        $.ajax({
            url: '<?php echo $this->Html->url(array("controller" => "reports", "action" => "getDepAndCCByCustomer")); ?>',
            type: 'POST',
            data: {
                customer_id: v_cst_id
            },
            success: function(data) {
                var obj = JSON.parse(data);
                var html = '<option>Selecione</option>';
                console.log(obj);
                for (var i = 0; i < obj.departments.length; i++) {
                    html += '<option value="' + obj.departments[i].CustomerDepartment.id + '">' + obj.departments[i].CustomerDepartment.name + '</option>';
                }
                $("#d").html(html);

                html = '<option>Selecione</option>';
                for (var i = 0; i < obj.costCenters.length; i++) {
                    html += '<option value="' + obj.costCenters[i].CostCenter.id + '">' + obj.costCenters[i].CostCenter.name + '</option>';
                }
                $("#cc").html(html);

                // reload select2
                $("#d").select2();
                $("#cc").select2();
            }
        });
    }
    $(document).ready(function() {
        trigger_cst_change();
        
        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });

        $('#c').on('change', function() {
            trigger_cst_change();
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