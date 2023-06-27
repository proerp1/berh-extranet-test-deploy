<?php $url_novo = $this->base."/emails_campanhas/add/"; ?>
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Nova campanha</a>
            </div>
        </div>
    </div>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Data de criação</th>
                        <th>Data de envio</th>
                        <th>Assunto</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados) { ?>
                        <?php for ($i=0; $i < count($dados); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <?php if($dados[$i]['EmailsCampanha']['send'] && $dados[$i]['EmailsCampanha']['processing']){ ?>
                                        <label class="label label-default">
                                            Enviando...
                                        </label>
                                    <?php } elseif($dados[$i]['EmailsCampanha']['send'] && $dados[$i]['EmailsCampanha']['processing'] == false){ ?>
                                        <label class="label label-success">
                                            Enviado
                                        </label>
                                    <?php } else { ?>
                                        <label class="label label-warning">
                                            Pendente de envio
                                        </label>
                                    <?php } ?>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['EmailsCampanha']['created'] != '' ? date('d/m/Y H:i:s', strtotime($dados[$i]['EmailsCampanha']['created'])) : '' ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['EmailsCampanha']['send_data'] != '0000-00-00 00:00:00' ? date('d/m/Y H:i:s', strtotime($dados[$i]['EmailsCampanha']['send_data'])) : '' ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]['EmailsCampanha']['subject'] ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/emails_campanhas/edit/'.$dados[$i]["EmailsCampanha"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="3">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>