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
                                    <th>Desconto</th>
                                    <th>Valor Total</th>
                                    <th>Assinatura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itens as $item) { ?>
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
                                        <td><?php echo $item['Order']['desconto']; ?></td>
                                        <td><?php echo $item[0]['total']; ?></td>
                                        <td></td>
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

    .break { page-break-after: always !important; }
</style>
