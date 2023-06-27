<script type="text/javascript">
    $(document).ready(function(){
        
        $(".input-daterange").datepicker({format: 'dd/mm/yyyy', multidate: false, weekStart: 1, autoclose: true, language: "pt-BR", todayHighlight: true, toggleActive: true});
        
        get_ids();

        $("#marcar_todos").on("click", function(){
            if ($(this).is(':checked')) {
                $(".check_individual").prop('checked', true);
            } else {
                $(".check_individual").prop('checked', false);
            }

            get_ids();
        });

        //função para salvar os id´s da lista


        $(".check_id").on("click", function(){  
            get_ids();
        })
    
    });


    function get_ids() {
        if ($(".check_individual:checked").length > 0) {
            $(".js_salvar").show();
        } else {
            $(".js_salvar").hide();
        }

        var userid = '';
        var incomeid = '';
        var count = 0;
        $(".check_individual:checked").each(function(index, el) {
            userid += $(this).data('id')+',';
            incomeid += $(this).data('incomeid')+',';
            count++;
        });

        //$(".js_salvar").attr('href', '<?php echo $this->base ?>/registro_lancamentos/cancelar_faturas/?id='+userid);
        $("#user_id").val(userid);
        $("#income_id").val(incomeid);
    } 
</script>

<?php
    if (isset($id)) {
        echo $this->element("abas_emails", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "emails_campanhas", "action" => "list_emails", $id)); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status do cliente:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="sc" id="sc">
                                    <option></option>
                                    <?php
                                        foreach ($statusClientes as $statusCliente) {
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($statusCliente['Status']['id'] == $_GET["sc"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$statusCliente['Status']['id'].'" '.$selected.'>'.$statusCliente['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Franquias:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="f" id="f">
                                    <option></option>
                                    <?php
                                        for ($a = 0; $a < count($codFranquias); $a++) {
                                            $selected = "";
                                            if (isset($_GET["f"])) {
                                                if ($codFranquias[$a]['Resale']['id'] == $_GET["f"]) {
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$codFranquias[$a]['Resale']['id'].'" '.$selected.'>'.$codFranquias[$a]['Resale']['nome_fantasia'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Vencimento:</label>
                                <div class="input-daterange input-group" id="datepicker">
                                    <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>">
                                    <span class="input-group-text" style="padding: 5px;"> até </span>
                                    <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>">
                                </div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status da conta:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="s" id="s">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($status); $a++){
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($status[$a]['Status']['id'] == $_GET["s"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
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
        <form class="pe-3 mb-3 justify-content-end d-flex" action="<?php echo $this->Html->url([ "controller" => "emails_campanhas", "action" => "list_emails"]).'/'.$id; ?>" method="post" role="form" id="salvar">
            <div class="form-group">
                <input type="hidden" name="user_id" id="user_id" >
                <input type="hidden" name="income_id" id="income_id" >
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success waves-effect check_id js_salvar" title="Salvar">
                        Salvar
                    </button>
                </div>
            </div>
        </form>

        <h4><?php echo count($data) ?> cliente(s)</h4>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">
                            <input type="checkbox" class="check_all" id="marcar_todos" <?php echo $data ? 'checked' : '' ?>>
                            <label for="check_all">Todos</label>
                        </th>
                        <th>Código</th>
                        <th>Nome Fantasia</th>
                        <th>Contato</th>
                        <th class="rounded-end">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><input type="checkbox" class="check_id check_individual" data-id="<?php echo $data[$i]["Customer"]["id"] ?>" data-incomeid="<?php echo $data[$i]["Income"]["id"] ?>" checked></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['codigo_associado'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['nome_secundario'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Customer']['contato'] ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo strtolower($data[$i]['Customer']['email']) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4" colspan="5">Nenhum registro encontrado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="page page-profile">
    <section class="panel panel-default">
        <div class="panel-heading">

            <div class="row">
                
            </div>
        </div>
    </section> <!-- /panel-default -->
</div> <!-- /page-profile -->