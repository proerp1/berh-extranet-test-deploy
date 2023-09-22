<?php $url_novo = $this->base."/proposals/add/".$id;  ?>
<?php
    echo $this->element("abas_customers", array('id' => $id));
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "proposals", "action" => "index", $id)); ?>" role="form" id="busca" autocomplete="off">
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
                        <th class="ps-4 w-150px min-w-150px rounded-start">Número</th>
                        <th>Data</th>
                        <th>Data prev. fechamento</th>
                        <th>Data fechamento</th>
                        <th>Valor VT</th>
                        <th>Valor VR/VA</th>
                        <th>Valor Combustível</th>
                        <th>Valor Multi</th>
                        <th>Valor Total</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Proposal"]["number"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Proposal"]["date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Proposal"]["expected_closing_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Proposal"]["closing_date"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["Proposal"]["transport_workers_price_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["Proposal"]["meal_workers_price_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["Proposal"]["fuel_workers_price_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["Proposal"]["multi_card_workers_price_total"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">R$<?php echo $data[$i]["Proposal"]["total_price"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base.'/proposals/edit/'.$id.'/'.$data[$i]["Proposal"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base.'/proposals/delete/'.$data[$i]["Proposal"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
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

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>