<?php $url_novo = $this->base."/outcomes/add/"; ?>
<?php 
	echo $this->element("abas_contas_pagar"); 
	$total_outcome[0] = 0;
?>

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
                            } ?> rounded h-8px" role="progressbar" style="width: <?php echo ($aba_atual_id == 11 || $aba_atual_id == 12 || $aba_atual_id == 14) ? '100%' : ($valor_restante / $total_outcome[0]["total_outcome"]) * 100 ?>%;" aria-valuenow="<?php echo ($aba_atual_id == 11 || $aba_atual_id == 12 || $aba_atual_id == 14) ? 100 : ($valor_restante / $total_outcome[0]["total_outcome"]) * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                            <div class="bg-success rounded h-8px" role="progressbar" style="width: <?php echo ($pago_outcome[0]["pago_outcome"] / $total_outcome[0]["total_outcome"]) * 100 ?>%;" aria-valuenow="<?php echo ($pago_outcome[0]["pago_outcome"] / $total_outcome[0]["total_outcome"]) * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                        <th>N° Documento</th>
                        <th>Fornecedor </th>
                        <th class="ps-4 w-150px min-w-150px rounded-start">Descrição</th>
                        <th>Status</th>
						<th>Conta bancária</th>
						<th>Vencimento</th>
                        <th>Data de criação</th>
						<th>Parcela</th>
						<th>Valor a pagar R$</th>
						<th>Data pagamento</th>
						<th>Valor pago R$</th>
                        <th>Observação </th>
						<th class="w-300px min-w-300px rounded-end">Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php 
                            $valor_a_pagar = 0;
                            $valor_pago = 0;
                            //debug($data);die();
                            for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $valor_a_pagar += $data[$i]["Outcome"]["valor_total_not_formated"];
                                $valor_pago += (isset($data[$i]["Outcome"]["valor_pago_not_formated"]) ? $data[$i]["Outcome"]["valor_pago_not_formated"] : 0);
                                
                               
                            ?>
							<tr>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["doc_num"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["supplier_id"]; ?></td>

								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["name"]; ?></td>
								<td class="fw-bold fs-7 ps-4">
									<span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
										<?php echo $data[$i]["Status"]["name"] ?>
									</span>
								</td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["BankAccount"]["name"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["created"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["parcela"].'ª'; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_total"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["data_pagamento"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Outcome"]["observation"]; ?></td>

								<td class="fw-bold fs-7 ps-4">
									<a href="<?php echo $this->base.'/outcomes/edit/'.$data[$i]["Outcome"]["id"]; ?>" class="btn btn-info btn-sm">
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
							<td colspan="9" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
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

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>
