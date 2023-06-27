<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $("#p").on("change", function(){
            var val = $(this).val();

            show_div_entre(val);
        });

        $("#e").on("change", function(){
            var estado = $(this).val();
            var el = $(this);

            load_city(estado, el)
        });

        show_div_entre($("#p").val());
        load_city($("#e").val(), $("#e"));
    })

    function load_city(estado, el){
        var source   = $("#template_cidade").html();
        var template = Handlebars.compile(source);

        var cidade = $("#cidade").val();

        $.ajax({
            url: base_url+"/reports/get_cidade/",
            type: "post",
            data: {estado: estado},
            dataType: "json",
            beforeSend: function(xhr){
                $(".loading_img").remove();
                el.parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
            },
            success: function(data){
                $(".loading_img").remove();
                var html_opt  = "<option value=''>Selecione</option>";
                
                $.each(data, function(index, value) {
                    var selected = '';
                    if (cidade == value.CepbrCidade.cidade) {
                        selected = 'selected';
                    }
                  var context = {name: value.CepbrCidade.cidade, id: value.CepbrCidade.cidade, selected: selected};
                  html_opt    += template(context);
                });

                $("#c").html(html_opt);
            }
        });
    }

    function show_div_entre(val){
        if (val == 'entre') {
            $(".div_entre").show();
        } else {
            $(".div_entre").hide();
        }
    }
</script>
<input type="hidden" id="cidade" value="<?php echo isset($_GET['c']) ? $_GET['c'] : '' ?>">
<script id="template_cidade" type="text/x-handlebars-template">
    <option value="{{id}}" {{selected}}>{{name}}</option>
</script>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "inadimplentes")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->base.'/reports/inadimplentes/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Valor:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="p" id="p">
                                    <option value="" <?php echo isset($_GET['p']) ? ($_GET['p'] == '' ? 'selected' : '') : '' ?>>Todos</option>
                                    <option value="acima" <?php echo isset($_GET['p']) ? ($_GET['p'] == 'acima' ? 'selected' : '') : '' ?>>Valor acima de</option>
                                    <option value="entre" <?php echo isset($_GET['p']) ? ($_GET['p'] == 'entre' ? 'selected' : '') : '' ?>>Valor entre</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Valor:</label>
                                <div class="col d-flex align-items-center">
                                    <span class="position-absolute ms-6">
                                        R$
                                    </span>
                                    <input type="text" class="form-control form-control-solid ps-15 money_exchange" id="valor_ini" name="valor_ini" value="<?php echo isset($_GET["valor_ini"]) ? $_GET["valor_ini"] : ""; ?>" />
                                </div>
                            </div>
                            <div class="mb-10 div_entre">
                                <label class="form-label fs-5 fw-bold mb-3">Valor final:</label>
                                <div class="col d-flex align-items-center">
                                    <span class="position-absolute ms-6">
                                        R$
                                    </span>
                                    <input type="text" class="form-control form-control-solid ps-15 money_exchange" id="valor_fim" name="valor_fim" value="<?php echo isset($_GET["valor_fim"]) ? $_GET["valor_fim"] : ""; ?>" />
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Estado:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="e" id="e">
                                    <option value="" selected disabled></option>
                                    <option value="" <?php echo isset($_GET['e']) ? ($_GET['e'] == '' ? 'selected' : '') : '' ?>>Todos</option>
                                    <?php 
                                        foreach ($estados as $estado){
                                            $selected = '';
                                            if (!empty($_GET['e'])) {
                                                if ($estado == $_GET['e']) {
                                                    $selected = 'selected';
                                                }
                                            }
                                            echo '<option value="'.$estado.'" '.$selected.'>'.$estado.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Cidade:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="c" id="c">
                                    <option value="" selected disabled></option>
                                    <option value="">Todos</option>                                 
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Limpar</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                            </div>
                        </div>
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
                        <th class="ps-4 rounded-start">Cliente</th>
                        <th>Estado</th>
                        <th>Cidade</th>
                        <th class="w-200px min-w-200px rounded-end">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'].' - '.$data[$i]['Customer']['nome_primario'].' '.$data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['estado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['cidade'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo number_format($data[$i][0]['total'],2,',','.') ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2"></td>
                            <th class="fw-bold fs-7 ps-4">Total</th>
                            <td class="fw-bold fs-7 ps-4"><?php echo number_format($total_valores,2,",","."); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="4">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#q").val(null);
            $("#p").val(null);
            $("#valor_ini").val(null);
            $("#valor_fim").val(null);
            $("#e").val(null);
            $("#c").val(null);
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });
    });
</script>