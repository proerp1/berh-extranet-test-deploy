<?php echo $this->element("abas_customers", array('id' => $id)); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Emitir Nota Fiscal</th>
                        <th>Saldo Inicial</th>
                        <th>Data Saldo Inicial</th>
                        <th>Data e hora da Alteração</th>
                        <th>Usuário Alteração</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $nota_fiscal_tipos = ['N' => 'Não', 'S' => 'Automático', 'A' => 'Antecipada', 'M' => 'Manual'];
                                $emite_nota_fiscal = $nota_fiscal_tipos[$data[$i]['LogCustomer']['emitir_nota_fiscal']]
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $emite_nota_fiscal; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['LogCustomer']['economia_inicial']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogCustomer']['dt_economia_inicial']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogCustomer']['created']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Creator']['name']; ?></td>
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
