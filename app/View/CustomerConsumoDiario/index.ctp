<?php
    echo $this->element("abas_customers", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Data</th>
                        <th class="rounded-end">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php foreach ($data as $item) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($item["ConsumoDiarioItem"]['data'])) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $item[0]['qtde'] ?></td>
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