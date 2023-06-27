<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>
<div class="row gy-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-success h-75px w-75px">
                    <i class="fas fa-users fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo $total_clientes ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">CLIENTES</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_mensal[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor Mensal R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_manutencao[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor Manutenção PEFIN R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_serasa[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor SERASA R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_pefin[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor PEFIN R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_hipercheck[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor BeRH R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_desconto[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor Desconto R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_total-$valor_desconto[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor Total R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url([ "controller" => "billings", "action" => "mensalidade", $id]); ?>/" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    
                    <?php if ($faturamento['Billing']['conta_gerada'] == 0) { ?>
                        <a href="javascript:" onclick="confirm('<h3>Deseja mesmo gerar a conta a receber?</h3>', '<?php echo $this->base.'/billings/gerar_contas_receber/'.$id ?>')" class="btn btn-primary">
                            Gerar contas a receber
                        </a>
                    <?php } elseif ($qtde_email_restante > 0) { ?>
                        <!-- <div class="form-group">
                            <a href="<?php echo '#'//$this->base.'/billings/enviar_email_boleto/'.$id ?>" class="btn btn-primary">
                                <i class="fa fa-send"></i>
                                Enviar email de cobrança - restam <?php echo $qtde_email_restante ?>
                            </a>
                        </div> -->
                    <?php } ?>

                    <a href="<?php echo $this->here.'?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-primary">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Código Associado</th>
                        <th>Cliente</th>
                        <th>Valor Mensal R$</th>
                        <th>Valor Total R$</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <?php
                                $valorManutencaoPefin = 0;
                                if (!empty($data[$i]['PefinMaintenance']['value_nao_formatado'])) {
                                    $valorManutencaoPefin = $data[$i]['PefinMaintenance']['value_nao_formatado'];
                                }

                                $total += $data[$i]['BillingMonthlyPayment']['monthly_value_total']+$valorManutencaoPefin;

                                $total_sem_desconto = $data[$i]['BillingMonthlyPayment']['monthly_value_total']+$valorManutencaoPefin;
                                $total_com_desconto = $total_sem_desconto - (($data[$i]['BillingMonthlyPayment']['desconto']/100)*$total_sem_desconto);
                            ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['BillingMonthlyPayment']['monthly_value_formatado'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($total_com_desconto, 2, ',', '.') ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/billings/demonstrativo/'.$data[$i]["Billing"]["id"].'/'.$data[$i]["Customer"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Demonstrativo
                                    </a>
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
        <?php echo $this->element("pagination"); ?>
    </div>
</div>