<?php
echo $this->element("abas_customers", array('id' => $id));
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "view_all_benefits", $id)); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar por Nome, CPF ou E-mail" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

                    <div class="dropdown me-3">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Ações em Lote
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="dropdownMenuButton" style="width: 300px;">
                            <div class="d-flex flex-column justify-content-start gap-3">
                                <button type="button" id="ativar_sel" class="btn btn-success">
                                    <i class="fas fa-check"></i>
                                    Ativar Selecionados
                                </button>

                                <button type="button" id="inativar_sel" class="btn btn-warning">
                                    <i class="fas fa-ban"></i>
                                    Inativar Selecionados
                                </button>

                                <button type="button" id="excluir_sel" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Excluir Selecionados
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtros
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções de Filtro</div>
                        </div>
                        <div class="separator border-gray-200"></div>

                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Código do Benefício:</label>
                                <input type="text" class="form-control form-control-solid" name="benefit_code" id="benefit_code" value="<?php echo isset($_GET['benefit_code']) ? $_GET['benefit_code'] : ''; ?>" placeholder="Código">
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Nome do Benefício:</label>
                                <input type="text" class="form-control form-control-solid" name="benefit_name" id="benefit_name" value="<?php echo isset($_GET['benefit_name']) ? $_GET['benefit_name'] : ''; ?>" placeholder="Nome">
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status do Vínculo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="status_link" id="status_link">
                                    <option value=''>Todos</option>
                                    <?php
                                    for ($a = 0; $a < count($status); $a++) {
                                        $selected = "";
                                        if (isset($_GET["status_link"])) {
                                            if ($status[$a]['Status']['id'] == $_GET["status_link"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $status[$a]['Status']['id'] . '" ' . $selected . '>' . $status[$a]['Status']['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status do Benefício:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="status_benefit" id="status_benefit">
                                    <option value=''>Todos</option>
                                    <?php
                                    for ($a = 0; $a < count($status); $a++) {
                                        $selected = "";
                                        if (isset($_GET["status_benefit"])) {
                                            if ($status[$a]['Status']['id'] == $_GET["status_benefit"]) {
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="' . $status[$a]['Status']['id'] . '" ' . $selected . '>' . $status[$a]['Status']['name'] . '</option>';
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
                    <th>Beneficiário</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Código Benefício</th>
                    <th>Nome Benefício</th>
                    <th>Status do Vínculo</th>
                    <th>Status do Benefício</th>
                    <th>Dias Úteis</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th class="w-100px min-w-100px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <input type="checkbox" name="item_ck" class="check_individual">
                                <input type="hidden" class="item_id" value="<?php echo $data[$i]["CustomerUserItinerary"]["id"]; ?>">
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["CustomerUser"]["name"]) ? $data[$i]["CustomerUser"]["name"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["CustomerUser"]["cpf"]) ? $data[$i]["CustomerUser"]["cpf"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["CustomerUser"]["email"]) ? $data[$i]["CustomerUser"]["email"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["Benefit"]["code"]) ? $data[$i]["Benefit"]["code"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["Benefit"]["name"]) ? $data[$i]["Benefit"]["name"] : ''; ?></td>
                            <td>
                                <span class='badge <?php echo isset($data[$i]["Status"]["label"]) ? $data[$i]["Status"]["label"] : '' ?>'>
                                    <?php echo isset($data[$i]["Status"]["name"]) ? $data[$i]["Status"]["name"] : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                // Get benefit status separately if needed
                                $benefitStatusLabel = '';
                                $benefitStatusName = '';
                                if (isset($data[$i]["Benefit"]["status_id"])) {
                                    foreach ($status as $st) {
                                        if ($st['Status']['id'] == $data[$i]["Benefit"]["status_id"]) {
                                            $benefitStatusLabel = $st['Status']['label'];
                                            $benefitStatusName = $st['Status']['name'];
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <span class='badge <?php echo $benefitStatusLabel ?>'>
                                    <?php echo $benefitStatusName ? $benefitStatusName : 'N/A' ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["CustomerUserItinerary"]["working_days"]) ? $data[$i]["CustomerUserItinerary"]["working_days"] : '0'; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($data[$i]["CustomerUserItinerary"]["quantity"]) ? $data[$i]["CustomerUserItinerary"]["quantity"] : '0'; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo isset($data[$i]["CustomerUserItinerary"]["unit_price"]) ? $data[$i]["CustomerUserItinerary"]["unit_price"] : '0,00'; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->base . '/customer_users/itineraries/' . $id . '/' . $data[$i]["CustomerUserItinerary"]["customer_user_id"]; ?>" class="btn btn-info btn-sm">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="12" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>

        <div class="card-footer d-flex justify-content-start py-4">
            <a href="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "index", $id)); ?>" class="btn btn-light-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>
</div>

<!-- Modal for Activate Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_ativar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Ativação</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja ativar os vínculos selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="ativa_confirm" class="btn btn-success">Sim, Ativar</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Inactivate Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_inativar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Inativação</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja inativar os vínculos selecionados?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="inativa_confirm" class="btn btn-warning">Sim, Inativar</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_excluir_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Exclusão</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir os vínculos selecionados? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="exclui_confirm" class="btn btn-danger">Sim, Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Reset filters
        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#status_link").val(null).trigger('change');
            $("#status_benefit").val(null).trigger('change');
            $("#benefit_code").val('');
            $("#benefit_name").val('');
            $("#q").val('');
            $("#busca").submit();
        });

        // Activate button click
        $('#ativar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_ativar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item para ativar');
            }
        });

        // Inactivate button click
        $('#inativar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_inativar_sel').modal('show');
            } else {
                alert('Selecione ao menos um item para inativar');
            }
        });

        // Delete button click
        $('#excluir_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_excluir_sel').modal('show');
            } else {
                alert('Selecione ao menos um item para excluir');
            }
        });

        // Activate confirmation
        $('#ativa_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const itineraryIds = [];

            checkboxes.each(function() {
                itineraryIds.push($(this).parent().find('.item_id').val());
            });

            if (itineraryIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_activate_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[itineraryIds]',
                    'value': JSON.stringify(itineraryIds)
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[customerId]',
                    'value': customerId
                }));

                // Preserve current filter parameters
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[queryString]',
                    'value': window.location.search.substring(1)
                }));

                $('body').append(form);
                form.submit();
            }
        });

        // Inactivate confirmation
        $('#inativa_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const itineraryIds = [];

            checkboxes.each(function() {
                itineraryIds.push($(this).parent().find('.item_id').val());
            });

            if (itineraryIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_inactivate_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[itineraryIds]',
                    'value': JSON.stringify(itineraryIds)
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[customerId]',
                    'value': customerId
                }));

                // Preserve current filter parameters
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[queryString]',
                    'value': window.location.search.substring(1)
                }));

                $('body').append(form);
                form.submit();
            }
        });

        // Delete confirmation
        $('#exclui_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const itineraryIds = [];

            checkboxes.each(function() {
                itineraryIds.push($(this).parent().find('.item_id').val());
            });

            if (itineraryIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_delete_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[itineraryIds]',
                    'value': JSON.stringify(itineraryIds)
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[customerId]',
                    'value': customerId
                }));

                // Preserve current filter parameters
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[queryString]',
                    'value': window.location.search.substring(1)
                }));

                $('body').append(form);
                form.submit();
            }
        });

        // Check all functionality
        $(".check_all").on("change", function() {
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }
        });
    });
</script>
