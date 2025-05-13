<style>
    @page {
        size: landscape;
        margin: 20mm;
    }
    .modal-content {
        width: 100%;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        word-wrap: break-word;
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
    .page-break {
        page-break-before: always;
    }
</style>

<?php if (!empty($data)) { ?>
    <div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="background-color: #ffffff;">
                <div class="cell p-10">
                    <img src="<?php echo $link."/img/logo-berh-colorido.png"; ?>" alt="" width="150">
                </div>
                <div class="modal-header" style="text-align: center;">
                    <h1 class="modal-title mx-auto" id="relatorioModalLabel">Relatório de Processamento</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-container mb-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data Geração Pedido</th>
                                    <th>Pedido</th>
                                    <th>Código Cliente</th>
                                    <th>CNPJ Cliente</th>
                                    <th>Razão Social</th>
                                    <th>Status Pedido</th>
                                    <th>Nome</th>
                                    <th>Matrícula</th>
                                    <th>CPF</th>
                                    <th>Cartão</th>
                                    <th>Dias Úteis</th>
                                    <th>Qtde</th>
                                    <th>Id(Código Operadora)</th>
                                    <th>Operadora</th>
                                    <th>Cod. do Benefício</th>
                                    <th>Vl. Unit</th>
                                    <th>Total</th>
                                    <th>Repasse</th>
                                    <th>Taxa ADM</th>
                                    <th>Status Operadora</th>
                                    <th>Compra Operadora</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $currentUserId = null;
                            $totalInicial = 0;
                            $totalDesconto = 0;
                            $totalDisponibilizado = 0;

                            foreach ($data as $index => $item) {
                                if ($currentUserId !== $item['CustomerUser']['id']) {
                                    if ($currentUserId !== null) {
                                        ?>
                                        <tr>
                                            <td colspan="21">
                                                <div class="totals">
                                                    <span>Total Inicial: R$<?php echo number_format($totalInicial, 2, ',', '.'); ?></span>
                                                    <span>Total Desconto: R$<?php echo number_format($totalDesconto, 2, ',', '.'); ?></span>
                                                    <span>Total Disponibilizado: R$<?php echo number_format($totalDisponibilizado, 2, ',', '.'); ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        $totalInicial = 0;
                                        $totalDesconto = 0;
                                        $totalDisponibilizado = 0;
                                    }
                                    $currentUserId = $item['CustomerUser']['id'];
                                }

                                $totalInicial += (float)$item['OrderItem']['subtotal_not_formated'];
                                $totalDesconto += (float)$item['OrderItem']['saldo_not_formated'];
                                $totalDisponibilizado += (float)$item['OrderItem']['subtotal_not_formated'] - (float)$item['OrderItem']['saldo_not_formated'];
                            ?>
                                <tr>
                                    <td><?php echo $item['Order']['created']; ?></td>
                                    <td><?php echo $item['Order']['id']; ?></td>
                                    <td><?php echo $item['Customer']['codigo_associado']; ?></td>
                                    <td><?php if (!empty($order['EconomicGroup']['name'])): ?><?php echo $order['EconomicGroup']['name']; ?><?php else: ?><?php echo $item['Customer']['nome_secundario']; ?><?php endif; ?></td>
                                    <td><?php if (!empty($order['EconomicGroup']['document'])): ?><?php echo $order['EconomicGroup']['document']; ?><?php else: ?><?php echo $item['Customer']['documento']; ?><?php endif; ?></td>
                                    <td><?php echo $item['Status']['name']; ?></td>
                                    <td><?php echo $item['CustomerUser']['name']; ?></td>
                                    <td><?php echo $item['CustomerUser']['matricula']; ?></td>
                                    <td><?php echo $item['CustomerUser']['cpf']; ?></td>
                                    <td><?php echo $item['CustomerUserItinerary']['card_number']; ?></td>
                                    <td><?php echo $item['OrderItem']['working_days']; ?></td>
                                    <td><?php echo $item['CustomerUserItinerary']['quantity']; ?></td>
                                    <td><?php echo $item['Supplier']['id']; ?></td>
                                    <td><?php echo $item['Supplier']['nome_fantasia']; ?></td>
                                    <td><?php echo $item['Benefit']['code']; ?></td>
                                    <td><?php echo $item['CustomerUserItinerary']['unit_price']; ?></td>
                                    <td><?php echo $item['OrderItem']['total']; ?></td>
                                    <td><?php echo $item['OrderItem']['transfer_fee']; ?></td>
                                    <td><?php echo $item['OrderItem']['commission_fee']; ?></td>
                                    <td><?php echo $item['OrderItem']['status_processamento']; ?></td>
                                    <td><?php echo number_format(($item['OrderItem']['subtotal_not_formated'] - $item['OrderItem']['saldo_not_formated']), 2, ',', '.'); ?></td>
                                </tr>
                            <?php } ?>

                            <?php if ($currentUserId !== null) { ?>
                            <tr>
                                <td colspan="21">
                                    <div class="totals">
                                        <span>Total Inicial: R$<?php echo number_format($totalInicial, 2, ',', '.'); ?></span>
                                        <span>Total Desconto: R$<?php echo number_format($totalDesconto, 2, ',', '.'); ?></span>
                                        <span>Total Disponibilizado: R$<?php echo number_format($totalDisponibilizado, 2, ',', '.'); ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
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
