<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body ">
        <?php echo $this->Form->create('Negativacao', ["class" => "form-horizontal col-md-12", "action" => "/".$form_action."/","id" => "form_img" ,"method" => "post", 'enctype' => 'multipart/form-data']); ?>
            <input type="hidden" name="data[Negativacao][billing_id]" value="<?php echo $id ?>">
            
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Importar Serasa (.csv)</label>
                <div class="col-sm-5">
                    <?php echo $this->Form->input('csv', ["div" => false, "label" => false, "required" => false, "extensaoValida" => true, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o arquivo"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/billings/edit/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-importar">Salvar</button>
                </div>
            </div>

        </form>
    </div>
</div>

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
    <form action="<?php echo $this->Html->url([ "controller" => "billings", "action" => "negativacao", $id]); ?>/" role="form" id="busca" autocomplete="off">
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
                        <th>Qtde Consultada</th>
                        <th>Qtde Faturada</th>
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
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Negativacao']['qtde_consumo'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Negativacao']['qtde_excedente'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['Negativacao']['valor_unitario_formatado'] ?></td>
                                <td class="fw-bold fs-7 ps-4">R$ <?php echo $data[$i]['Negativacao']['valor_total_formatado'] ?></td>
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