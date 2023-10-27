<?php $url_novo = $is_admin ? $this->base . "/customer_users/add_user/" . $id : $this->base . "/customer_users/add/" . $id;  ?>
<?php
echo $this->element("abas_customers", array('id' => $id));
?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "index", $id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <a href="#" class="btn btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans">
                        <i class="fas fa-arrow-up"></i>
                        Importar
                    </a>

                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo; ?>">Novo</a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>

                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option value=''>Selecione</option>
                                    <?php
                                    for ($a = 0; $a < count($status); $a++) {
                                        $selected = "";
                                        if (isset($_GET["t"])) {
                                            if ($status[$a]['Status']['id'] == $_GET["t"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $status[$a]['Status']['id'] . '" ' . $selected . '>' . $status[$a]['Status']['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Centro de Custo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="cc" id="cc">
                                    <option value=''>Selecione</option>
                                    <?php
                                    for ($a = 0; $a < count($cost_centers); $a++) {
                                        $selected = "";
                                        if (isset($_GET["cc"])) {
                                            if ($cost_centers[$a]['CostCenter']['id'] == $_GET["cc"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $cost_centers[$a]['CostCenter']['id'] . '" ' . $selected . '>' . $cost_centers[$a]['CostCenter']['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Departamento:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="d" id="d">
                                    <option value=''>Selecione</option>
                                    <?php
                                    for ($a = 0; $a < count($departments); $a++) {
                                        $selected = "";
                                        if (isset($_GET["d"])) {
                                            if ($departments[$a]['CustomerDepartment']['id'] == $_GET["d"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $departments[$a]['CustomerDepartment']['id'] . '" ' . $selected . '>' . $departments[$a]['CustomerDepartment']['name'] . '</option>';
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Departamento</th>
                    <th>Centro de Custo</th>
                    <th class="w-200px min-w-200px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $urlEdit = $is_admin ? 'edit_user' : 'edit'; ?>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                    <?php echo $data[$i]["Status"]["name"] ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["email"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerDepartment"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CostCenter"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->base . '/customer_users/' . $urlEdit . '/' . $id . '/' . $data[$i]["CustomerUser"]["id"] . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-info btn-sm">
                                    Editar
                                </a>

                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_enviar_sptrans" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/customer_users/upload_csv/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Importar Beneficiários e Itinerários</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                    <div class="mt-10">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="option_itinerary" value="2" id="flexRadioChecked1" checked="checked" />
                            <label class="form-check-label" for="flexRadioChecked1">
                                Manter Itinerários Existentes
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="radio" name="option_itinerary" value="1" id="flexRadioChecked2" />
                            <label class="form-check-label" for="flexRadioChecked2">
                                Inativar Itinerários Existentes
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloImportacaoBeneficiarios.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });
    });
</script>
