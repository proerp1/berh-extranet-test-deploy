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
                    <div class="col d-flex align-items-center">
                        <?php
                        $tp = isset($_GET['tp']) ? $_GET['tp'] : 'default'; ?>
                        <select name="tp" id="tp" class="form-select">
                            <option value="default" <?php echo $tp == 'default' ? 'selected="selected"' : ''; ?>>Itinerários</option>
                            <option value="dados_bancarios" <?php echo $tp == 'dados_bancarios' ? 'selected="selected"' : ''; ?>>Dados Bancários</option>
                            <option value="residencia" <?php echo $tp == 'residencia' ? 'selected="selected"' : ''; ?>>Residencia</option>
                            <option value="trabalho" <?php echo $tp == 'trabalho' ? 'selected="selected"' : ''; ?>>Trabalho</option>
                        </select>
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
                                    <span class="input-group-text" style="padding: 5px;"> de </span>
                                    <input class="form-control" id="de" name="de" value="<?php echo $de ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="para" value="<?php echo $para; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Clientes:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option>Selecione</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Fornecedores:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sup" id="sup">
                                    <option>Selecione</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status Pedido:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="st" id="st">
                                    <option>Selecione</option>
                                    <?php
                                    foreach ($statuses as $keySt => $status) {
                                        $selected = "";
                                        if (isset($_GET["st"])) {
                                            if ($keySt == $_GET["st"]) {
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
            <?php echo $this->element("table"); ?>
            <?php
            switch ($tp) {
                case 'dados_bancarios':
                    echo $this->element("../Reports/_report_dados_bancarios");
                    break;

                case 'residencia':
                    echo $this->element("../Reports/_report_residencia");
                    break;

                case 'trabalho':
                    echo $this->element("../Reports/_report_trabalho");
                    break;

                default:
                    echo $this->element("../Reports/_report_default");
                    break;
            }
            ?>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
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
    $(document).ready(function() {
        trigger_date_change();

        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });

        $('#de').on('change', function() {
            trigger_date_change();
        });
        
        $('#ate').on('change', function() {
            trigger_date_change();
        });

        $('#tp').on('change', function() {
            $("#busca").submit();
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