<?php $url_novo = $this->base."/retorno_cnabs/baixar_contas/".$id; ?>
<?php $url_excel = $this->base."/retorno_cnabs/gerar_excel/".$id; ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "retorno_cnabs", "action" => "detalhes", $id)); ?>/" role="form" id="busca" autocomplete="off">
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
                    <a href="<?php echo $url_excel;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Gerar excel
                    </a>
                    
                    <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">
                        <i class="fas fa-cog"></i>
                        Processar contas
                    </a>
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <p>
                    Baixando contas com a data 
                    <?php
                        if ($retorno['RetornoCnab']['data_pagamento'] != null) {
                            echo $retorno['RetornoCnab']['data_pagamento'];
                        } else {
                            echo "padrão do arquivo";
                        }
                    ?>
                </p>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Processado</th>
                        <th>Encontrado</th>
                        <th>Código movimento</th>
                        <th>Status do cliente</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Vencimento</th>
                        <th>Valor pago</th>
                        <th>Valor liquido</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo $data[$i]["TmpRetornoCnab"]["processado"] == 1 ? '<label class="badge badge-success">Sim</label>' : '<label class="badge badge-danger">Não</label>'; ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php echo $data[$i]["TmpRetornoCnab"]["encontrado"] == 1 ? '<label class="badge badge-success">Sim</label>' : '<label class="badge badge-danger">Não</label>'; ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["TmpRetornoCnab"]["cod_ocorrencia"].' - '.$data[$i]["TmpRetornoCnab"]["ocorrencia"] ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($data[$i]["TmpRetornoCnab"]["encontrado"] == 1){ ?>
                                        <label class="badge <?php echo $data[$i]["Income"]["Customer"]['Status']['label'] ?>">
                                            <?php echo $data[$i]["Income"]["Customer"]['Status']['name'] ?>
                                        </label>
                                    <?php } ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php
                                        if ($data[$i]["TmpRetornoCnab"]["encontrado"] == 1) {
                                            echo "<a target='_blank' href='".$this->Html->url(['controller' => 'customers', 'action' => 'edit', $data[$i]["Income"]["Customer"]['id']])."'>".$data[$i]["Income"]["Customer"]['codigo_associado'].' - '.$data[$i]["Income"]["Customer"]['nome_primario']."</a>";
                                        } else {
                                            echo "<i>Conta sem vínculo no sistema</i>";
                                        }
                                    ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["TmpRetornoCnab"]["nosso_numero"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y', strtotime($data[$i]["TmpRetornoCnab"]["vencimento"])); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]["TmpRetornoCnab"]["valor_pago"],2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i]["TmpRetornoCnab"]["valor_liquido"],2,',','.'); ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if ($data[$i]["Income"]["data_cancel"] == '1901-01-01 00:00:00') { ?>
                                        <?php if ($data[$i]["TmpRetornoCnab"]["encontrado"] == 1) { ?>
                                            <a href="<?php echo $this->base.'/incomes/edit/'.$data[$i]["TmpRetornoCnab"]["income_id"]; ?>" class="btn btn-info btn-sm">
                                                Detalhes
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo $this->base.'/incomes/add_retorno/'.$id.'/'.$data[$i]["TmpRetornoCnab"]["id"] ?>" class="btn btn-success btn-sm">
                                                Cadastrar
                                            </a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php echo $data[$i]["Income"]["data_cancel"] != '' ? 'Conta excluída '.date('d/m/Y H:i:s', strtotime($data[$i]["Income"]["data_cancel"])) : '' ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="fw-bold fs-7 ps-4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>