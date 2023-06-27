<?php echo $this->element("abas_customers", array('id' => $customer_id)); ?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body py-7">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <td colspan="10" class="ps-4 rounded-top"><h4>Logons</h4></td>
                    </tr>
                    <tr class="fw-bolder text-muted bg-light">
                        <td class="ps-4 rounded-bottom">Produto</td>
                        <td>Logon</td>
                        <td class="rounded-bottom">Consultas Realizadas</td>
                    </tr>

                    <?php if ($logons) { ?>
                        <?php foreach ($logons as $logon) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $logon['Product']['name']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $logon['NegativacaoLogon']['logon']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $logon['NegativacaoLogon']['qtde']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </head>
            </table>
        </div>
        <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base.'/customers/mensalidade/'.$customer_id; ?>" class="btn btn-light-dark">Voltar</a>
        </div>
    </div>
</div>