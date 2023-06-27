<?php 
    echo $this->element("abas_price_tables");
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "price_tables", "action" => "products", $id)); ?>" role="form" id="busca" autocomplete="off">
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

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <form action="<?php echo $this->Html->url(array( "controller" => "price_tables", "action" => "atualiza_precos", $id)); ?>/" method="post">
            <div class="table-responsive">
                <?php echo $this->element("table"); ?>
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-4 w-50 min-w-50 rounded-start">Produto</th>
                            <th class="w-50 min-w-50 rounded-end">Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data) { ?>
                            <?php for ($i=0; $i < count($data); $i++) { ?>
                                <tr>
                                    <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Product"]["name"]; ?></td>
                                    <td class="fw-bold fs-7 ps-4">
                                        <input type="hidden" name="product_price_id[]" value="<?php echo $data[$i]["ProductPrice"]["id"]; ?>">
                                        <input type="text" class="form-control money_exchange" name="value[]" value="<?php echo $data[$i]["ProductPrice"]["value"]; ?>">
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4" colspan="8">Nenhum registro encontrado</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php echo $this->element("pagination"); ?>
            <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Atualizar preços</button>
        </form>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>