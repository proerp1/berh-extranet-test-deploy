<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "custo_serasa", "action" => "cliente")); ?>" role="form" id="busca" autocomplete="off">
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
                                <label class="form-label fs-5 fw-bold mb-3">Faturamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($faturamentos); $a++){
											$selected = "";
											if (isset($_GET["t"])) {
												if($faturamentos[$a]['Billing']['id'] == $_GET["t"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$faturamentos[$a]['Billing']['id'].'" '.$selected.'>'.$faturamentos[$a]['Billing']['date_billing_index'].'</option>';
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
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
		<p>Total: <?php echo count($data) ?></p>
        <div class="table-responsive">
        	<?php echo $this->element("table"); ?>
				<thead>
					<tr class="fw-bolder text-muted bg-light">
						<th class="ps-4 rounded-start">Nome</th>
						<th class="rounded-end">Valor</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($data) { ?>
						<?php for ($i=0; $i < count($data); $i++) { ?>
							<tr>
								<td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
								<td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i][0]["total"],2,',','.'); ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="2" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
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
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>