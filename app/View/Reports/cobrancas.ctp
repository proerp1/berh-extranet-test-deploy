<?php $url_novo = $this->base."/incomes/add/"; ?>
<?php if (CakeSession::read('Auth.User.group_id') == 1) { ?>
    <div class="row gy-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-lg-3 col-sm-6">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary h-75px w-75px">
                        <i class="fas fa-list fa-3x text-white"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($total_cobrancas, 2, ",", '.') ?></span>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Total de Cobranças</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                        <i class="fas fa-list fa-3x text-white"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo $total_clientes ?></span>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Total de Clientes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                        <i class="fas fa-list fa-3x text-white"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($total_pago[0][0]["valor_pago"], 2, ",", '.') ?></span>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Total Pago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card h-lg-100">
                <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success h-75px w-75px">
                        <i class="fas fa-list fa-3x text-white"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_total[0][0]["valor_total"], 2, ",", '.') ?></span>
                        <div class="m-0">
                            <span class="fw-bold fs-6 text-gray-400">Valor total R$</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "cobrancas")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
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
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Cliente</th>
                        <th>Descrição</th>
                        <th>Conta bancária</th>
                        <th>Vencimento</th>
                        <th>Parcela</th>
                        <th>Valor a receber R$</th>
                        <th class="w-250px min-w-250px rounded-end">Valor com multa e juros</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo !empty($data[$i]["Income"]["BankAccount"]) ? $data[$i]["Income"]["BankAccount"]["name"] : ''; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["parcela"].'ª'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]["Income"]["valor_total_nao_formatado"] + $juros_multa[$data[$i]["Income"]["id"]]['juros_multa'],2,',','.'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="7">Nenhum registro encontrado</td>
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
            $("#q").val(null);
            $("#t").val(null);
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>