<?php echo $this->element("abas_users"); ?>

<div class="card mb-5 mb-xl-8">
    <?php echo $this->Form->create('UserResale', ["id" => "js-form-submit", "action" => "add/".$id, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <div class="card-header d-block border-0 pt-6 pb-6">
            <div class="row">
                <div class="col-3 mb-10">
                    <label class="form-label fs-5 fw-bold mb-3">Parceiros:</label>
                    <?php echo $this->Form->input('resale_id', ["required" => true, "placeholder" => "", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => $resaleIds]);  ?>
                </div>
                <div class="col-12 mb-7">
                    <div class="col-sm-offset-2 col-sm-9">
                        <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 rounded-start">Parceiro</th>
                        <th class="w-150px min-w-150px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Resale"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/user_resales/delete/'.$data[$i]["UserResale"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="2">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>