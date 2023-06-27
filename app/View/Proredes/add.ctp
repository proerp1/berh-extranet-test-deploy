<?php $url_novo = $this->base."/proredes/gerar_remessa/"; ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <h4>Total: <?php echo count($clientes) ?></h4>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Código</th>
                        <th>Razão social</th>
                        <th>Nome fantasia</th>
                        <th class="w-150px min-w-150px rounded-end">CNPJ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clientes) { ?>
                        <?php for ($i=0; $i < count($clientes); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $clientes[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $clientes[$i]["Customer"]["nome_primario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $clientes[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $clientes[$i]["Customer"]["documento"]; ?></td>
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
        <a href="<?php echo $this->base; ?>/proredes/" class="btn btn-light-dark">Voltar</a>
    </div>
</div>