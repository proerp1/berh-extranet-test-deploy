<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "reports", "action" => "importar_movimentacao")); ?>" role="form" id="busca" autocomplete="off">
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
                    <a href="#" class="btn btn-primary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_importar_saldo">
                        <i class="fas fa-arrow-up"></i>
                        Importar Extrato em Lote (CSV)
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
                    <th>Arquivo</th>
                    <th>Data de criação</th>
                    <th>Usuário de criação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4"><a href="<?php echo $this->base.'/files/order_balances_all/'.$data[$i]["OrderBalanceFile"]["file_name"] ?>"><?php echo $data[$i]["OrderBalanceFile"]["file_name"]; ?></a></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["OrderBalanceFile"]["created"] ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Creator"]["name"]; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="10">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php if ($buscar) { ?>
            <?php echo $this->element("pagination"); ?>            
        <?php } ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_importar_saldo" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="<?php echo $this->base.'/orders/upload_saldo_csv_all/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>Enviar CSV com as movimentações a serem incluídos</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloImportacaoMovimentacaoAll.csv" targe="_blank" download>Baixar Modelo</a>
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

<style>
    table tr th a {
        color: #009ef7;
        display: block;
        width: 100%;
        height: 100%;
    }
</style>
