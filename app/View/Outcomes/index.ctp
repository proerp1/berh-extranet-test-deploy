<?php $url_novo = $this->base."/outcomes/add/"; ?>
<?php echo $this->element("abas_contas_pagar"); ?>

<div class="row gy-5 g-xl-10">
    <?php if ($aba_atual_id != $aba_pago_id): ?>
        <!-- Primeiro card -->
        <div class="col-lg-4 col-sm-6 mb-xl-10">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <div class="m-0">
                        <i class="fas fa-dollar-sign fa-3x <?php 
                            if ($aba_atual_id == 11) { 
                                echo 'text-warning'; 
                            } elseif ($aba_atual_id == 12) { 
                                echo 'text-primary'; 
                            } elseif ($aba_atual_id == 14) { 
                                echo 'text-dark'; 
                            } else { 
                                echo 'text-danger'; 
                            } ?>"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <?php if (isset($total_outcome[0]["total_outcome"]) && isset($pago_outcome[0]["pago_outcome"])): ?>
                            <?php $valor_restante = $total_outcome[0]["total_outcome"] - $pago_outcome[0]["pago_outcome"]; ?>
                            <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($valor_restante, 2, ",", '.') ?></span>
                        <?php else: ?>
                            <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($total_outcome[0]["total_outcome"], 2, ",", '.') ?></span>
                        <?php endif; ?>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">
                                <?php if ($aba_atual_id == 11): ?>
                                    Valor programado
                                <?php elseif ($aba_atual_id == 12): ?>
                                    Valor aprovado
                                <?php elseif ($aba_atual_id == 14): ?>
                                    Valor cancelado
                                <?php else: ?>
                                    Valor restante a pagar
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="h-8px mx-3 w-100 bg-light-info rounded">
                            <div class="<?php 
                                if ($aba_atual_id == 11) { 
                                    echo 'bg-warning'; 
                                } elseif ($aba_atual_id == 12) { 
                                    echo 'bg-primary'; 
                                } elseif ($aba_atual_id == 14) { 
                                    echo 'bg-dark'; 
                                } else { 
                                    echo 'bg-danger'; 
                                } ?> rounded h-8px" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="col-lg-4 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-success"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <?php if(isset($pago_outcome[0]["pago_outcome"])): ?>
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($pago_outcome[0]["pago_outcome"], 2, ",", '.') ?></span>
                    <?php else: ?>
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ 0.00</span>
                    <?php endif; ?>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor pago</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-info rounded">
                        <?php if(isset($total_outcome[0]["total_outcome"]) && isset($pago_outcome[0]["pago_outcome"])): ?>
                            <div class="bg-success rounded h-8px" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card mb-5 mb-xl-8">
	<form action="<?php echo $this->Html->url(array( "controller" => "outcomes", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
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

                    <?php if (isset($_GET["t"]) && ($_GET["t"] == 11 || $_GET["t"] == 12 || $_GET["t"] == 13 || $_GET["t"] == 103)) { ?>
                        <a href="#" id="download_sel" class="btn btn-secondary me-3">
                            Download em Lote
                        </a>
                    <?php } ?>

                    <?php if (isset($_GET["t"]) && ($_GET["t"] == 11 || $_GET["t"] == 12)) { ?>
                        <a href="#" id="pendente_sel" class="btn btn-secondary me-3">
                            Pendente em Lote 
                        </a>
                    <?php } ?>

                    <?php if (isset($_GET["t"]) && $_GET["t"] == 11) { ?>
                        <a href="#" id="aprovar_sel" class="btn btn-secondary me-3">
                            Aprovar em Lote
                        </a>
                    <?php } ?>

                    <?php if (isset($_GET["t"]) && ($_GET["t"] == 12 || $_GET["t"] == 103)) { ?>
                        <a href="#" id="conta_paga_sel" class="btn btn-secondary me-3">
                            Pagar em Lote
                        </a>
                    <?php } ?>

                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->here.'?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'&exportar' ;?>" class="btn btn-light-primary me-3">
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
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-group input-daterange" id="datepicker">
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
                                <label class="form-label fs-5 fw-bold mb-3">Data pagamento:</label>
                                <div class="input-daterange input-group" id="datepicker">
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

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
        	<?php echo $this->element("table"); ?>
				<thead>
					<tr class="fw-bolder text-muted bg-light">
                        <?php if (isset($_GET["t"]) && in_array($_GET["t"], [11, 12, 13, 103])) { ?>
                            <th class="ps-4 w-80px min-w-80px rounded-start">
                                <input type="checkbox" class="check_all">
                            </th>
                        <?php } ?>
			            <th <?php echo (!isset($_GET["t"]) || $_GET["t"] != 11 && $_GET["t"] != 12) ? 'class="ps-4 w-80px min-w-80px rounded-start"' : '' ?>>ID</th>
                        <th>N° Documento</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Fornecedor</th>
                        <th>Nome </th>
                        <th>Descrição</th>
                        <th>Status</th>
			            <th>Vencimento</th>
                        <th>Data de criação</th>
						<th>Parcela</th>
                        <th data-priority="1"><?php echo $this->Paginator->sort('Outcome.valor_total', 'Valor a pagar R$'); ?> <?php echo $this->Paginator->sortKey() == 'Outcome.valor_total' ? "<i class='fas fa-sort-".($this->Paginator->sortDir() == 'asc' ? 'up' : 'down')."'></i>" : ''; ?></th>
						<th>Data pagamento</th>
						<th>Valor pago R$</th>
                        <th>Observação</th>
						<th class="w-300px min-w-300px rounded-end">Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php 
                        $valor_a_pagar = 0;
                        $valor_pago = 0;
                    ?>

                    <?php if ($data) { ?>
						<?php 
                            for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $valor_a_pagar += $data[$i]["Outcome"]["valor_total_not_formated"];
                                $valor_pago += (isset($data[$i]["Outcome"]["valor_pago_not_formated"]) ? $data[$i]["Outcome"]["valor_pago_not_formated"] : 0);
                                
                               
                            ?>
							<tr>
                                <?php if (isset($_GET["t"]) && ($_GET["t"] == 11 || $_GET["t"] == 12 || $_GET["t"] == 13 || $_GET["t"] == 103)) { ?>
                                    <td class="fw-bold fs-7 ps-4">
                                        <input type="checkbox" name="item_ck" class="check_individual" data-id="<?php echo $data[$i]["Outcome"]["id"]; ?>">
                                    </td>
                                <?php } ?>
				                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["doc_num"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["order_id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["supplier_id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["nome_fantasia"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["name"]; ?></td>
								<td class="fw-bold fs-7 ps-4">
									<span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
										<?php echo $data[$i]["Status"]["name"] ?>
									</span>
								</td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["created"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["parcela"].'ª'; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_total"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["data_pagamento"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["observation"]; ?></td>

								<td class="fw-bold fs-7 ps-4">

                                    <span class='badge badge-success'><i class="fas fa-info" style="color:#fff" title="<?php echo $data[$i]["Outcome"]["observation"]; ?>"></i></span>

                                        
									<a href="<?php echo $this->Html->url(['controller' => 'outcomes', 'action' => 'edit', $data[$i]["Outcome"]["id"], '?' => $_SERVER['QUERY_STRING']]); ?>" class="btn btn-info btn-sm">
										Editar
									</a>
                                    <?php if($data[$i]["Status"]["id"]!= 13){?>
									<a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/outcomes/delete/'.$data[$i]["Outcome"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
										Excluir
									</a>
                                    <?php } ?>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="20" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="fw-bold fs-5 ps-4">Total</th>
                        <th class="fw-bold fs-6 ps-4"><?php echo number_format($valor_a_pagar, 2, ',', '.'); ?></th>

                        <th class="fw-bold fs-5 ps-4"></th>
                        <th class="fw-bold fs-6 ps-4"><?php echo number_format($valor_pago, 2, ',', '.'); ?></th>
                    </tr>
                </tfoot>
			</table>
        </div>

        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_aprovar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Aprovar items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="aprova_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_pendente_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tornar Pendente items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="pendente_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalContaPaga" tabindex="-1" role="dialog" aria-labelledby="labelModalContaPaga">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelModalContaPaga">Pagar conta</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo $this->Form->create('Outcome', array("id" => "js-form-submit", "class" => "form-horizontal", "action" => '../outcomes/pagar_titulo_lote/', "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
                <input type="hidden" name="data[Outcome][ids]" id="ids_outcome">
                <input type="hidden" name="data[Outcome][status_id]" value="13">
                <div class="modal-body">
                    <div class="mb-7">
                        <label class="form-label">Data de Pagamento</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <?php echo $this->Form->input('data_pagamento', ["type" => "text", "required" => true, "placeholder" => "Data de Pagamento", "class" => "form-control datepicker mb-3 mb-lg-0"]);  ?>
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                        <?php echo $this->Form->input('payment_method_baixa', array("required" => true, "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione", 'options' => ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito',  '7' => 'Débito em conta',  '8' => 'Dinheiro', '2' => 'Transfêrencia']));  ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $(".datepicker").datepicker({
            format: 'dd/mm/yyyy',
            weekStart: 1,
            orientation: "bottom auto",
            autoclose: true,
            language: "pt-BR",
            todayHighlight: true,
            toggleActive: true,
            endDate: new Date()
        });

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $('#aprovar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_aprovar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a aprovar');
            }
        });

        $('#pendente_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_pendente_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a tornar pendente');
            }
        });

        $('#download_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                const status_id = $('#t').val();
                const checkboxes = $('input[name="item_ck"]:checked');
                const outcomeIds = [];

                checkboxes.each(function() {
                    outcomeIds.push($(this).data('id'));
                });

                if (outcomeIds.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: base_url+'/outcomes/download_zip_document',
                        data: {
                            outcomeIds,
                            status: status_id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                window.location.href = base_url+'/files/docoutcome/file/'+response.nome_zip;
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

        $('#conta_paga_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modalContaPaga').modal('show');

                const checkboxes = $('input[name="item_ck"]:checked');
                const outcomeIds = [];

                checkboxes.each(function() {
                    outcomeIds.push($(this).data('id'));
                });

                $("#ids_outcome").val(outcomeIds);
            } else {
                alert('Selecione ao menos um item a pagar');
            }
        });

        $('#aprova_confirm').on('click', function(e) {
            e.preventDefault();

            const checkboxes = $('input[name="item_ck"]:checked');
            const outcomeIds = [];

            checkboxes.each(function() {
                outcomeIds.push($(this).data('id'));
            });

            if (outcomeIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/outcomes/change_status_lote',
                    data: {
                        outcomeIds,
                        status: 12
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $('#pendente_confirm').on('click', function(e) {
            e.preventDefault();

            const checkboxes = $('input[name="item_ck"]:checked');
            const outcomeIds = [];

            checkboxes.each(function() {
                outcomeIds.push($(this).data('id'));
            });

            if (outcomeIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/outcomes/change_status_lote',
                    data: {
                        outcomeIds,
                        status: 103
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });
    })
</script>
