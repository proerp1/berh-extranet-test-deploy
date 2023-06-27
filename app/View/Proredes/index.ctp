<?php $url_novo = $this->base."/proredes/add/"; ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "proredes", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
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

                    <!-- <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">Novo</a> -->

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Número</th>
                        <th>Data</th>
                        <th>Arquivo</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Prorede"]["numero"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]["Prorede"]["created"])); ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/proredes/download_remessa/'.$data[$i]["Prorede"]["arquivo"]; ?>">
                                        <?php echo $data[$i]["Prorede"]["arquivo"]; ?>
                                    </a>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/proredes/view/'.$data[$i]["Prorede"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Visualizar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>