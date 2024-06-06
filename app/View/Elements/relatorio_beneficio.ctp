<?php if (!empty($itens)) { ?>
    <?php foreach ($itens as $item) { ?>
<div class="modal fade" id="relatorioModal" tabindex="-1" aria-labelledby="relatorioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="background-color: #ffffff;">
        <div class="cell p-10">
            <img src="<?php echo $link."/img/logo-berh-colorido.png" ?>" alt="" width="150">
        </div>
            <div class="modal-header" style="text-align: center;">
                <h1 class="modal-title mx-auto" id="relatorioModalLabel">Relatório de Entrega de Benefícios</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="content text-center mb-4">
                    <h2>Detalhes do Pedido</h2>
                </div>
                <div class="table-container mb-4">
                    <div class="table">
                        <div class="row">
                            <div class="cell fw-bold">Pedido: <?php echo $order['Order']['id'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Razão Social: <?php echo $order['Customer']['nome_secundario'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">CNPJ: <?php echo $order['Customer']['documento'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Período de Utilização: <?php echo $order['Order']['order_period_from']; ?> a <?php echo $order['Order']['order_period_to']; ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Matrícula: <?php echo $item['CustomerUser']['matricula'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Dpto:</div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Nome: <?php echo $item['CustomerUser']['nome'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">CPF: <?php echo $item['CustomerUser']['cpf'] ?></div>
                        </div>
                        <!--
                        <div class="row">
                            <div class="cell fw-bold">Cargo: <?php echo $item[0]['cargo'] ?></div>
                        </div>
                        -->
                        <div class="row">
                            <div class="cell fw-bold">Código: <?php echo $item['CustomerUserItinerary']['benefit_id']; ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Descrição: <?php echo $item['CustomerUserItinerary']['benefit_name']; ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Quantidade: <?php echo $item[0]['qtd'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Dia: <?php echo $item[0]['working_days'] ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Total:</div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Valor Unitário: <?php echo $item['CustomerUserItinerary']['unit_price']; ?></div>
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Valor Total: <?php echo $item[0]['valor'] ?></div>      
                        </div>
                        <div class="row">
                            <div class="cell fw-bold">Valor Total Recebido: <?php echo $item[0]['total'] ?></div>               
                        </div>
                    </div>
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
<?php   }
    }
?>

<style>
    .table {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }
    .cell {
        flex: 1;
    }
    .cell.fw-bold {
        font-weight: bold;
    }
    .table .row:nth-child(odd) {
        background-color: #f9f9f9;
    }
</style>