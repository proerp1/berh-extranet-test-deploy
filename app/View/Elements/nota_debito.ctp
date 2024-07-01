<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nota de Débito</title>
    <style>
        .page {
/*            padding: 10px 20px 10px 25px*/
        }

        .cell {
            border: 1px solid #000
        }

        .cell + .cell {
            border-top: none;
        }

        .p-10 {
            padding: 10px
        }

        .pl-10 {
            padding-left: 10px
        }

        .pb-10 {
            padding-bottom: 10px
        }

        .pt-10 {
            padding-top: 10px
        }

        .pr-10 {
            padding-right: 10px
        }

        .p-20 {
            padding: 20px
        }

        .pl-20 {
            padding-left: 20px
        }

        .pb-20 {
            padding-bottom: 20px
        }

        .pt-20 {
            padding-top: 20px
        }

        .pr-20 {
            padding-right: 20px
        }

        .p-30 {
            padding: 30px
        }

        .pl-30 {
            padding-left: 30px
        }

        .pb-30 {
            padding-bottom: 30px
        }

        .pt-30 {
            padding-top: 30px
        }

        .pr-30 {
            padding-right: 20px
        }

        .p-40 {
            padding: 40px
        }

        .pl-40 {
            padding-left: 40px
        }

        .pb-40 {
            padding-bottom: 40px
        }

        .pt-40 {
            padding-top: 40px
        }

        .pr-40 {
            padding-right: 40px
        }

        .p-50 {
            padding: 50px
        }

        .pl-50 {
            padding-left: 50px
        }

        .pb-50 {
            padding-bottom: 50px
        }

        .pt-50 {
            padding-top: 50px
        }

        .pr-50 {
            padding-right: 50px;
        }

        .d-flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .flex-row {
            flex-direction: row;
        }

        .align-center {
            align-items: center;
        }

        .align-bottom {
            align-items: baseline;
        }

        .justify-center {
            justify-content: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-end {
            justify-content: flex-end;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .items-table {
            border-spacing: 0;
            width: 100%;
        }

        .items-table th, .items-table td {
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .items-table th:last-child, .items-table td:last-child {
            border-right: 0;
        }

        .items-table tr:last-child th, .items-table tr:last-child td {
            border-bottom: 0;
        }

        .items-table th, .items-table td {
            text-align: left;
            padding-left: 10px;
        }

        .items-table th:nth-child(3), .items-table td:nth-child(3) {
            text-align: center;
            padding-left: 0;
        }

        .bold {
            font-weight: bold
        }

        .m-0 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="cell p-10">
            <img src="<?php echo $link."/img/logo-berh-colorido.png" ?>" alt="" width="150">
        </div>
        <div class="cell text-right">
            <p class="pr-50 bold m-0">Nº <?php echo $order['Order']['id'] ?></p>
        </div>
        <div class="cell p-10 d-flex flex-col">
            <table>
                <tr>
                    <td class="pb-10" colspan="3"><p class="m-0 bold">Dados da Prestação de Serviços:</p></td>
                </tr>
                <tr>
                    <td class="pb-10" width="25%"><b>ND Nº <?php echo $order['Order']['id'] ?></b></td>
                    <td class="pb-10" width="55%"><b>Série: 3</b></td>
                    <td class="pb-10" style="text-align: end"><b>Emitido em: <?php echo date('d/m/Y') ?></b></td>
                </tr>
                <tr>
                    <td width="25%"><b>Valor dos serviços:</b></td>
                    <td width="55%"><b>R$ <?php echo $order['Order']['total'] ?></b></td>
                </tr>
            </table>
        </div>
        <div class="cell text-center">
            <b>PRESTADOR DE SERVIÇOS:</b>
        </div>
        <div class="cell p-10 d-flex flex-col">
            <table>
                <tr>
                    <td width="25%"><b>Razão Social/Nome:</b></td>
                    <td><b>BE RH BENEFICIOS LTDA</b></td>
                </tr>
                <tr>
                    <td width="25%"><b>CNPJ/CPF:</b></td>
                    <td><b>48.503.984/0001-50</b></td>
                </tr>
                <tr>
                    <td width="25%"><b>Endereço:</b></td>
                    <td><b> Av. Marquês de São Vicente, 446 - Várzea da Barra Funda, - São Paulo/SP</b></td>
                </tr>
                <tr>
                    <td width="25%"><b>CEP:</b></td>
                    <td><b>01139-000</b></td>
                </tr>
                <tr>
                    <td width="25%"><b>Telefone:</b></td>
                    <td><b>(11) 5043-0544</b></td>
                </tr>
                <tr>
                    <td width="25%"><b>Email:</b></td>
                    <td><b>nfe@berh.com.br ; faturamento@berh.com.br</b></td>
                </tr>
            </table>
        </div>
        <div class="cell text-center">
            <b>TOMADOR DE SERVIÇOS:</b>
        </div>
        <?php if ($order['Order']['economic_group_id'] == null) { ?>
            <div class="cell p-10 d-flex flex-col">
                <table>
                    <tr>
                        <td width="25%"><b>Razão Social/Nome:</b></td>
                        <td><b><?php echo $order['Customer']['nome_primario'] ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>CNPJ/CPF:</b></td>
                        <td><b><?php echo $order['Customer']['documento'] ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>Endereço:</b></td>
                        <td><b><?php echo "{$order['Customer']['endereco']}, {$order['Customer']['numero']} - {$order['Customer']['bairro']} - {$order['Customer']['cidade']}/{$order['Customer']['estado']}" ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>Fone:</b></td>
                        <td><b><?php echo $order['Customer']['telefone1'] ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>Email:</b></td>
                        <td><b><?php echo $order['Customer']['email'] ?></b></td>
                    </tr>
                </table>
            </div>
        <?php } else { ?>
            <div class="cell p-10 d-flex flex-col">
                <table>
                    <tr>
                        <td width="25%"><b>Razão Social/Nome:</b></td>
                        <td><b><?php echo $order['EconomicGroup']['razao_social'] ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>CNPJ/CPF:</b></td>
                        <td><b><?php echo $order['EconomicGroup']['document'] ?></b></td>
                    </tr>
                    <tr>
                        <td width="25%"><b>Endereço:</b></td>
                        <td><b><?php echo "{$order['EconomicGroup']['endereco']}, {$order['EconomicGroup']['numero']} - {$order['EconomicGroup']['bairro']} - {$order['EconomicGroup']['cidade']}/{$order['EconomicGroup']['estado']}" ?></b></td>
                    </tr>
                </table>
            </div>
        <?php } ?>
        
        <div class="cell p-10">
            <p class="m-0">ATIVIDADE ADMINISTRACAO E GERENCIAMENTO DAS AQUISICOES DE BENEFICIOS P/ TERCEIROS EM SISTEMA CONVENCIONAL E OU ELETRONICO POR MEIO MAGNETICO OU SIMILAR</p>
        </div>
        <div class="cell text-center">
            <b>DESCRIÇÃO DO DEMONSTRATIVO DE BENEFÍCIOS</b>
        </div>
        <div class="cell">
            <p class="m-0 p-10">REEMBOLSO DE PAGAMENTO REFERENTE A AQUISIÇÃO DE BENEFÍCIOS DE VALES- TRANSPORTES, VALES-REFEIÇÃO, VALES-ALIMENTAÇÃO E SIMILARES PARA TERCEIROS. VALORES RELACIONADOS COM TAXA PELA PRESTAÇÃO DE SERVIÇO SERÃO RECONHECIDOS ATRAVÉZ DE NOTA FISCAL SERVIÇOS. </p>
        </div>
        <div class="cell text-center">
            <b>PEDIDO(S):</b>
        </div>
        <div class="cell">
            <p class="pl-20 bold m-0"><?php echo $order['Order']['id'] ?></p>
        </div>
        <div class="cell text-center">
            <b>OUTRAS INFORMAÇÕES</b>
        </div>
        <div class="cell text-center">
            <table class="items-table">
                <tr>
                    <th>Benefício</th>
                    <th style="text-align: center;">Quantidade</th>
                    <th style="text-align: center;">Valor Itens</th>
                </tr>
                <?php if (!empty($itens)) { ?>
                    <?php foreach ($itens as $item) { ?>
                        <tr>
                            <td><?php echo $item['CustomerUserItinerary']['benefit_name'] ?></td>
                            <td style="text-align: center;"><?php echo $item[0]['qtd'] ?></td>
                            <td style="text-align: center;">R$ <?php echo number_format($item[0]['valor'],2,',','.') ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <th colspan="2" style="text-align: right;" class="pr-10">VALOR TOTAL ITENS (A)</th>
                    <td style="text-align: center;">R$ <?php echo $order['Order']['subtotal']; ?></td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right;" class="pr-10">VALOR TOTAL REPASSE OPERADORA (B)</th>
                    <td style="text-align: center;">R$ <?php echo $order['Order']['transfer_fee']; ?></td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right;" class="pr-10">DESCONTO (C)</th>
                    <td style="text-align: center;">R$ <?php echo $order['Order']['desconto']; ?></td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right;" class="pr-10">ACRÉSCIMO (D)</th>
                    <td style="text-align: center;">R$ 0,00</td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: right;" class="pr-10">TOTAL GERAL (A + B - C + D)</th>
                    <td style="text-align: center;">R$ <?php echo number_format(($order['Order']['subtotal_not_formated'] + $order['Order']['transfer_fee_not_formated'] - $order['Order']['desconto_not_formated']), 2, ',', '.') ?></td>
                    </tr>
            </table>
        </div>
        <?php if ($order['Order']['observation'] != '') { ?>
            <div class="cell text-center">
                <b>DESCRIÇÃO DO DEMONSTRATIVO DE BENEFÍCIOS</b>
            </div>
            <div class="cell">
                <p class="m-0 p-10"><?php echo $order['Order']['observation'] ?></p>
            </div>
        <?php } ?>
    </div>
</body>
</html>
