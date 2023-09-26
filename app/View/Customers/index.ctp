<?php $url_novo = $this->base."/customers/add/"; ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array( "controller" => "customers", "action" => "index")); ?>/" role="form" id="busca" autocomplete="off">
        <?php if (isset($_GET['logon'])): ?>
            <input type="hidden" name="logon" value="">
        <?php endif ?>
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="<?php echo isset($_GET['logon']) ? 'Digite o logon' : 'Buscar' ?>" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a href="<?php echo $this->base.'/customers/index/?exportar=true&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ;?>" class="btn btn-light-primary me-3">
                        <i class="fas fa-file-excel"></i>
                        Exportar
                    </a>


                    <a type="button" class="btn btn-primary" href="<?php echo $url_novo;?>">Novo</a>
                    
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-400px" data-kt-menu="true" id="kt-toolbar-filter">
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Opções</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        
                        <div class="px-7 py-5">
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Status:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="t" id="t">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($status); $a++){
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if($status[$a]['Status']['id'] == $_GET["t"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$status[$a]['Status']['id'].'" '.$selected.'>'.$status[$a]['Status']['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Franquias:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="f" id="f">
                                    <option></option>
                                    <?php
                                        for($a = 0; $a < count($codFranquias); $a++){
                                            $selected = "";
                                            if (isset($_GET["f"])) {
                                                if($codFranquias[$a]['Resale']['id'] == $_GET["f"]){
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="'.$codFranquias[$a]['Resale']['id'].'" '.$selected.'>'.$codFranquias[$a]['Resale']['nome_fantasia'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                                <div class="input-group input-daterange" id="datepicker">
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
    <?php echo $this->element("pagination"); ?>
    <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Código</th>
                        <th>Nome fantasia</th>
                        <th>CNPJ</th>
                        <th>Cidade</th>
                        <th>UF</th>
                        <th>Revenda</th>
                        <th>Status</th>
                        <th class="w-300px min-w-300px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i=0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["codigo_associado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_secundario"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["documento"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["cidade"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["estado"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Resale"]["nome_fantasia"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base; ?>/customers/edit/<?php echo $data[$i]["Customer"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>
                                    
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
        <?php echo $data ? $this->element("pagination") : ''; ?>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function () {
            $("#t").val(null).trigger('change');
            $("#f").val(null).trigger('change');
            $("#de").val(null);
            $("#ate").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        $("#cnpj").mask("99.999.999/9999-99");

        $('#consulta-cnpj').on('click', function(){
            if($('#cnpj').val() != ''){
                if($(this).attr('disabled') != 'disabled'){
                    $('#consulta-cnpj').attr('disabled', true);
                    $('#loading').show();
                    $.ajax({
                        method: "POST",
                        url: "<?php echo $this->base; ?>/customers/simulate_access_serasa",
                        data: { cnpj: $('#cnpj').val() },
                        dataType: "JSON",
                        success: function(data){
                            $('#container-resultado').show();
                            $('#result-messsage').text(data.message);
                            $('#loading').hide();
                            $('#consulta-cnpj').removeAttr('disabled');
                        }
                    })
                }
            } else {
                alert('Preencha o CNPJ')
                $('#consulta-cnpj').removeAttr('disabled');
                $('#loading').hide();
            }
        })
    })
</script>

<!-- Modal -->
<div class="modal fade" id="modal_filtro" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_filtroLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="confirm-simple-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabelSimple">Simular Filtro de Adesão</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="recipient-name" class="col-form-label">CNPJ:</label>
                            <input type="text" name="cnpj" id="cnpj" class="form-control">
                        </div>
                    </div>

                    <div class="row" id="container-resultado" style="margin-top: 20px; display:none;">
                        <div class="mb-3 col-12">
                            <label for="recipient-name" class="col-form-label">Resultado:</label>
                            <span id="result-messsage"></span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <span id="loading" style="display:none">Carregando...</span>
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Voltar</button>
                    <a type="submit" class="btn btn-primary js-salvar" id="consulta-cnpj">Simular</a>
                </div>
            </form>
        </div>
    </div>
</div>
