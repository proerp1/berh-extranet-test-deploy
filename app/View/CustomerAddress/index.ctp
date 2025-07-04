<?php
if (isset($customer_id)) {
    $url = $this->here;
    echo $this->element("abas_customers", array('id' => $customer_id, 'url' => $url));
}
?>
<?php $url_novo = $this->base."/customer_address/add/".$customer_id; ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customer_address", "action" => "index", $customer_id)); ?>/" role="form" id="busca" autocomplete="off">
        <?php if (isset($_GET['logon'])): ?>
            <input type="hidden" name="logon" value="">
        <?php endif ?>
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>

                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="<?php echo isset($_GET['logon']) ? 'Digite o logon' : 'Buscar' ?>" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">Novo</a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>

                        <div class="px-7 py-5">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
            <tr class="fw-bolder text-muted bg-light">
                <th>Status</th>
                <th>Nome</th>
                <th>Endereço Completo</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>CEP</th>
                <th>Usuário/Beneficiário</th>
                <th class="w-200px min-w-200px rounded-end">Ações</th>
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
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerAddress"]["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerAddress"]["address"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerAddress"]["city"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerAddress"]["state"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerAddress"]["zip_code"]; ?></td>
                        <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerUser"]["name"]; ?></td>
                        <td class="fw-bold fs-7 ps-4">
                            <a href="<?php echo $this->base; ?>/customer_address/edit/<?php echo $data[$i]["Customer"]["id"]; ?>/<?php echo $data[$i]["CustomerAddress"]["id"]; ?>" class="btn btn-info btn-sm">
                                Editar
                            </a>
                            <a href="javascript:" onclick="verConfirm('<?php echo $this->base; ?>/customer_address/delete/<?php echo $data[$i]["Customer"]["id"]; ?>/<?php echo $data[$i]["CustomerAddress"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                Excluir
                            </a>
                        </td>
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
        <?php echo $data ? $this->element("pagination") : ''; ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#f").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $('#c').on('change', function () {
            $("#busca").submit();
        });
    })
</script>

<!-- Modal -->
<div class="modal fade" id="modal_filtro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_filtroLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="confirm-simple-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabelSimple">Simular Filtro de Adesão</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="recipient-name" class="col-form-label">CNPJ:</label>
                            <input type="text" name="cnpj" id="cnpj" class="form-control">
                        </div>
                    </div>

                    <div class="row" id="container-resultado" style="margin-top: 20px; display:none;">
                        <div class="mb-3 col-12">
                            <label for="recipient-name" class="col-form-label">Resultado:</label>
                            <span id="result-messsage"></span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <span id="loading" style="display:none">Carregando...</span>
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Voltar</button>
                    <a type="submit" class="btn btn-primary js-salvar" id="consulta-cnpj">Simular</a>
                </div>
            </form>
        </div>
    </div>
</div>
