<?php if (!empty($itens)) { ?>
    <div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="background-color: #ffffff;">
                <div class="cell p-10">
                    <img src="<?php echo $link."/img/logo-berh-colorido.png"; ?>" alt="" width="150">
                </div>
                <div class="modal-header" style="text-align: center;">
                    <h1 class="modal-title mx-auto" id="relatorioModalLabel">Listagem de Entrega</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="content text-center mb-4">
                        <h2>Detalhes do Pedido</h2>
                        <h4>Período de Utilização: <?php echo date('d/m/Y', strtotime($de)); ?> a <?php echo date('d/m/Y', strtotime($para)); ?></h4>
                        <h4>Data: <?php echo date('d/m/Y'); ?></h4>
                    </div>
                    <div class="table-container mb-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Pedido</th>
                                    <th>Nome</th> 
                                    <th>CPF</th>
                                    <th>Matrícula</th>
                                    <th>Código</th>
                                    <th>Item</th>
                                    <th>Dias</th>
                                    <th>Qtde</th>
                                    <th>Unit</th>
                                    <th>Valor Total</th>
                                    <th>Assinatura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $currentUserId = null;
                                $totalInicial = 0;
                                $totalDesconto = 0;
                                $totalDisponibilizado = 0;

                                foreach ($itens as $index => $item) {
                                    // Se mudar de usuário ou for o último item, exibe os totais
                                    if ($currentUserId !== $item['CustomerUser']['id'] && $currentUserId !== null) {
                                        ?>
                                        <tr>
                                            <td colspan="14">
                                                <div class="totals">
                                                    <span>Total Inicial: R$<?php echo number_format($totalInicial, 2, ',', '.'); ?></span>
                                                    <span>Total Desconto: R$<?php echo number_format($totalDesconto, 2, ',', '.'); ?></span>
                                                    <span>Total Disponibilizado: R$<?php echo number_format($totalDisponibilizado, 2, ',', '.'); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        // Reseta os totais para o próximo usuário
                                        $totalInicial = 0;
                                        $totalDesconto = 0;
                                        $totalDisponibilizado = 0;
                                    }
                                    
                                    $currentUserId = $item['CustomerUser']['id'];

                                    $totalInicial += (float)$item['OrderItem']['subtotal_not_formated'];
                                    $totalDesconto += (float)$item['OrderItem']['saldo'];
                                    $totalDisponibilizado += (float)$item['OrderItem']['subtotal_not_formated'] - (float)$item['OrderItem']['saldo_not_formated'];
                                    ?>
                                    <tr>
                                        <td><?php echo $item['Customer']['nome_secundario']; ?></td>
                                        <td><?php echo $item['Customer']['documento']; ?></td>
                                        <td><?php echo $item['Order']['id']; ?></td>
                                        <td><?php echo $item['CustomerUser']['nome']; ?></td>
                                        <td><?php echo $item['CustomerUser']['cpf']; ?></td>
                                        <td><?php echo $item['CustomerUser']['matricula']; ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['benefit_code']; ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['benefit_name']; ?></td>
                                        <td><?php echo $item[0]['working_days']; ?></td>
                                        <td><?php echo $item[0]['qtd']; ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['unit_price']; ?></td>
                                        <td><?php echo number_format(($item['OrderItem']['subtotal_not_formated'] - $item['OrderItem']['saldo_not_formated']), 2, ',', '.'); ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    // Se mudar de usuário, exibe os totais
                                    if ($currentUserId !== $item['CustomerUser']['id'] && $currentUserId !== null) {
                                        ?>
                                        <tr>
                                            <td colspan="14">
                                                <div class="totals">
                                                    <span>Total Inicial: R$<?php echo number_format($totalInicial, 2, ',', '.'); ?></span>
                                                    <span>Total Desconto: R$<?php echo number_format($totalDesconto, 2, ',', '.'); ?></span>
                                                    <span>Total Disponibilizado: R$<?php echo number_format($totalDisponibilizado, 2, ',', '.'); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        // Reseta os totais para o próximo usuário
                                        $totalInicial = 0;
                                        $totalDesconto = 0;
                                        $totalDisponibilizado = 0;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="footer text-center mt-4">
                        <p>BERH © 2024 Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .totals {
        text-align: center;
        display: flex;
        justify-content: center;
        gap: 20px;
        font-weight: bold;
        padding: 10px 0;
    }
</style>
