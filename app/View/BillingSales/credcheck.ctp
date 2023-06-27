<?php $url_novo = $this->base."/billing_sales/faturar_hipercheck/".$id;  ?>
<?php
    echo $this->element("aba_faturamento_vendas_revenda", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
        </div>
        <div class="card-toolbar">
            <a href="<?php echo $this->here.'?excel' ?>" class="btn btn-success" type="button">
                <i class="fas fa-file-excel"></i>
                Exportar
            </a>
        </div>
    </div>
    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Vendedor</th>
                        <th>Quantidade de planos</th>
                        <th>Comissão</th>
                        <th>Valor a pagar</th>
                        <th>Valor pago</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($previsao) { ?>
                        <?php for ($i=0; $i < count($previsao); $i++) { ?>
                            <?php
                                $valor_pagar = $previsao[$i][0]["valor_comissao"];

                                if (!empty($realizado[$i][0])) {
                                    $valor_pago = $realizado[$i][0]["valor_comissao"];
                                } else {
                                    $valor_pago = 0;
                                }

                                $novas_contas = false;
                                if ($valor_pagar > $valor_pago) {
                                    $novas_contas = true;
                                }

                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo $previsao[$i]["s"]["nome_fantasia"] ?>
                                    <?php echo $novas_contas ? '&nbsp;<span class="badge badge-circle badge-warning"><i class="fa fa-exclamation text-white"></i></span>' : '' ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $previsao[$i][0]["qtde"] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($previsao[$i]['p']["commission"],2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_pagar,2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_pago,2,',','.') ?></td>
                                <td class="fw-bold fs-7 ps-4"> 
                                    <?php if ($billing_sale['BillingSale']['faturado_hipercheck'] == 1) { ?>
                                        <a href="<?php echo $this->base.'/billing_sales/detalhes_hipercheck/'.$id.'/'.$previsao[$i]["s"]["id"]; ?>" class="btn btn-info btn-sm">Detalhes</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="6">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>