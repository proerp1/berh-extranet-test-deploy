<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Nome</th>
                        <th>Vencimento</th>
                        <th>Valor a receber R$</th>
                        <th>Valor recebido R$</th>
                        <th>Data recebimento</th>
                        <th>Remessa</th>
                        <th>Sequência</th>
                        <th class="w-250px min-w-250px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["Customer"]["codigo_associado"].' - '.$data[$i]["Income"]["Customer"]["nome_secundario"] ; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["vencimento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["valor_pago"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Income"]["data_pagamento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo str_pad($data[$i]['Income']['cnab_lote_id'], 6, 0, STR_PAD_LEFT) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Income']['cnab_num_sequencial'] ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/incomes/edit/'.$data[$i]["Income"]["id"]; ?>" class="btn btn-info btn-sm">Detalhes</a>
                                    <?php if ($data[$i]["CnabItem"]["id_web"]) { ?>
                                        <a href="<?php echo $this->base.'/incomes/gerar_boleto/'.$data[$i]["Income"]["id"].'/true'; ?>" class="btn btn-success btn-sm">Ver boleto</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button type="button" onclick="history.go(-1)" class="btn btn-light-dark">Voltar</button>
    </div>
</div>