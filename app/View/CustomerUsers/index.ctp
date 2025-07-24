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
                    <!-- <a href="#" class="btn btn-secondary me-3" data-bs-toggle="modal" data-bs-target="#modal_grupo_economico">
                        <i class="fas fa-arrow-up"></i>
                        Atualizar Grupo Econômico
                    </a>
                    <a href="#" class="btn btn-secondary me-3" data-bs-toggle="modal" data-bs-target="#modal_dias_uteis">
                        <i class="fas fa-arrow-up"></i>
                        Atualizar Dias Úteis
                    </a>

                    <a href="#" class="btn btn-secondary me-3" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans">
                        <i class="fas fa-arrow-up"></i>
                        Importar
                    </a>

                    <a href="#" class="btn btn-secondary me-3" data-bs-toggle="modal" data-bs-target="#modal_ativar_inativar">
                        <i class="fas fa-arrow-up"></i>
                        Ativar/Inativar (csv)
                    </a>

                    <a href="#" id="excluir_sel" class="btn btn-secondary me-3">
                        <i class="fas fa-thumbs-down"></i>
                        Excluir em Lote
                    </a>

                    <a href="#" onclick="confirm('Você realmente deseja excluir todos os registros da lista?', '<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "exclui_todos", $id)); ?>');" class="btn btn-danger me-3">
                        <i class="fas fa-trash"></i>
                        Excluir Todos
                    </a> -->

                    <a href="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "generate_excel_report", $id)); ?>" class="btn btn-sm btn-primary me-3 d-flex align-items-center justify-content-center text-center">
                        <i class="fas fa-download me-2"></i>
                        Relatório de Benefícios
                    </a>

                    <div class="dropdown me-3">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Ações
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="dropdownMenuButton" style="width: 300px;">
                            <div class="d-flex flex-column justify-content-start gap-3">
                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal_grupo_economico">
                                    <i class="fas fa-arrow-up"></i>
                                    Atualizar Grupo Econômico
                                </button>

                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal_dias_uteis">
                                    <i class="fas fa-arrow-up"></i>
                                    Atualizar Dias Úteis
                                </button>

                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal_enviar_sptrans">
                                    <i class="fas fa-arrow-up"></i>
                                    Importar
                                </button>

                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal_ativar_inativar">
                                    <i class="fas fa-arrow-up"></i>
                                    Ativar/Inativar (csv)
                                </button>

                                <button type="button" id="excluir_sel" class="btn btn-secondary">
                                    <i class="fas fa-thumbs-down"></i>
                                    Excluir em Lote
                                </button>

                                <a href="javascript:;" onclick="confirm('Você realmente deseja excluir todos os registros da lista?', '<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "exclui_todos", $id)); ?>');" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Excluir Todos
                                </a>
                            </div>
                        </div>
                    </div>

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
                    <th class="ps-4 w-80px min-w-80px rounded-start">
                        <input type="checkbox" class="check_all">
                    </th>
                    <th>Status</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Departamento</th>
                    <th>Centro de Custo</th>
                    <th>Grupo Economico</th>
                    <th>Endereço de Entrega</th>
                    <th>Usuário Alterado</th>
                    <th>Observação</th>


                    <th class="w-200px min-w-200px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $urlEdit = $is_admin ? 'edit_user' : 'edit'; ?>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <input type="checkbox" name="item_ck" class="check_individual" id="">
                            </td>
                            <td>
                                <input type="hidden" class="item_id" value="<?php echo $data[$i]["CustomerUser"]["id"]; ?>">
                                <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                    <?php echo $data[$i]["Status"]["name"] ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["email"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["cpf"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerDepartment"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CostCenter"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo !empty($data[$i]["EconomicGroup"][0]["name"]) ? $data[$i]["EconomicGroup"][0]["name"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo !empty($data[$i]["CustomerAddress"]["address"]) ? $data[$i]["CustomerAddress"]["address"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["UserUpdated"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["observation"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->base . '/customer_users/' . $urlEdit . '/' . $id . '/' . $data[$i]["CustomerUser"]["id"] . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-info btn-sm">
                                    Editar
                                </a>

                            <?php if (!empty($user['Group']['id']) && $user['Group']['id'] == 1): ?>
                                
                                <a href="javascript:" 
                                    onclick="verConfirm('<?php echo $this->base . '/customer_users/delete_user/' . $data[$i]["CustomerUser"]["customer_id"] . '/' . $data[$i]["CustomerUser"]["id"]; ?>');" 
                                    rel="tooltip" 
                                    title="Excluir" 
                                    class="btn btn-danger btn-sm">
                                    Excluir
                                </a>



                            <?php endif; ?>


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
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base . '/customer_users/baixar_beneficiarios/'.$id.'/'.$is_admin; ?>" targe="_blank" download>Baixar Modelo (.csv)</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_grupo_economico" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/customer_users/update_grupo_economico/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Atualizar Grupo Econômico dos Beneficiários</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloAtualizacaoGrupos.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_dias_uteis" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/customer_users/update_working_days/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Atualizar Dias Úteis dos Beneficiários</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloAtualizacaoDiasUteis.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_ativar_inativar" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/customer_users/update_ativar_inativar/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $id; ?>">
                <div class="modal-body">
                    <p>Atualizar Status dos Beneficiários</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloAtivarInativarBeneficiariosLote.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_ativar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Ativar items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="ativa_confirm" class="btn btn-success">Sim</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_excluir_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Excluir items selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="exclui_confirm" class="btn btn-success">Sim</a>
            </div>
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

        $('#ativar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_ativar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a ativar');
            }
        });

        $('#excluir_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_excluir_sel').modal('show');
            } else {
                alert('Selecione ao menos um item a inativar');
            }
        });

        $('#ativa_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const custUserIds = [];

            checkboxes.each(function() {
                custUserIds.push($(this).parent().parent().find('.item_id').val());
            });

            if (custUserIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/customer_users/ativa_customer_user',
                    data: {
                        custUserIds,
                        customerId
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

        $('#exclui_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const custUserIds = [];

            checkboxes.each(function() {
                custUserIds.push($(this).parent().parent().find('.item_id').val());
            });

            if (custUserIds.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: base_url+'/customer_users/exclui_customer_user',
                    data: {
                        custUserIds,
                        customerId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = base_url+'/customer_users/index/'+customerId
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
    });
</script>
