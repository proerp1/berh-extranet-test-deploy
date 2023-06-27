<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "negativacao", "action" => "detalhes_lote", $id)); ?>/" role="form" id="busca" autocomplete="off">
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

                    <a href="<?php echo $this->base."/negativacao/export_detalhes/".$id."?" ;?>" class="btn btn-light-primary me-3">
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
                                    <option value="25" <?php echo (isset($_GET['t']) AND $_GET['t'] == 25) ? 'selected' : '' ?> >Inclusões</option>
                                    <option value="24" <?php echo (isset($_GET['t']) AND $_GET['t'] == 24) ? 'selected' : '' ?> >Exclusões</option>
                                    <option value="23" <?php echo (isset($_GET['t']) AND $_GET['t'] == 23) ? 'selected' : '' ?> >Erros</option>
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
                        <th>Nome do credor</th>
                        <th>CNPJ do credor</th>
                        <th>Código do Associado</th>
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Valor</th>
                        <th>Remessa</th>
                        <th>Sequência</th>
                        <th>Erros</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php $erros = $data[$i]['CadastroPefinErros'] ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                    <?php if ($data[$i]['CadastroPefin']['principal_id'] != ''): ?>
                                        <br><br>
                                        <span class="badge badge-warning" data-toggle="tooltip" data-placement="top" title="Coobrigado!"><i class="fa fa-exclamation-triangle"></i> </span>
                                    <?php endif ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['documento'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['nome'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['documento'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['valor'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['n_remessa'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CadastroPefin']['n_sequencial'] ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php 
                                        if (!empty($erros)) {
                                            for ($a=0; $a < count($erros); $a++) { 
                                                echo $erros[$a]['ErrosPefin']['descricao'].'<br>';
                                            }
                                        }
                                    ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/negativacao/view/'.$data[$i]["CadastroPefin"]["id"].(!empty($erros) ? '/alterar' : ''); ?>" class="btn btn-info btn-sm">Detalhes</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="11">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button type="button" onclick="history.go(-1)" class="btn btn-light-dark">Voltar</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
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