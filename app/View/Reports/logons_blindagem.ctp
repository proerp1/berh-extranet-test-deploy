<?php 
    
    if(isset($_GET["s"]) && $_GET["s"] != ""){
        $select = $_GET["s"];
    } else {
        $select ='';
    }
    
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "logons_blindagem")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="logon" name="logon" value="<?php echo isset($_GET["logon"]) ? $_GET["logon"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->base.'/reports/logons_blindagem/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>

                    <a href="#" class="btn btn-light-success me-3 blindagem" id="btn-blindar">
                        <i class="fas fa-lock"></i>
                        Blindar
                    </a>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="st" id="st">
                                    <option></option>
                                    <?php 
                                        foreach ($status as $status_id => $status_name) {
                                            $selected = '';
                                            if (isset($_GET['st'])) {
                                                if ($_GET['st'] == $status_id) {
                                                    $selected = 'selected';
                                                }
                                            }

                                            echo "<option value='$status_id' $selected>$status_name</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status dos logins:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                    <option></option>
                                    <option value="1" <?php echo ($select == 1) ? "selected" : "" ?>>Blindado</option>
                                    <option value="2" <?php echo ($select == 2) ? "selected" : "" ?>>Pendente</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start"><input type="checkbox" name="todos" id="todos"> Marcar Todos</th>
                        <th>Login de Consulta</th>
                        <th>Código do Cliente</th>
                        <th>Cliente</th>
                        <th>Criado Por</th>
                        <th>Data e Hora</th>
                        <th class="w-250px min-w-250px rounded-end">Status do Login de Consulta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados) { ?>
                        <?php for ($i=0; $i < count($dados); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><input type="checkbox" name="login_blindado" class="login" data-id="<?php echo $dados[$i]["LoginConsulta"]["id"] ?>" data-loginid="<?php echo $dados[$i]["LoginConsulta"]["id"] ?>"></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["LoginConsulta"]["login"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["codigo_associado"] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["Customer"]["nome_secundario"] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $dados[$i]["User"]["name"] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo date("d/m/Y H:i:s", strtotime($dados[$i]["LoginConsulta"]["created"])) ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo ($dados[$i]["LoginConsulta"]["login_blindado"] == 2) ? "Pendente" : "Blindado" ?></td>
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
        <?php if ($dados){ ?>
            <label class="pull-left" style="margin: 0px 10px 0 0"><?php echo $this->Paginator->counter("{:count} registro(s)"); ?></label>  
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#logon").val(null);
            $("#s").val(null);
            $("#st").val(null);
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#logon').on('change', function () {
            $("#busca").submit();
        });

        $(".blindagem").hide();

        $("#todos").on("change", function(){
            if ($(this).is(':checked')) {
                $(".login").prop('checked', true);
            } else {
                $(".login").prop('checked', false);
            }

            get_ids();
        })

        $(".login").on("click", function(){ 
            get_ids();
        })

        $(".login").on("click", function(){
            var loginid = $(this).data('loginid');

            if (!$(this).is(':checked')) {
                $("#"+loginid).prop('checked', false);
                $("body").find("[data-loginid='"+loginid+"']").prop('checked', false);
            } else {
                $("#"+loginid).prop('checked', true);
                $("body").find("[data-loginid='"+loginid+"']").prop('checked', true);
            }

            get_ids();
        });

        function get_ids() {
            if ($(".login:checked").length > 0) {
                $(".blindagem").show();
            } else {
                $(".blindagem").hide();
            }

            var ids = '';
            $(".login:checked").each(function(index, el) {
                ids += $(this).data('id')+',';
            });

            $("#btn-blindar").attr('href', '<?php echo $this->base ?>/reports/blindar/?id='+ids);
        }
    })

</script>