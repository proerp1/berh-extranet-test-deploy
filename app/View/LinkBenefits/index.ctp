
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "link_benefits", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
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

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 rounded-start">Data</th>
                    <th>Arquivo</th>
                    <th>Usuário</th>
                    <th class="rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <?php echo date('d/m/Y H:i:s', strtotime($data[$i]["LinkBenefit"]["created"])) ?>
                            </td>
                            <td class="fw-bold fs-7 ps-4">
                                <a download href="<?php echo $this->base.'/files/link_benefit/file_name/'.$data[$i]["LinkBenefit"]["id"].'/'.$data[$i]["LinkBenefit"]["file_name"] ?>"><?php echo $data[$i]["LinkBenefit"]["file_name"] ?></a>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["UserCreated"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->Html->url(['action' => 'logs', $data[$i]["LinkBenefit"]["id"]]); ?>" class="btn btn-info btn-sm">
                                    Logs
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
        <?php echo $data ? $this->element("pagination") : ''; ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_enviar_sptrans" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?php echo $this->base . '/link_benefits/upload_csv/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>Importar Beneficiários e Itinerários</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloAssociarCartao.csv" targe="_blank" download>Baixar Modelo</a>
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
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });
    });
</script>
