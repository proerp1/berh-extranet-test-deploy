<?php if (!empty($itens)) { ?>
    <?php foreach ($itens as $item) { ?>
<div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="background-color: #ffffff;">
        <div class="cell p-10">
            <img src="<?php echo $link."/img/logo-berh-colorido.png"; ?>" alt="" width="150">
        </div>
            <div class="modal-header" style="text-align: center;">
                <h1 class="modal-title mx-auto" id="relatorioModalLabel">Relatório de Entrega de Benefícios</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="content text-center mb-4">
                    <h2>Detalhes do Pedido</h2>
                    <h4>Data da Impressão: <?php echo date('d/m/Y'); ?></h4>
                    <h4>Período de Utilização: <?php echo $order['Order']['order_period_from']; ?> a <?php echo $order['Order']['order_period_to']; ?></h4>
                    <h4>Razão Social: <?php echo $order['Customer']['nome_secundario']; ?></h4>
                    <h4>CNPJ: <?php echo $order['Customer']['documento']; ?></h4>
                </div>
                <div class="table-container mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Nome</th>
                                <th>Matrícula</th>
                                <th>CPF</th>
                                <th>Código</th>
                                <th>Item</th>
                                <th>Dias</th>
                                <th>Quant. por dia</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $order['Order']['id']; ?></td>
                                <td><?php echo $item['CustomerUser']['nome']; ?></td>
                                <td><?php echo $item['CustomerUser']['matricula']; ?></td>
                                <td><?php echo $item['CustomerUser']['cpf']; ?></td>
                                <td><?php echo $item['CustomerUserItinerary']['benefit_code']; ?></td>
                                <td><?php echo $item['CustomerUserItinerary']['benefit_name']; ?></td>
                                <td><?php echo $item[0]['working_days']; ?></td>
                                <td><?php echo $item[0]['qtd']; ?></td>
                                <td><?php echo $item['CustomerUserItinerary']['unit_price']; ?></td>
                                <td><?php echo $item[0]['valor']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="signature text-center mt-4">
                    <p>Data: ___ / ___ / ____</p>
                    <p>Assinatura: ____________________________________________________________</p>
                </div>
                <div class="footer text-center mt-4">
                    <p>BERH © 2024 Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
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
</style>
