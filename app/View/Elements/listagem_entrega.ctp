<?php if (!empty($itens)) { ?>
    <?php foreach ($itens as $item) { ?>
        <div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="background-color: #ffffff;">
                    <div class="cell p-10">
                        <img src="<?php echo $link."/img/logo-berh-colorido.png" ?>" alt="" width="150">
                    </div>
                    <div class="modal-header" style="text-align: center;">
                        <h1 class="modal-title mx-auto" id="relatorioModalLabel">Listagem de Entrega</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="content text-center mb-4">
                            <h2>Detalhes do Pedido</h2>
                        </div>
                        <div class="table-container mb-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Razão Social</th>
                                        <th>CNPJ</th>
                                        <th>Pedido</th>
                                        <th>Operadora</th>
                                        <th>CPF</th>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>Quantidade</th>
                                        <th>Unit</th>
                                        <th>Valor Total</th>
                                        <th>Data Recarga</th>
                                        <th>Data</th>
                                        <th>Período de Utilização</th>
                                        <th>Total Registros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $order['Customer']['nome_secundario'] ?></td>
                                        <td><?php echo $order['Customer']['documento'] ?></td>
                                        <td><?php echo $order['Order']['id'] ?></td>
                                        <td></td>
                                        <td><?php echo $item['CustomerUser']['cpf'] ?></td>
                                        <td><?php echo $item['CustomerUser']['matricula'] ?></td>
                                        <td><?php echo $item['CustomerUser']['nome'] ?></td>
                                        <td><?php echo $item[0]['qtd'] ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['unit_price']; ?></td>
                                        <td><?php echo $item[0]['total'] ?></td>
                                        <td><?php echo $item['Order']['credit_release_date'] ?></td>
                                        <td><?php echo date('d/m/Y'); ?></td>
                                        <td><?php echo $order['Order']['order_period_from']; ?> a <?php echo $order['Order']['order_period_to']; ?></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="signature text-center mt-4" style="margin-top: 50px;">
                            <br>
                            <p>Assinatura: ____________________________</p>
                        </div>
                        <div class="footer text-center mt-4" style="margin-top: 100px;">
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