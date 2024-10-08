<?php $url_novo = $this->base."/atendimentos/add/"; ?>
<div class="card mb-5 mb-xl-8">
<form action="<?php echo $this->Html->url(array("controller" => "atendimentos", "action" => "index")); ?>/" role="form" id="busca" method="get">
    <div class="card-header border-0 pt-6 mb-3">
        <div class="card-title">
            <div class="col-md-6 d-flex align-items-center my-1">
                <span class="position-absolute ms-6">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
            </div>
            <div class="col-md-6 d-flex justify-content-evenly" style="margin-top: 5px">
                <span class="badge badge-success" style="margin-right: 10px">
                    Atendidos: <?php echo $atendidos ?>
                </span>
                <span class="badge badge-warning">
                    Pendentes: <?php echo $pendentes ?>
                </span>
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="fas fa-filter"></i>
                    Filtro
                </button>
                <a href="<?php echo $this->Html->url(['controller' => 'atendimentos', 'action' => 'index', '?' => array_merge($_GET, ['exportar' => 'true'])]); ?>" class="btn btn-light-primary me-3">
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
                            <label class="form-label fs-5 fw-bold mb-3">Departamentos:</label>
                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                <option></option>
                                <?php
                                    for($a = 0; $a < count($departments); $a++){
                                        $selected = "";
                                        if (isset($_GET["t"])) {
                                            if($departments[$a]['Department']['id'] == $_GET["t"]){
                                                $selected = "selected";
                                            }
                                        }
                                        echo '<option value="'.$departments[$a]['Department']['id'].'" '.$selected.'>'.$departments[$a]['Department']['name'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-10">
                            <label class="form-label fs-5 fw-bold mb-3">Cliente:</label>
                            <input type="text" class="form-control form-control-solid fw-bolder" name="cliente" id="cliente" placeholder="Digite o nome do cliente" value="<?php echo isset($_GET['cliente']) ? $_GET['cliente'] : ''; ?>">
                        </div>
                        <div class="mb-10">
                            <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="status" id="status">
                                <option></option>
                                <option value="35" <?php echo isset($_GET['status']) && $_GET['status'] == '35' ? 'selected' : ''; ?>>Atendido</option>
                                <option value="34" <?php echo isset($_GET['status']) && $_GET['status'] == '34' ? 'selected' : ''; ?>>Pendente</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                            <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                        </div>
                    </div>
                </div>
                <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">Novo</a>
            </div>
        </div>
    </div>
</form>


    <form action="<?php echo $this->Html->url(array('controller' => 'atendimentos', 'action' => 'index')); ?>" method="post" id="exportForm">
        <input type="hidden" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>">
        <input type="hidden" name="t" value="<?php echo isset($_GET["t"]) ? $_GET["t"] : ""; ?>">
        <input type="hidden" name="cliente" value="<?php echo isset($_GET["cliente"]) ? $_GET["cliente"] : ""; ?>">
        <input type="hidden" name="export_excel" value="1">
    </form>

    <div class="card-body pt-0 py-3">
    <?php echo $this->element("pagination"); ?>
    <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 min-w-125px rounded-start">Status</th>
                        <th>Atendimento N°</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Departamento</th>
                        <th>Atendente</th>
                        <th>Arquivo</th>
                        <th>Assunto</th>
                        <th>Enviado em</th>
                        <th>Finalizado em</th>
                        <th>Finalizado por</th>
                        <th class="min-w-100px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Atendimento"]["id"] ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Customer"]["nome_primario"] ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Customer"]["documento"] ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Department"]["name"] ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Atendimento"]["name_atendente"] ?></td>
                                <td class="fw-bold fs-7">
                                    <a href="<?php echo $this->webroot.'files/atendimento/file_atendimento/'.$data[$i]["Atendimento"]["id"].'/'.$data[$i]["Atendimento"]["file_atendimento"]; ?>"><?php echo $data[$i]["Atendimento"]["file_atendimento"]; ?></a>
                                </td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Atendimento"]["subject"]; ?></td>
                                <td class="fw-bold fs-7"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]["Atendimento"]["created"])); ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Atendimento"]["data_finalizacao"] ?></td>
                                <td class="fw-bold fs-7"><?php echo $data[$i]["Atendimento"]["finalizado_por"] ?></td>
                                <td class="fw-bold fs-7">
                                    <a href="<?php echo $this->base.'/atendimentos/view/'.$data[$i]["Atendimento"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Visualizar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="10">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>

<script>
    $(document).ready(function(){
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })  
</script>
