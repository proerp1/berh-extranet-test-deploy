<?php
    if (isset($id)) {
        echo $this->element("abas_emails", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "emails_campanhas", "action" => "view_emails", $id)); ?>" role="form" id="busca" autocomplete="off">
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
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <h4><?php echo count($data) ?> cliente(s)</h4>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Código</th>
                        <th>Nome Fantasia</th>
                        <th>Contato</th>
                        <th class="rounded-end">Email</th>
                        <?php if (!$processing && !$send) { ?>
                            <th class="w-150px min-w-150px rounded-end">Ações</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr <?php echo !$data[$i]['MailList']['sent'] ? 'style="color:red"' : '' ?>>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['contato'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo strtolower($data[$i]['Customer']['email']) ?>
                                    <?php echo($data[$i]['Customer']['email1'] != '' ? ', '.strtolower($data[$i]['Customer']['email1']) : ''); ?>
                                </td>
                                <?php if (!$processing && !$send) { ?>
                                    <td class="fw-bold fs-7 ps-4">
                                        <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/emails_campanhas/delete_email/'.$id.'/'.$data[$i]["MailList"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                            Excluir
                                        </a>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="5">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>