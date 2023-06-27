<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Documento</th>
                        <th>Nome</th>
                        <th>Data solic. exclusão</th>
                        <th class="w-250px min-w-250px rounded-end">Motivo baixa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CadastroPefin"]["documento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CadastroPefin"]["nome"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CadastroPefin"]["data_solic_baixa"] != '' ? date('d/m/Y H:i:s', strtotime($data[$i]["CadastroPefin"]["data_solic_baixa"])) : ''; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["MotivoBaixa"]["nome"]; ?></td>
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