<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Data</th>
                        <th class="rounded-end">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) { ?>
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo date('d/m/Y H:i:s', strtotime($data[$i]["LinkBenefitLog"]["created"])) ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo $data[$i]["LinkBenefitLog"]["description"]; ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["UserCreated"]["name"]; ?></td>
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

<div class="mt-7">
    <div class="col-sm-offset-2 col-sm-9">
        <a href="<?php echo $this->Html->url(['action' => 'index']) ?>" class="btn btn-light-dark">Voltar</a>
    </div>
</div>