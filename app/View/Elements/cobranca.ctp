<?php if (!empty($itens)) { ?>
    <div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="background-color: #ffffff;">
                <div class="cell p-10">
                    <img src="<?php echo $link . "/img/logo-berh-colorido.png"; ?>" alt="" width="150">
                </div>
                <div class="modal-header" style="text-align: center;">
                    <h1 class="modal-title mx-auto" id="relatorioModalLabel">Cobranças</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-container mb-4">
                        <h3>Informações Gerais</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>N° Pedido</th>
                                    <th>CNPJ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo date('d/m/Y'); ?></td>
                                    <td><?php echo $order['Order']['id']; ?></td>
                                    <td><?php echo $order['Customer']['documento']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container mb-4">
                        <h3>Custo Operadora</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Operadora</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($itens as $item){ ?>
                                    <tr>
                                        <td><?php echo $item['Supplier']['razao_social']; ?></td>
                                        <td><?php echo number_format($item[0]['subtotal'],2,',','.'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container mb-4">
                        <h3>Repasse</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Repasse</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($itens as $item){ ?>
                                <tr>
                                    <td><?php echo $item['Supplier']['razao_social']; ?></td>
                                    <td><?php echo number_format($item[0]['transfer_fee'],2,',','.'); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container mb-4">
                        <h3>Custo BeRe-Tx Adm</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Taxa</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($itens as $item){ ?>
                                <tr>
                                    <td><?php echo $item['Supplier']['razao_social']; ?></td>
                                    <td><?php echo number_format($item[0]['commission_fee'],2,',','.'); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-container mb-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Desconto</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                    <td><?php echo $item['OrderItem']['saldo']; ?></td>
                                    <td><?php echo $order['Order']['total'] ; ?></td>
                                    </tr>
                                
                            </tbody>
                        </table>
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
        table-layout: fixed;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        width: 50%; 
    }
    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>
