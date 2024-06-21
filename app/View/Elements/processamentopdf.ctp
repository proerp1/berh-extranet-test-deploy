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
    .page-break {
        page-break-before: always;
    }
</style>


<?php if (!empty($data)) { ?>
    
        <div class="modal fade " id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="background-color: #ffffff;">
                    <div class="cell p-10">
                        <img src="<?php echo $link."/img/logo-berh-colorido.png"; ?>" alt="" width="150">
                    </div>
                    <div class="modal-header" style="text-align: center;">
                        <h1 class="modal-title mx-auto" id="relatorioModalLabel">Relatorio Processamento</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-container mb-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data Geração Pedido</th>
                                        <th>Pedido</th>
                                        <th>CNPJ CLIENTE</th>
                                        <th>Razão Social</th>
                                        <th>Status Pedido</th>
                                        <th>Nome</th>
                                        <th>Matrícula</th>
                                        <th>CPF</th>
                                        <th>Cartão</th>
                                        <th>Dias Úteis</th>
                                        <th>Id(Código Operadora)</th>
                                        <th>Operadora</th>
                                        <th>Id(Código do Benefício / ítem)</th>
                                        <th>VlUnit</th>
                                        <th>Qtde do Benefício por Dia</th>
                                        <th>Total</th>
                                        <th>Repasse</th>
                                        <th>Taxa ADM</th>
                                        <th>Status Operadora</th>
                                        <th>Economia</th>
                                        <th>Compra Operadora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data as $index => $item) { ?>
                                    <tr>
                                        <td><?php echo $item['Order']['created']; ?></td>
                                        <td><?php echo $item['Order']['id']; ?></td>
                                        <td><?php echo $item['Customer']['documento']; ?></td>
                                        <td><?php echo $item['Customer']['nome_primario']; ?></td>
                                        <td><?php echo $item['Status']['name']; ?></td>
                                        <td><?php echo $item['CustomerUser']['name']; ?></td>
                                        <td><?php echo $item['CustomerUser']['matricula']; ?></td>
                                        <td><?php echo $item['CustomerUser']['cpf']; ?></td>
                                        <td></td>
                                        <td><?php echo $item['OrderItem']['working_days']; ?></td>
                                        <td><?php echo $item['Supplier']['id']; ?></td>
                                        <td><?php echo $item['Supplier']['nome_fantasia']; ?></td>
                                        <td><?php echo $item['Benefit']['code']; ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['unit_price']; ?></td>
                                        <td><?php echo $item['CustomerUserItinerary']['quantity']; ?></td>
                                        <td><?php echo $item['OrderItem']['total']; ?></td>
                                        <td><?php echo $item['OrderItem']['transfer_fee']; ?></td>
                                        <td><?php echo $item['OrderItem']['commission_fee']; ?></td>
                                        <td><?php echo $item['OrderItem']['status_processamento']; ?></td>
                                        <td><?php echo $item['OrderItem']['saldo']; ?></td>
                                        <td><?php echo $item['OrderItem']['pedido_operadora']; ?></td>
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

