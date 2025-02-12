<?php $url_novo = $this->base."/benefits/add/"; ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "benefits", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
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

                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="fas fa-filter"></i>
                    Filtro
                </button>

                <a href="<?php echo $this->base.'/benefits/index/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                    <i class="fas fa-file-excel"></i>
                    Exportar
                </a>
                
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Novo</a>
                </div>

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
                            <label class="form-label fs-5 fw-bold mb-3">Tipo:</label>
                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="o" id="o">
                                <option></option>
                                <?php
                                    foreach ($benefitTypes as $type) {
                                        $selected = (isset($_GET["o"]) && $_GET["o"] == $type['BenefitType']['name']) ? "selected" : "";
                                        echo '<option value="'.$type['BenefitType']['name'].'" '.$selected.'>'.$type['BenefitType']['name'].'</option>';
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
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        
                        <th>ID</th>
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th class="ps-4">Código</th>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Prazo Recarga</th>
                        <th>Prazo Cartão Novo</th>
                        <th>Fornecedor</th>
                        <th>CNPJ</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["id"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["code"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["BenefitType"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["name"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["unit_price"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["time_to_recharge"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["time_card"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["documento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/benefits/edit/'.$data[$i]["Benefit"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/benefits/delete/'.$data[$i]["Benefit"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="8">Nenhum registro encontrado</td>
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
    });
</script>
