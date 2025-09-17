<?php
    echo $this->element("abas_customers", array('id' => $id));
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customer_benefit_codes", "action" => "index", $id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <?php if ($can_bulk_edit) { ?>
                        <button type="button" class="btn btn-danger me-3" id="delete_all" disabled>Apagar Selecionados</button>
                    <?php } ?>

                    <a href="<?php echo $this->base.'/customer_benefit_codes/index/'.$id.'/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <a href="#" class="btn btn-secondary me-3" style="float:right" data-bs-toggle="modal" data-bs-target="#modal_importar_saldo">
                        <i class="fas fa-arrow-up"></i>
                        Importar (CSV)
                    </a>
                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Novo</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <?php if ($can_bulk_edit) { ?>
                            <th class="w-80px min-w-80px ps-4">
                                <input type="checkbox" class="delete_id delete_id_all" name="delete_id" value="all">
                            </th>
                        <?php } ?>
                        <th class="ps-4 rounded-start">Benefício BE</th>
                        <th class="ps-4 rounded-start">Código Beneficio BE</th>
                        <th>Código Beneficio Cliente</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <?php if ($can_bulk_edit) { ?>
                                    <td class="fw-bold fs-7 ps-4">
                                        <input type="checkbox" class="delete_id delete_id_<?php echo $data[$i]["CustomerBenefitCode"]["id"] ?>" name="delete_id" value="<?php echo $data[$i]["CustomerBenefitCode"]["id"] ?>">
                                    </td>
                                <?php } ?>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Benefit"]["name"] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerBenefitCode"]["code_be"] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerBenefitCode"]["code_customer"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/customer_benefit_codes/edit/'.$id.'/'.$data[$i]["CustomerBenefitCode"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/customer_benefit_codes/delete/'.$id.'/'.$data[$i]["CustomerBenefitCode"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="10">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal_importar_saldo" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tem certeza?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="<?php echo $this->base . '/customer_benefit_codes/upload/' . $id; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>CSV com os códigos de De/Para</p>
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info mr-auto" href="<?php echo $this->base; ?>/files/ModeloImportacaoDeParaBeneficioClientes.csv" targe="_blank" download>Baixar Modelo</a>
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Sim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const allIds = JSON.parse('<?= json_encode(array_values($allIds)) ?>')
    let deleteIdCookies = getCookie('customerBenefitCodesDeleteIds')
    let deleteIds = deleteIdCookies ? deleteIdCookies.split(',') : []
    let isAllIds = JSON.parse(getCookie('checkedAllIds'))

    $('.delete_id_all').prop('checked', isAllIds)

    $('#delete_all').attr('disabled', !deleteIds.length)
    for (const deleteId of deleteIds) {
        $(`.delete_id_${deleteId}`).prop('checked', true)
    }

    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $('.delete_id').on('change', function () {
            const isChecked = $(this).is(':checked')
            let deleteId = $(this).val()

            if (deleteId === 'all') {
                $('.delete_id').prop('checked', isChecked)
                setCookie('checkedAllIds', isChecked)
                deleteId = allIds
            }

            updateDeleteIds(deleteId, isChecked)
        })

        <?php if ($can_bulk_edit) { ?>
            $('#delete_all').on('click', function () {
                verConfirm('/customer_benefit_codes/delete_all/<?= $id ?>?ids='+deleteIds.toString())
            })
        <?php } ?>
    });

    function updateDeleteIds(id, add) {
        if (add) {
            if (Array.isArray(id)) {
                deleteIds = id
            } else {
                deleteIds.push(id)
            }
        } else {
            if (Array.isArray(id)) {
                deleteIds = []
            } else {
                deleteIds = deleteIds.filter(deleteId => deleteId !== id)
            }
        }

        $('#delete_all').attr('disabled', !deleteIds.length)

        setCookie('customerBenefitCodesDeleteIds', deleteIds)
    }

    function setCookie(name, valor) {
        let data = new Date();
        data.setTime(data.getTime() + (60 * 60 * 1000)); // milissegundos
        let expires = "expires=" + data.toUTCString();
        document.cookie = `${name}=${valor}; ${expires}; path=/`;
    }

    function getCookie(name) {
        let nomeBusca = name + "=";
        let cookies = document.cookie.split(';');
        for (let c of cookies) {
            c = c.trim();
            if (c.indexOf(nomeBusca) === 0) {
                return c.substring(nomeBusca.length);
            }
        }
        return null;
    }
</script>
