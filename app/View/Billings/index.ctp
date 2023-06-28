<?php $url_novo = $this->base."/billings/add/";  ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "billings", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
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
                                <label class="form-label fs-5 fw-bold mb-3">Mês:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="text" class="form-control datepickerMes" name="data" id="mes" value="<?php echo isset($_GET["data"]) ? $_GET["data"] : ""; ?>">
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
                        <th>Data</th>
                        <th>Mensalidades</th>
                        <th>Manutenção</th>
                        <th>Total</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8'); ?>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $valor_total = 0;
                                $mensalidade = 0;
                                $total_manutencao = 0;
                                $total_desconto = 0;
                                foreach ($val_mensalidade as $value) {
                                    if ($value['BillingMonthlyPayment']['billing_id'] == $data[$i]["Billing"]["id"]) {
                                        $mensalidade = $value[0]['valor_total'];
                                    }
                                }

                                foreach ($manutencao as $value) {
                                    if ($value['BillingMonthlyPayment']['billing_id'] == $data[$i]["Billing"]["id"]) {
                                        $total_manutencao = $value[0]['valor_total'];
                                    }
                                }

                                foreach ($valor_desconto as $value) {
                                    if ($value['BillingMonthlyPayment']['billing_id'] == $data[$i]["Billing"]["id"]) {
                                        $total_desconto = $value[0]['valor_total'];
                                    }
                                }

                                $valor_total = ($mensalidade+$total_manutencao)-$total_desconto;
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Billing"]["date_billing_index"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($mensalidade,2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($total_manutencao,2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_total,2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/billings/mensalidade/'.$data[$i]["Billing"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/billings/delete/'.$data[$i]["Billing"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
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
            $("#mes").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>