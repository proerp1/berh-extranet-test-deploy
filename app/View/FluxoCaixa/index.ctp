<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "fluxo_caixa", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
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

                    <?php if ($exportar): ?>
                        <a href="<?php echo $this->base.'/fluxo_caixa/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                            <i class="fas fa-file-excel"></i>
                            Exportar
                        </a>
                    <?php endif ?>
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Conta bancária:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($conta_bancaria); $a++){
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($conta_bancaria[$a]['BankAccount']['id'] == $_GET["t"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$conta_bancaria[$a]['BankAccount']['id'].'" '.$selected.'>'.$conta_bancaria[$a]['BankAccount']['name'].'</option>';
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
						<th>Conta bancária</th>
                        <th>Código/Id</th>
                        <th>Cliente</th>
                        <th>Fornecedor</th>
                        <th>N° Pedido</th>
						<th>Data</th>
						<th>Nome da conta</th>
						<th>Cadastro</th>
						<th>Valor</th>
						<th class="w-150px min-w-150px rounded-end">Saldo</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($conta)): ?>
						<tr>
							<td colspan="6" class="fw-bold fs-7 ps-4"><?php echo $conta['BankAccount']['name'] ?></td>
							<td class="fw-bold fs-7 ps-4"><?php echo number_format($saldo,2,',','.') ?></td>
						</tr>
					<?php endif ?>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<?php
								$color =  ($data[$i][0]['operador'] == '+' ? '#000' : '#f00'); 
								$saldo = ($data[$i][0]['operador'] == '+' ? $saldo + $data[$i][0]['valor_total'] : $saldo - $data[$i][0]['valor_total']);
							?>
							<tr>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['status'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['codigo'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['customer_nome_secundario'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['supplier_nome_fantasia'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['order_id'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i][0]['data_pagamento'])) ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['nome_conta'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i][0]['nome'] ?></td>
								<td class="fw-bold fs-7 ps-4"><span style="color: <?php echo $color; ?>"><?php echo $data[$i][0]['operador'].' '.number_format($data[$i][0]['valor_total'],2,',','.') ?></span></td>
								<td class="fw-bold fs-7 ps-4"><?php echo number_format($saldo,2,',','.') ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="8" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<th colspan="6" class="fw-bold fs-7 ps-4">Total:</th>
					<th class="fw-bold fs-7 ps-4"><?php echo number_format($saldo,2,',','.') ?></th>
				</tfoot>
			</table>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>