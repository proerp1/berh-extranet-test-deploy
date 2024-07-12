<?php if (!empty($itens)) { ?>
    <?php foreach ($itens as $index => $item) { ?>
        <div class="modal fade <?php echo $index > 0 ? 'page-break' : ''; ?>" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="background-color: #ffffff;">
                    <div class="cell p-10">
                        <img src="<?php echo $link."/img/logo-berh-colorido.png"; ?>" alt="" width="150">
                    </div>
                    <div class="modal-header" style="text-align: center;">
                        <h1 class="modal-title mx-auto" id="relatorioModalLabel">Resumo</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-container mb-4">
                            <h3>Informações do Pedido</h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data da Impressão</th>
                                        <th>CNPJ</th>
                                        <th>Qtde Operadoras</th>
                                        <th>N° Pedido</th>
                                        <th>Qtde Colaboradores</th>
                                        <th>Data Pedido</th>
                                        <th>Data Pagamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('d/m/Y'); ?></td>
                                        <td><?php echo $order['Customer']['documento']; ?></td>
                                        <td><?php echo $suppliersCount; ?></td>
                                        <td><?php echo $order['Order']['id']; ?></td>
                                        <td><?php echo $usersCount; ?></td>
                                        <td><?php echo $order['Order']['created']; ?></td>
                                        <td><?php echo $order['Income']['data_pagamento']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-container mb-4">
                            <h3>Itens</h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Valor(es) do(s) Beneficio(s)</th>
                                        <th>Custo(s) da(s) Recarga(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <?php echo $order['Order']['subtotal']; ?></td>
                                        <td> <?php echo $order['Order']['transfer_fee']; ?></td>
                                    </tr>
                                    </tbody>
                            </table>
                        </div>

                        <div class="table-container mb-4">
                            <h3>Valor das Taxas</h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Taxa de serviço</th>
                                        <th>TPP(ou)Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <?php echo $order['Order']['commission_fee']; ?></td>
                                        <td><?php echo $order['Order']['tpp_fee']; ?></td>
                                    </tr>
                            </tbody>
                            </table>
                        </div>

                        

                        <div class="table-container mb-4">
                            <h3>Valor(es) Total(is)</h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Desconto</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td><?php echo $order['Order']['desconto']; ?></td>
                                    <td><?php echo $order['Order']['total']; ?></td>
                                    </tr>
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
    .page-break {
        page-break-before: always;
    }
</style>
