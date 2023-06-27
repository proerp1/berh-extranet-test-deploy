<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Usuário</th>
                        <th>Quantidade de Clientes</th>
                        <th style="width: 15%">Valor Cobrado</th>
                        <th>Total de Cobranças Realizadas</th>
                        <th>Total Cobrado</th>
                        <th>Total Recebido</th>
                        <th class="w-150px min-w-150px rounded-end">Ultimo histórico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $valor_total = 0; 
                        $qtde_total_exito = 0;
                        $valor_total_exito = 0;
                        $valor_total_pago = 0;
                        $qtde_total = 0;
                    ?>
                    <?php foreach ($data['QtdeUsuarios'] as $user): ?>
                        <?php
                            $valor_linha = !empty($valor_cobrado[$user['user_id']]) ? $valor_cobrado[$user['user_id']][0][0]['total'] : 0;
                            $valor_total += $valor_linha;
                            
                            $qtde_total_exito += $exito[$user['user_id']][0][0]["qtde"];
                            $valor_total_exito += $exito[$user['user_id']][0][0]["valor_total"];
                            $valor_total_pago += $exito[$user['user_id']][0][0]["valor_total_pago"];
                            $qtde_total += $user['QtdeUsuarios'][0]['total_clientes'];
                        ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4"><?php echo $user['User']['name'] ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $user['QtdeUsuarios'][0]['total_clientes']; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_linha, 2, ',','.') ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $exito[$user['user_id']][0][0]["qtde"]; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($exito[$user['user_id']][0][0]["valor_total"], 2,',','.'); ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($exito[$user['user_id']][0][0]["valor_total_pago"], 2,',','.'); ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($exito[$user['user_id']][0][0]["ultimo_registro"])); ?></td>
                        </tr>
                    <?php endforeach ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">Total:</td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $qtde_total; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_total, 2,',','.') ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $qtde_total_exito; ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_total_exito, 2,',','.'); ?></td>
                            <td class="fw-bold fs-7 ps-4">R$ <?php echo number_format($valor_total_pago, 2,',','.'); ?></td>
                            <td class="fw-bold fs-7 ps-4"></td>
                        </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base.'/cobrancas/divisao_cobradores'; ?>" class="btn btn-light-dark">Voltar</a>
                <a href="<?php echo $this->here.'/?exportar'; ?>" class="btn btn-primary"><i class="fas fa-file-excel"></i> Exportar</a>
                <a href="<?php echo $this->base.'/cobrancas/excel_clientes_cobrados/'.$id; ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar Detalhes dos Clientes</a>
                <a href="<?php echo $this->base.'/cobrancas/excel_clientes_exito/'.$id; ?>" class="btn btn-warning"><i class="fas fa-file-excel"></i> Exportar Cobranças Realizadas</a>
            </div>
        </div>  
    </div>
</div>