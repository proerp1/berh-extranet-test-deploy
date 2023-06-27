<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "status_clientes")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->base.'/reports/status_clientes/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
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
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                    <option></option>
                                    <option value="4" <?php echo isset($_GET['s']) && $_GET['s'] == 4 ? 'selected' : '' ?>>Bloqueado</option>
                                    <option value="5" <?php echo isset($_GET['s']) && $_GET['s'] == 5 ? 'selected' : '' ?>>Cancelado</option>
                                    <option value="6" <?php echo isset($_GET['s']) && $_GET['s'] == 6 ? 'selected' : '' ?>>Aguardando Carta</option>
                                    <option value="41" <?php echo isset($_GET['s']) && $_GET['s'] == 41 ? 'selected' : '' ?>>Inadimplente</option>
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">Código</th>
                        <th>Nome fantasia</th>
                        <th>Cancelado</th>
                        <th>Bloqueado</th>
                        <th>Inadimplente</th>
                        <th>Aguradando Carta</th>
                        <th class="w-200px min-w-200px rounded-end">Login de Consulta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados) { ?>
                        <?php for ($i=0; $i < count($dados); $i++) { ?>
                            <?php 
                                $status_cliente = $dados[$i]["Customer"]["status_id"];
                                $status_resposta = "";

                                switch ($status_cliente) {
                                    case 4:
                                        $status_resposta = "Bloqueado";
                                        break;
                                    case 5:
                                        $status_resposta = "Cancelado";
                                        break;
                                    case 6:
                                        $status_resposta = "Aguardando Carta";
                                        break;
                                    
                                    default:
                                        $status_resposta = "Inadimplente";
                                        break;
                                 }
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($status_resposta == "Cancelado") ? "Sim" : "Não" ; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($status_resposta == "Bloqueado") ? "Sim" : "Não" ; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($status_resposta == "Inadimplente") ? "Sim" : "Não" ; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($status_resposta == "Aguardando Carta") ? "Sim" : "Não" ; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Status"]["name"] ?></td>
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
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null);

            $("#busca").submit();
        });
    });
</script>