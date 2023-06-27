<?php
    echo $this->element("abas_customers", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Login</th>
                        <th>Cliente</th>
                        <th>Valor R$</th>
                        <th>Dias</th>
                        <th>Validade</th>
                        <th>Contratação</th>
                        <th>Registro de baixa</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php foreach ($data as $item) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $item['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $item['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($item['ClienteMeProteja']['clienteMeProtejaValor'], 2, ',', '.') ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $item['ClienteMeProteja']['clienteMeProtejaDias'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($item['ClienteMeProteja']['clienteMeProtejaValidade'])) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($item['ClienteMeProteja']['clienteMeProtejaDataCadastro'])) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $item[0]['cronDataCancel'] ? date('d/m/Y', strtotime($item[0]['cronDataCancel'])) : ''; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($item['ClienteMeProteja']['clienteMeProtejaValidade'] > date('Y-m-d') && $item[0]['cronDataCancel'] == '') { ?>
                                        <a href="<?php echo $this->base."/meproteja/cancel_meproteja/".$id; ?>" target="_blank" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Desativar Meproteja</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>