<?php echo $this->element("abas_contas_pagar"); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "outcomes", "action" => "all_documents")); ?>" role="form" id="busca" autocomplete="off">
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
                    <a href="#" id="download_sel" class="btn btn-secondary me-3">
                        Download em Lote
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
                            
                            <!-- Campo para Vencimento -->
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Vencimento:</label>
                                <div class="d-flex">
                                    <input type="date" class="form-control form-control-solid fw-bolder me-2" name="vencimento_de" id="vencimento_de" placeholder="De" value="<?php echo isset($_GET['vencimento_de']) ? $_GET['vencimento_de'] : ''; ?>">
                                    <input type="date" class="form-control form-control-solid fw-bolder" name="vencimento_ate" id="vencimento_ate" placeholder="Até" value="<?php echo isset($_GET['vencimento_ate']) ? $_GET['vencimento_ate'] : ''; ?>">
                                </div>
                            </div>

                            <!-- Campo para Data de Pagamento -->
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data de Pagamento:</label>
                                <div class="d-flex">
                                    <input type="date" class="form-control form-control-solid fw-bolder me-2" name="data_pagamento_de" id="data_pagamento_de" placeholder="De" value="<?php echo isset($_GET['data_pagamento_de']) ? $_GET['data_pagamento_de'] : ''; ?>">
                                    <input type="date" class="form-control form-control-solid fw-bolder" name="data_pagamento_ate" id="data_pagamento_ate" placeholder="Até" value="<?php echo isset($_GET['data_pagamento_ate']) ? $_GET['data_pagamento_ate'] : ''; ?>">
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
                        <th class="ps-4 w-80px min-w-80px rounded-start">
                            <input type="checkbox" class="check_all">
                        </th>
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status do documento</th>
                        <th>Id da conta</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>N° Documento</th>
                        <th>Fornecedor</th>
                        <th>Nome Fornecedor</th>
                        <th>Descrição</th>
                        <th>Status conta</th>
                        <th>Vencimento</th>
                        <th>Valor a Pagar</th>
                        <th>Data do Pagamento</th>
                        <th>Valor Pago</th>
                        <th>Nome</th>
                        <th>Tipo de Documento</th>
                        <th>Documento</th>
                        <th>Data</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" name="item_ck" class="check_individual" data-id="<?php echo $data[$i]["Docoutcome"]["id"]; ?>">
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["order_id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["doc_num"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["supplier_id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["OutcomeStatus"]["label"] ?>'>
                                        <?php echo $data[$i]["OutcomeStatus"]["name"] ?>
                                    </span>
                                </td>

                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["vencimento"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_total"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["data_pagamento"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Docoutcome"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["TipoDocumento"]["nome"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><a href="<?php echo $this->base.'/files/docoutcome/file/'.$data[$i]["Docoutcome"]["id"].'/'.$data[$i]["Docoutcome"]["file"] ?>"><?php echo $data[$i]["Docoutcome"]["file"] ?></a></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['Docoutcome']['created'])) ?></td>

                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/outcomes/edit_document/'.$data[$i]["Outcome"]["id"].'/'.$data[$i]["Docoutcome"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });


        $('#download_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                const checkboxes = $('input[name="item_ck"]:checked');
                const docOutcomeIds = [];

                checkboxes.each(function() {
                    docOutcomeIds.push($(this).data('id'));
                });

                if (docOutcomeIds.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: base_url+'/outcomes/download_zip_document_id',
                        data: {
                            docOutcomeIds
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.url_zip;
                            } else {
                                alert('Nenhum arquivo encontrado');
                            }
                        }
                    });
                }
            } else {
                alert('Selecione ao menos um item para fazer download');
            }
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });      
    });
</script>