<?php echo $this->element("abas_suppliers", array('id' => $id)); ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Tipo Repasse</th>
                        <th>Realiza Gestão Eficiente</th>
                        <th>Modalidade</th>
                        <th>Tecnologia</th>
                        <th>Versão Crédito</th>
                        <th>Versão Cadastro</th>
                        <th>Tipo Conta</th>
                        <th>Banco</th>
                        <th>Forma Pagamento</th>
                        <th>Agência</th>
                        <th>Conta</th>
                        <th>Chave PIX</th>
                        <th>Data Alteração</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $repasses = [1 => 'Valor', 2 => 'Percentual'];
                                $payment_method = ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito', '7' => 'Débito em conta', '8' => 'Dinheiro', '2' => 'Transferência', '9' => 'Desconto', '11' => 'Pix', '10' => 'Outros'];
                                $pix_types = ['' => '-', 'cnpj' => 'CNPJ', 'cpf' => 'CPF', 'email' => 'E-mail', 'celular' => 'Celular', 'chave' => 'Chave', 'qr code' => 'Qr Code', 'aleatoria' => 'Aleatória'];

                                $pix = $data[$i]['LogSupplier']['pix_type'] ? $pix_types[$data[$i]['LogSupplier']['pix_type']].' - '.$data[$i]['LogSupplier']['pix_id'] : '-';
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $repasses[$data[$i]['LogSupplier']['transfer_fee_type']]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogSupplier']['realiza_gestao_eficiente'] ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Modalidade']['name'] ?: '-'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Tecnologia']['name'] ?: '-'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['VersaoCredito']['nome'] ?: '-'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['VersaoCadastro']['nome'] ?: '-'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['BankAccountType']['description']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['BankCode']['name']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $payment_method[$data[$i]['LogSupplier']['payment_method']]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogSupplier']['branch_number'].'-'.$data[$i]['LogSupplier']['branch_digit']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogSupplier']['acc_number'].'-'.$data[$i]['LogSupplier']['acc_digit']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $pix; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['LogSupplier']['created']; ?></td>
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
