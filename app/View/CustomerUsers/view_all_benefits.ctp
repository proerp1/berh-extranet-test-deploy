<?php
echo $this->element("abas_customers", array('id' => $id));
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "view_all_benefits", $id)); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="fw-bold fs-4">Benefícios por Cliente</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

                    <div class="dropdown me-3">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Ações em Lote
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="dropdownMenuButton" style="width: 350px;">
                            <div class="d-flex flex-column justify-content-start gap-3">
                                <div class="fw-bold text-dark border-bottom pb-2">Ações nos Benefícios:</div>

                                <button type="button" id="ativar_beneficio_sel" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i>
                                    Ativar Benefícios
                                </button>

                                <button type="button" id="desativar_beneficio_sel" class="btn btn-warning">
                                    <i class="fas fa-ban"></i>
                                    Desativar Benefícios
                                </button>

                                <div class="fw-bold text-dark border-bottom border-top pt-3 pb-2 mt-2">Ações nos Vínculos:</div>

                                <button type="button" id="ativar_sel" class="btn btn-success btn-sm">
                                    <i class="fas fa-link"></i>
                                    Ativar Vínculos
                                </button>

                                <button type="button" id="inativar_sel" class="btn btn-warning btn-sm">
                                    <i class="fas fa-unlink"></i>
                                    Inativar Vínculos
                                </button>

                                <button type="button" id="excluir_sel" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                    Excluir Vínculos
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
                    <th>Código Benefício</th>
                    <th>Nome Benefício</th>
                    <th>Status do Benefício</th>
                    <th>Qtd. Beneficiários</th>
                    <th class="w-100px min-w-100px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($paginatedData) { ?>
                    <?php for ($i = 0; $i < count($paginatedData); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <input type="checkbox" name="item_ck" class="check_individual">
                                <input type="hidden" class="item_id" value="<?php echo $paginatedData[$i]["Benefit"]["id"]; ?>">
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($paginatedData[$i]["Benefit"]["code"]) ? $paginatedData[$i]["Benefit"]["code"] : ''; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo isset($paginatedData[$i]["Benefit"]["name"]) ? $paginatedData[$i]["Benefit"]["name"] : ''; ?></td>
                            <td>
                                <?php
                                // Get benefit status
                                $benefitStatusLabel = '';
                                $benefitStatusName = '';
                                if (isset($paginatedData[$i]["Benefit"]["status_id"])) {
                                    foreach ($status as $st) {
                                        if ($st['Status']['id'] == $paginatedData[$i]["Benefit"]["status_id"]) {
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
                            <td class="fw-bold fs-7 ps-4"><?php echo $paginatedData[$i]["customer_user_count"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <button type="button" class="btn btn-info btn-sm view-details"
                                        data-benefit-id="<?php echo $paginatedData[$i]["Benefit"]["id"]; ?>"
                                        data-benefit-name="<?php echo htmlspecialchars($paginatedData[$i]["Benefit"]["name"]); ?>"
                                        data-customer-id="<?php echo $id; ?>">
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>

        <?php if (isset($paginationInfo)): ?>
        <div class="row">
            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                <div class="dataTables_length">
                    Exibindo <?php echo $paginationInfo['current']; ?> de <?php echo $paginationInfo['count']; ?> registros
                </div>
            </div>
            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                <?php if ($paginationInfo['pageCount'] > 1): ?>
                <div class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <?php if ($paginationInfo['page'] > 1): ?>
                        <li class="paginate_button page-item previous">
                            <a href="?page=<?php echo $paginationInfo['page'] - 1; ?><?php echo isset($_GET['benefit_code']) ? '&benefit_code=' . $_GET['benefit_code'] : ''; ?><?php echo isset($_GET['benefit_name']) ? '&benefit_name=' . $_GET['benefit_name'] : ''; ?><?php echo isset($_GET['status_link']) ? '&status_link=' . $_GET['status_link'] : ''; ?><?php echo isset($_GET['status_benefit']) ? '&status_benefit=' . $_GET['status_benefit'] : ''; ?>" class="page-link">
                                <i class="previous"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $paginationInfo['pageCount']; $p++): ?>
                        <li class="paginate_button page-item <?php echo $p == $paginationInfo['page'] ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $p; ?><?php echo isset($_GET['benefit_code']) ? '&benefit_code=' . $_GET['benefit_code'] : ''; ?><?php echo isset($_GET['benefit_name']) ? '&benefit_name=' . $_GET['benefit_name'] : ''; ?><?php echo isset($_GET['status_link']) ? '&status_link=' . $_GET['status_link'] : ''; ?><?php echo isset($_GET['status_benefit']) ? '&status_benefit=' . $_GET['status_benefit'] : ''; ?>" class="page-link">
                                <?php echo $p; ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($paginationInfo['page'] < $paginationInfo['pageCount']): ?>
                        <li class="paginate_button page-item next">
                            <a href="?page=<?php echo $paginationInfo['page'] + 1; ?><?php echo isset($_GET['benefit_code']) ? '&benefit_code=' . $_GET['benefit_code'] : ''; ?><?php echo isset($_GET['benefit_name']) ? '&benefit_name=' . $_GET['benefit_name'] : ''; ?><?php echo isset($_GET['status_link']) ? '&status_link=' . $_GET['status_link'] : ''; ?><?php echo isset($_GET['status_benefit']) ? '&status_benefit=' . $_GET['status_benefit'] : ''; ?>" class="page-link">
                                <i class="next"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card-footer d-flex justify-content-start py-4">
            <a href="<?php echo $this->Html->url(array("controller" => "customer_users", "action" => "index", $id)); ?>" class="btn btn-light-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>
</div>

<!-- Modal for Activate Benefit Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_ativar_beneficio_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Ativação do Benefício</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja ativar <strong>os benefícios selecionados</strong>?</p>
                <p class="text-muted">Esta ação ativa o benefício globalmente (não apenas os vínculos com beneficiários).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="ativa_beneficio_confirm" class="btn btn-success">Sim, Ativar Benefícios</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deactivate Benefit Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_desativar_beneficio_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Desativação do Benefício</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja desativar <strong>os benefícios selecionados</strong>?</p>
                <p class="text-muted">Esta ação desativa o benefício globalmente (não apenas os vínculos com beneficiários).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="desativa_beneficio_confirm" class="btn btn-warning">Sim, Desativar Benefícios</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Activate Links Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_ativar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Ativação dos Vínculos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja ativar <strong>TODOS os vínculos</strong> dos benefícios selecionados?</p>
                <p class="text-muted">Esta ação afetará todos os beneficiários que possuem estes benefícios.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="ativa_confirm" class="btn btn-success">Sim, Ativar Vínculos</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Inactivate Links Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_inativar_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Inativação dos Vínculos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja inativar <strong>TODOS os vínculos</strong> dos benefícios selecionados?</p>
                <p class="text-muted">Esta ação afetará todos os beneficiários que possuem estes benefícios.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="inativa_confirm" class="btn btn-warning">Sim, Inativar Vínculos</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Links Confirmation -->
<div class="modal fade" tabindex="-1" id="modal_excluir_sel" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Exclusão dos Vínculos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <strong>TODOS os vínculos</strong> dos benefícios selecionados?</p>
                <p class="text-muted">Esta ação afetará todos os beneficiários que possuem estes benefícios e não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <a id="exclui_confirm" class="btn btn-danger">Sim, Excluir Vínculos</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Benefit Details -->
<div class="modal fade" tabindex="-1" id="modal_benefit_details" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalhes do Benefício: <span id="detail_benefit_name"></span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="benefit_details_content">
                    <div class="text-center py-5">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Carregando...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Fechar</button>
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
            $("#busca").submit();
        });

        // View details button
        $('.view-details').on('click', function(e) {
            e.preventDefault();
            const benefitId = $(this).data('benefit-id');
            const benefitName = $(this).data('benefit-name');
            const customerId = $(this).data('customer-id');

            $('#detail_benefit_name').text(benefitName);
            $('#modal_benefit_details').modal('show');

            // Load benefit details via AJAX
            $('#benefit_details_content').html('<div class="text-center py-5"><span class="spinner-border spinner-border-sm" role="status"></span> Carregando...</div>');

            $.ajax({
                url: base_url + '/customer_users/get_benefit_details',
                type: 'POST',
                data: {
                    benefit_id: benefitId,
                    customer_id: customerId
                },
                success: function(response) {
                    $('#benefit_details_content').html(response);
                },
                error: function() {
                    $('#benefit_details_content').html('<div class="alert alert-danger">Erro ao carregar detalhes</div>');
                }
            });
        });

        // Activate benefit button click
        $('#ativar_beneficio_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_ativar_beneficio_sel').modal('show');
            } else {
                alert('Selecione ao menos um benefício para ativar');
            }
        });

        // Deactivate benefit button click
        $('#desativar_beneficio_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_desativar_beneficio_sel').modal('show');
            } else {
                alert('Selecione ao menos um benefício para desativar');
            }
        });

        // Activate links button click
        $('#ativar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_ativar_sel').modal('show');
            } else {
                alert('Selecione ao menos um benefício para ativar os vínculos');
            }
        });

        // Inactivate button click
        $('#inativar_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_inativar_sel').modal('show');
            } else {
                alert('Selecione ao menos um benefício para inativar');
            }
        });

        // Delete links button click
        $('#excluir_sel').on('click', function(e) {
            e.preventDefault();

            if ($('input[name="item_ck"]:checked').length > 0) {
                $('#modal_excluir_sel').modal('show');
            } else {
                alert('Selecione ao menos um benefício para excluir os vínculos');
            }
        });

        // Activate confirmation
        $('#ativa_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const benefitIds = [];

            checkboxes.each(function() {
                benefitIds.push($(this).parent().find('.item_id').val());
            });

            if (benefitIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_activate_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[benefitIds]',
                    'value': JSON.stringify(benefitIds)
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
            const benefitIds = [];

            checkboxes.each(function() {
                benefitIds.push($(this).parent().find('.item_id').val());
            });

            if (benefitIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_inactivate_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[benefitIds]',
                    'value': JSON.stringify(benefitIds)
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
            const benefitIds = [];

            checkboxes.each(function() {
                benefitIds.push($(this).parent().find('.item_id').val());
            });

            if (benefitIds.length > 0) {
                // Create a form and submit it
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_delete_itineraries'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[benefitIds]',
                    'value': JSON.stringify(benefitIds)
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

        // Activate benefit confirmation
        $('#ativa_beneficio_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const benefitIds = [];

            checkboxes.each(function() {
                benefitIds.push($(this).parent().find('.item_id').val());
            });

            if (benefitIds.length > 0) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_enable_benefits'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[benefitIds]',
                    'value': JSON.stringify(benefitIds)
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[customerId]',
                    'value': customerId
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[queryString]',
                    'value': window.location.search.substring(1)
                }));

                $('body').append(form);
                form.submit();
            }
        });

        // Deactivate benefit confirmation
        $('#desativa_beneficio_confirm').on('click', function(e) {
            e.preventDefault();

            const customerId = <?php echo $id; ?>;
            const checkboxes = $('input[name="item_ck"]:checked');
            const benefitIds = [];

            checkboxes.each(function() {
                benefitIds.push($(this).parent().find('.item_id').val());
            });

            if (benefitIds.length > 0) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': base_url + '/customer_users/batch_disable_benefits'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[benefitIds]',
                    'value': JSON.stringify(benefitIds)
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'data[customerId]',
                    'value': customerId
                }));

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
