<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>

<div class="row gy-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-lg-6 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-success h-75px w-75px">
                    <i class="fas fa-list fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo $consultas_realizadas ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Linhas importadas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-sm-6">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-start align-items-center flex-row gap-10">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary h-75px w-75px">
                    <i class="fas fa-dollar-sign fa-3x text-white"></i>
                </div>
                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-3x text-gray-800 lh-1 ls-n2"><?php echo number_format($valor_total[0]['total'], 2, ',', '.') ?></span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Valor Total R$</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url([ "controller" => "billings", "action" => "pefin", $id]); ?>/" role="form" id="busca" autocomplete="off">
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
                    <?php if (count($data) == 0): ?>
                        <a type="button" href="<?php echo $this->base.'/billings/add_pefin/'.$id; ?>" class="btn btn-primary">Processar registros</a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Produto</th>
                        <th>Código Associado</th>
                        <th>Cliente</th>
                        <th>Qtde</th>
                        <th>Valor Unitário R$</th>
                        <th class="rounded-end">Valor Total R$</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Product']['name'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Pefin']['qtde_realizado'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['Pefin']['valor_unitario_formatado'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['Pefin']['valor_total_formatado'] ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="7">Nenhum registro encontrado</td>
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
        $("form").on("submit", function(){
            var $el = $(".js-importar");

            $el.button('loading');
        });

        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    })
</script>