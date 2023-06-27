<div class="row gy-5 g-xl-10">
    <div class="col-lg-3 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-info"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($valor_total[0][0]['total'], 2, ",", '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor total de negativados</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-info rounded">
                        <div class="bg-info rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
            	<div class="badge badge-danger w-100 mb-2" style="display: block;">Incluídos</div>
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-danger"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($valor_total_inclusao[0][0]['total'], 2, ",", '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor total de negativações ativas</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                        <div class="bg-danger rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <span class="badge badge-danger mt-3">Títulos a resgatar</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
            	<div class="badge badge-success w-100 mb-2" style="display: block;">Baixados</div>
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-success"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($valor_total_baixado[0][0]['total'], 2, ",", '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor total de negativações baixadas</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-success rounded">
                        <div class="bg-success rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <span class="badge badge-success mt-3">Títulos resgatados</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-xl-10">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <i class="fas fa-dollar-sign fa-3x text-warning"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2">R$ <?php echo number_format($valor_total_decurs[0][0]['total'], 2, ",", '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor total de negativações baixadas</span>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="h-8px mx-3 w-100 bg-light-warning rounded">
                        <div class="bg-warning rounded h-8px" role="progressbar" style="width: 55%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <span class="badge badge-warning mt-3">por decurso prazo</span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
	<form action="<?php echo $this->Html->url(array( "controller" => "negativacao", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
		<div class="card-header border-0 pt-6 mb-3">
			<div class="card-title">
				<div class="row">
					<div class="col-md-12 d-flex align-items-center my-1">
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

                    <a href="<?php echo $this->here."/?excel&".(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');?>" class="btn btn-light-primary me-3">
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
								<label class="form-label fs-5 fw-bold mb-3">Cliente:</label>
								<select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
									<option></option>
									<?php
										foreach ($clientes as $cliente_id => $cliente_nome) {
											$selected = "";
											if (isset($_GET["c"])) {
												if($cliente_id == $_GET["c"]){
													$selected = "selected";
												}
											}
											echo '<option value="'.$cliente_id.'" '.$selected.'>'.$cliente_nome.'</option>';
										}
									?>
								</select>
							</div>
							<div class="mb-10">
								<label class="form-label fs-5 fw-bold mb-3">Data:</label>
								<div class="input-daterange input-group" id="datepicker">
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
			<?php echo $this->element("Negativacao/negativacoes_table"); ?>
		</div>
		<?php echo $this->element("pagination"); ?>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#c").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
	})
</script>