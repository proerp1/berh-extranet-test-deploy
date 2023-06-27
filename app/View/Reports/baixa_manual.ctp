<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "baixa_manual")); ?>/" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->here.'?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
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
						<th class="ps-4 w-150px min-w-150px rounded-start">Código associado</th>
						<th>Cliente</th>
						<th>Mensalidade</th>
						<th>Vencimento</th>
						<th>Data de pagamento</th>
						<th>Valor total</th>
						<th>Valor pago</th>
						<th>Data baixa</th>
						<th class="w-150px min-w-150px rounded-end">Usuário baixa</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['c']['codigo_associado'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['c']['nome_secundario'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['i']['mensalidade'] ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]['i']['vencimento'])) ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]['i']['data_pagamento'])) ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]['i']['valor_total'],2,',','.') ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]['i']['valor_pago'],2,',','.') ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]['i']['data_baixa'])) ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['u']['usuarioBaixa'] ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="9" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
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