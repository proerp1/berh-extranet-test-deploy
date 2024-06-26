
<div class="modal fade <?php //echo $index > 0 ? 'page-break' : ''; ?>" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <tr>
                                    <td><?php echo 'de ' . h($get_de) . ' até ' . h($get_ate); ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">Nenhum dado encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-body">
                <div class="table-container mb-4">
                    <h3>Informações do Pedido</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Status do Pedido</th>
                                <th>Repasse</th>
                                <th>Subtotal</th>
                                <th>Taxa</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($data)) { ?>
                            <?php foreach ($data as $pedido) { ?>
                            <tr>  
                                <td><?php echo h($pedido["Customer"]["nome_primario"]); ?></td>
                                <td><?php echo h($pedido["Status"]["name"]); ?></td>
                                <td><?php echo 'R$' . h($pedido["Order"]["transfer_fee"]); ?></td>
                                <td><?php echo 'R$' . h($pedido["Order"]["subtotal"]); ?></td>
                                <td><?php echo 'R$' . h($pedido["Order"]["commission_fee"]); ?></td>
                            </tr>
                            <?php } ?>
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
