<?php $url_novo = $this->base."/negativacao/add/"; ?>

<?php echo $this->element("abas_customers", array('id' => $id)); ?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customers", "action" => "documents", $id)); ?>" role="form" id="busca" autocomplete="off">
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 min-w-150px rounded-start">Elegível para gestão econômico</th>
                        <th>Margem de segurança</th>
                        <th>Incluir qtde. mínina diária </th>
                        <th>Tipos de GE </th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerGeLog']['flag_gestao_economico'] == 'S' ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerGeLog']['porcentagem_margem_seguranca']; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerGeLog']['qtde_minina_diaria'] == 2 ? 'Sim' : 'Não'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerGeLog']['tipo_ge'] == 1 ? 'Pré' : ($data[$i]['CustomerGeLog']['tipo_ge'] == 2 ? 'Pós' : ($data[$i]['CustomerGeLog']['tipo_ge'] == 3 ? 'Garantido' : '')) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['UsuarioCriacao']['name'] ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>
