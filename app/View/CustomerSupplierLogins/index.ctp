<?php $url_novo = $this->base."/customer_supplier_logins/add/".$tipo."/".$id; ?>

<?php if ($tipo == 1) { ?>
    <?php echo $this->element("abas_customers", ['id' => $id]); ?>
<?php } else { ?>
    <?php echo $this->element("abas_suppliers", ['id' => $id]); ?>
<?php } ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customer_supplier_logins", "action" => "index", $tipo, $id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Novo</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Status</th>
                        <?php if ($tipo == 1) { ?>
                            <th class="w-400px min-w-400px">Fornecedor</th>
                        <?php } else { ?>
                            <th class="w-150px min-w-150px">ID Cliente</th>
                            <th>Razão Social</th>
                            <th>CNPJ</th>
                        <?php } ?>
                        <th>Grupo Econômico</th>
                        <th>URL</th>
                        <th>Login</th>
                        <th>Senha</th>
                        <th>Observação</th>
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
                                <?php if ($tipo == 1) { ?>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Supplier"]["nome_fantasia"]; ?></td>
                                <?php } else { ?>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["id"]; ?></td>
                                    <td><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                                    <td><?php echo $data[$i]["Customer"]["documento"]; ?></td>
                                <?php } ?>
                                <td class="fw-bold fs-7 ps-4"><?php echo !empty($data[$i]["EconomicGroup"]["name"]) ? $data[$i]["EconomicGroup"]["name"] : "Todos"; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $data[$i]["CustomerSupplierLogin"]["url"]; ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo $data[$i]["CustomerSupplierLogin"]["url"]; ?>
                                    </a>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerSupplierLogin"]["login"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerSupplierLogin"]["password"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerSupplierLogin"]["observation"]; ?></td>

                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/customer_supplier_logins/edit/'.$tipo.'/'.$id.'/'.$data[$i]["CustomerSupplierLogin"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/customer_supplier_logins/delete/'.$tipo.'/'.$id.'/'.$data[$i]["CustomerSupplierLogin"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="10" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
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