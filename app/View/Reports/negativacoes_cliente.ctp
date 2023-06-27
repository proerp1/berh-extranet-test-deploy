<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "negativacoes_cliente")); ?>/" role="form" id="busca" autocomplete="off">
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

                    <a onclick="confirmNeg('Tem certeza que deseja gerar registros para esse(s) cliente(s)?')" href="javascript:;" class="btn btn-light-primary me-3 js_link_gerar_registros" style="display:none">
                        <i class="fas fa-file"></i>
                        Gerar negativações
                    </a>

                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start"><input type="checkbox" class="check_all" id="check_all"> <label for="check_all">Selecionar todos</label></th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Venc da dívida</th>
                        <th>Valor</th>
                        <th class="w-150px min-w-150px rounded-end">Inclusão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <input type="checkbox" class="check_conta check_individual" data-id="<?php echo $data[$i]["CustomerPefin"]["id"] ?>" id="<?php echo $data[$i]["CustomerPefin"]["id"] ?>">
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_primario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['NaturezaOperacao']['nome'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerPefin']['venc_divida'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['CustomerPefin']['valor'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date('d/m/Y H:i:s', strtotime($data[$i]['CustomerPefin']['created'])) ?></td>
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
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $(".check_all").on("change", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }

            get_ids();
        })

        $(".check_conta").on("click", function(){   
            get_ids();
        })
    })

    function get_ids() {
        if ($(".check_individual:checked").length > 0) {
            $(".js_link_gerar_registros").show();
        } else {
            $(".js_link_gerar_registros").hide();
        }

        var pefinid = '';
        $(".check_individual:checked").each(function(index, el) {
            pefinid += $(this).data('id')+',';
        });

        return pefinid;
    }

    function confirmNeg(message, link) {
        bootbox.confirm({
            message,
            title: 'Atenção',
            buttons: {
                confirm: {
                    label: 'Sim',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Não',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                console.log('This was logged in the callback: ' + result);

                var pefinid = get_ids();

                if (result) {
                    window.location.href = '<?php echo $this->base ?>/reports/insert_pefin/?id='+pefinid;
                }
            }
        });
    }
</script>