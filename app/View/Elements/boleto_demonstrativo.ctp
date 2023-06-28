<?php 
    $faturamento_inicio = $faturamento['Billing']['date_billing'];
    $faturamento_fim = date("t/m/Y", strtotime(str_replace('/', '-', $faturamento['Billing']['date_billing'])));
?>
<html>
    <head>
        <title>BOLETO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="UTF-8">
        <style type="text/css">
            <!--.cp {  font: bold 9px Arial; color: black}
                <!--.ti {  font: 8px Arial, Helvetica, sans-serif}
                <!--.ld { font: bold 10px Arial; color: #000000}
                <!--.ct { FONT: 8px "Arial Narrow"; COLOR: #000033}
                <!--.cn { FONT: 8px Arial; COLOR: black }
                <!--.bc { font: bold 20px Arial; color: #000000 }
                <!--.ld2 { font: bold 12px Arial; color: #000000 }
                .style1 {font-size: 12px}
                .style2 {font-size: 9px}
                .style4 {font-size: 12px; font-weight: bold; border-style: solid; border-width: 1px }
                -->
            @page {
            margin-left: 10mm;
            margin-right: 10mm;
            margin-top: 0;
            }
        </style>
    </head>
    <body topmargin="0" rightmargin="0" bgcolor="#ffffff" text="#000000">
        <table border="0" width="666">
            <tr>
                <td width="666" rowspan="3" valign="middle"><img src="<?php echo APP ?>webroot/img/logo-berh-principal-alta.png" width="145"></td>
            </tr>
            <tr width="666">
                <td width="300">
                  <p align="center" class="style2">&nbsp;</p>
                  <p align="center" class="style2">&nbsp;</p>
                  <p align="center" class="style2">&nbsp;</p>
                </td>
            </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" width="666" style="border-width:0px; ">
            <tbody>
                <tr>
                    <td class="cp" colspan="6" bgcolor="#CCCCCC">
                        <p align="center" class="style1"><font face="Tahoma">Demonstrativo</font></p>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="6">
                        <table border="0" width="100%" bordercolorlight="#333333" height="320">
                            <tr>
                                            <td colspan="7" class="style4">Razão Social :<?php echo $faturamento_cliente['Customer']['nome_primario'].' - '.$faturamento_cliente['Customer']['nome_secundario']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="style4">Período : <?php echo $faturamento_inicio." até ".$faturamento_fim; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="style4">Minimo Consultas : <?php echo $faturamento_cliente['BillingMonthlyPayment']['quantity']; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="style4">Produtos</td>
                                        </tr>
                                        <tr>
                                            <td class="style4">Nome</td>
                                            <td class="style4">Tipo da consulta</td>
                                            <td class="style4" colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">Consultas Realizadas</td>
                                            <?php if ($tipo == 1): ?>
                                                <td class="style4">Consultas Faturadas</td>
                                            <?php endif ?>
                                            <td class="style4">Valor Unitário</td>
                                            <td class="style4">Valor total</td>
                                            <td class="style4">Total</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="style4">Mensalidade</td>
                                            <td colspan="1" class="style4">R$ <?php echo $faturamento_cliente['BillingMonthlyPayment']['monthly_value']; ?></td>
                                        </tr>
                                        <?php $total = 0; ?>
                                        <?php if ($negativacao) { ?>
                                            <?php for ($i=0; $i < count($negativacao); $i++) { ?>
                                            <?php $total += $negativacao[$i]['n']['valor_total']; ?>
                                                <tr>
                                                    <td colspan="1" class="style4"><?php echo $negativacao[$i]['p']['name']; ?></td>
                                                    <td colspan="1" class="style4">
                                                        <?php
                                                            if ($negativacao[$i]['n']['type'] == 1) {
                                                                echo 'Quantidade';
                                                            } else if ($negativacao[$i]['n']['type'] == 2) {
                                                                echo 'Consumo';
                                                            } else if ($negativacao[$i]['n']['type'] == 3) {
                                                                echo 'Fora da composição do plano';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td colspan="<?php echo $tipo == 1 ? '1' : '2' ?>" class="style4"><?php echo $negativacao[$i]['n']['qtde_consumo']; ?></td>
                                                    <?php if ($tipo == 1): ?>
                                                        <td colspan="1" class="style4"><?php echo $negativacao[$i]['n']['qtde_excedente']; ?></td>
                                                    <?php endif ?>
                                                    <td colspan="1" class="style4">R$ <?php echo number_format($negativacao[$i]['n']['valor_unitario'],2,',','.'); ?></td>
                                                    <td colspan="1" class="style4">R$ <?php echo number_format($negativacao[$i]['n']['qtde_consumo']*$negativacao[$i]['n']['valor_unitario'],2,',','.'); ?></td>
                                                    <td colspan="1" class="style4">R$ <?php echo number_format($negativacao[$i]['n']['valor_total'],2,',','.'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else {?>
                                            <tr>
                                                <td colspan="7" class="style4">Nenhum registro encontrado</td>
                                            </tr>
                                        <?php } ?>

                                        <?php if ($pefin) { ?>
                                            <?php for ($i=0; $i < count($pefin); $i++) { ?>
                                            <?php $total += $pefin[$i]['n']['valor_total']; ?>
                                                <tr>
                                                    <td class="style4" colspan="1"><?php echo $pefin[$i]['p']['name']; ?></td>
                                                    <td class="style4"></td>
                                                    <td class="style4" colspan="<?php echo $tipo == 1 ? '1' : '2' ?>"><?php echo $pefin[$i]['n']['qtde_realizado']; ?></td>
                                                    <?php if ($tipo == 1): ?>
                                                        <td class="style4" colspan="1"><?php echo $pefin[$i]['n']['qtde_excedente']; ?></td>
                                                    <?php endif ?>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($pefin[$i]['n']['valor_unitario'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($pefin[$i]['n']['qtde_realizado']*$pefin[$i]['n']['valor_unitario'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($pefin[$i]['n']['valor_total'],2,',','.'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($berh) { ?>
                                            <?php for ($i=0; $i < count($berh); $i++) { ?>
                                            <?php $total += $berh[$i]['BillingNovaVida']['valor_total']; ?>
                                                <tr>
                                                    <td class="style4" colspan="1"><?php echo $berh[$i]['Product']['name']; ?></td>
                                                    <td class="style4"></td>
                                                    <td class="style4" colspan="<?php echo $tipo == 1 ? '1' : '2' ?>"><?php echo $berh[$i]['BillingNovaVida']['quantidade']; ?></td>
                                                    <?php if ($tipo == 1): ?>
                                                        <td class="style4" colspan="1"><?php echo $berh[$i]['BillingNovaVida']['quantidade_cobrada']; ?></td>
                                                    <?php endif ?>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($berh[$i]['BillingNovaVida']['valor_unitario'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($berh[$i]['BillingNovaVida']['quantidade_cobrada']*$berh[$i]['BillingNovaVida']['valor_unitario'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($berh[$i]['BillingNovaVida']['valor_total'],2,',','.'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if (!empty($meproteja)) { ?>
                                            <?php for ($i=0; $i < count($meproteja); $i++) { ?>
                                            <?php $total += $meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor']; ?>
                                                <tr>
                                                    <td class="style4" colspan="1"><?php echo $meproteja[$i]['Product']['name']; ?></td>
                                                    <td class="style4">Me proteja</td>
                                                    <td class="style4" colspan="<?php echo $tipo == 1 ? '1' : '2' ?>">1</td>
                                                    <?php if ($tipo == 1): ?>
                                                        <td class="style4" colspan="1">1</td>
                                                    <?php endif ?>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'],2,',','.'); ?></td>
                                                    <td class="style4" colspan="1">R$ <?php echo number_format($meproteja[$i]['ClienteMeProteja']['clienteMeProtejaValor'],2,',','.'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php $manutencao = 0; ?>
                                        <?php if ($faturamento_cliente['PefinMaintenance']['id'] != null): ?>
                                            <?php $manutencao = $faturamento_cliente['PefinMaintenance']['value']; ?>
                                            <tr>
                                                <td class="style4" colspan="6">Manutenção PEFIN:</td>
                                                <td class="style4" colspan="1">R$ <?php echo number_format($faturamento_cliente['PefinMaintenance']['value'],2,',','.'); ?></td>
                                            </tr>
                                        <?php endif ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" valign="top">
                        <table border="0" width="100%">
                            <tr>
                                <td align="right">
                                    <table border=0>
                                        <tr>
                                            <td width="188" class="style4">
                                                <span class="style2"><font face="Tahoma">Total Excedente:</font></span>
                                            </td>
                                            <td><font face="Tahoma" size="1">R$ <?php echo number_format($total, 2, ',', '.'); ?></font></td>
                                        </tr>
                                        <?php 
                                            $total_sem_desconto = $faturamento_cliente['BillingMonthlyPayment']['monthly_value']+$total+$manutencao;
                                            $valor_descontar = ($faturamento_cliente['BillingMonthlyPayment']['desconto']/100)*$total_sem_desconto;
                                        ?>
                                        <?php if ($faturamento_cliente['BillingMonthlyPayment']['desconto'] > 0){ ?>
                                            <tr>
                                                <td width="188" class="style4">
                                                    <span class="style2"><font face="Tahoma">Total sem desconto:</font></span>
                                                </td>
                                                <td><font face="Tahoma" size="1">R$ <?php echo number_format($total_sem_desconto, 2, ',', '.'); ?></font></td>
                                            </tr>
                                            <tr>
                                                <td width="188" class="style4">
                                                    <span class="style2"><font face="Tahoma">Desconto:</font></span>
                                                </td>
                                                <td><font face="Tahoma" size="1">
                                                    <p>- <?php echo number_format($faturamento_cliente['BillingMonthlyPayment']['desconto'], 2, '.', ''); ?>%</p>
                                                    <p>- R$<?php echo number_format($valor_descontar,2,',','.'); ?></p>
                                                </font></td>
                                            </tr>
                                        <?php } ?>
                                        <?php
                                            $total_com_desconto = $total_sem_desconto - $valor_descontar;
                                        ?>
                                        <tr>
                                            <td width="188" class="style4">
                                                <span class="style2"><font face="Tahoma">Total Fatura:</font></span>
                                            </td>
                                            <td><font face="Tahoma" size="1"><b>R$ <?php echo number_format($total_com_desconto, 2, ',', '.'); ?></b></font></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>