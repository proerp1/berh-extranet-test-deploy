<?php $url_novo = $this->base . "/users/add/";  ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "pedidos")); ?>" role="form" id="busca" autocomplete="off">
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
                <?php if (!empty($_GET['q']) || !empty($_GET['c'])) { ?>
                    <a href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'relatorio_processamento', '?' => $_SERVER['QUERY_STRING'] ]); ?>" class="btn btn-primary me-3">
                        <i class="fas fa-download"></i>
                        Processamento
                    </a>
                <?php } ?>

                    <?php if (!empty($_GET['q']) || !empty($_GET['c'])) { ?>
                        <a href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'demanda_judicial', '?' => $_SERVER['QUERY_STRING']]); ?>" class="btn btn-primary me-3">
                            <i class="fas fa-download"></i>
                            Demanda Judicial
                        </a>
                    <?php } ?>

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
                                <select class="form-select form-select-solid fw-bolder"
                                        data-kt-select2="true"
                                        data-placeholder="Selecione"
                                        data-allow-clear="true"
                                        name="st[]"
                                        id="st"
                                        multiple>
                                    <?php
                                    $selectedStatus = isset($_GET["st"]) && is_array($_GET["st"]) ? $_GET["st"] : [];

                                    foreach ($statuses as $keySt => $status) {
                                        $selected = in_array($keySt, $selectedStatus) ? "selected" : "";
                                        echo '<option value="' . $keySt . '" ' . $selected . '>' . $status . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status Processamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="stp[]" id="stp" multiple>
                                    <option value="ARQUIVO_GERADO" <?php echo isset($_GET['stp']) && in_array('ARQUIVO_GERADO', $_GET['stp']) ? 'selected' : ''; ?>>ARQUIVO_GERADO</option>
                                    <option value="CADASTRO_INCONSISTENTE" <?php echo isset($_GET['stp']) && in_array('CADASTRO_INCONSISTENTE', $_GET['stp']) ? 'selected' : ''; ?>>CADASTRO_INCONSISTENTE</option>
                                    <option value="CADASTRO_PROCESSADO" <?php echo isset($_GET['stp']) && in_array('CADASTRO_PROCESSADO', $_GET['stp']) ? 'selected' : ''; ?>>CADASTRO_PROCESSADO</option>
                                    <option value="CARTAO_NOVO" <?php echo isset($_GET['stp']) && in_array('CARTAO_NOVO', $_GET['stp']) ? 'selected' : ''; ?>>CARTAO_NOVO</option>
                                    <option value="CARTAO_NOVO_CREDITO_INCONSISTENTE" <?php echo isset($_GET['stp']) && in_array('CARTAO_NOVO_CREDITO_INCONSISTENTE', $_GET['stp']) ? 'selected' : ''; ?>>CARTAO_NOVO_CREDITO_INCONSISTENTE</option>
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
                                <select class="form-select form-select-solid fw-bolder"
                                        data-kt-select2="true"
                                        data-placeholder="Selecione"
                                        data-allow-clear="true"
                                        name="bt[]"
                                        id="bt"
                                        multiple>
                                    <?php
                                    $selectedBt = isset($_GET["bt"]) && is_array($_GET["bt"]) ? $_GET["bt"] : [];

                                    foreach ($benefitTypes as $keyBt => $benefitType) {
                                        $selected = in_array($keyBt, $selectedBt) ? "selected" : "";
                                        echo '<option value="' . $keyBt . '" ' . $selected . '>' . $benefitType . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary filter" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
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
        <?php echo $data ? $this->element("pagination") : '' ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-180px min-w-180px rounded-start">CNPJ</th>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Data de criação</th>
                    <th>N° Pedido</th>
                    <th>Status Pedido</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Tipo Beneficio</th>
                    <th>Primeira Compra</th>
                    <th>Dias Úteis</th>
                    <th>Operadora</th>
                    <th>Valor Unitário</th>
                    <th>Quantidade</th>
                    <th>Var</th>
                    <th>Repasse</th>
                    <th class="w-100px min-w-100px rounded-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php for ($i = 0; $i < count($data); $i++) {
                    $total += $data[$i]["OrderItem"]["subtotal_not_formated"];
                ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["created"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $statuses[$data[$i]["Order"]["status_id"]]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["BenefitType"]["name"]) && !empty($data[$i]["BenefitType"]["name"]) ? $data[$i]["BenefitType"]["name"]: 'Não especificado';?>
                        </td>
                       <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["first_order"] == 0 ? "Não" : "Sim"; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["working_days"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Supplier']["nome_fantasia"]; ?></td>
                        <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["CustomerUserItinerary"]["unit_price"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["manual_quantity"] != 0 ? $data[$i]["OrderItem"]["manual_quantity"] : $data[$i]["CustomerUserItinerary"]["quantity"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["var"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderItem"]["transfer_fee"]; ?></td>
                        <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["OrderItem"]["total"]; ?></td>

                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="12"></td>
                    <td>Total</td>
                    <td>R$<?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
            </table>
        </div>
        <?php echo $data ? $this->element("pagination") : '' ?>
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
