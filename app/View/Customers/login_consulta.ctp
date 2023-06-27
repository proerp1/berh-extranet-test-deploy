<?php $url_novo = $this->base . "/customers/add_login_consulta/" . $id;  ?>
<?php
    $url = $this->here;
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>
<div class="card mb-5 mb-xl-8">
    <?php if ($cliente['Customer']['winback'] == 1) { ?>
        <span class="badge badge-danger w-100">Cliente WINBACK</span>
    <?php } ?>
    <form action="<?php echo $this->Html->url(array( "controller" => "customers", "action" => "login_consulta", $id)); ?>" role="form" id="busca" autocomplete="off">
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
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="fas fa-filter"></i>
                        Filtro
                    </button>

                    <a type="button" class="btn btn-primary me-3" href="<?php echo $url_novo;?>">Novo</a>
                    <?php if($cliente['Resale']['robo_disponivel'] == 1) { ?>
                        <a type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#myModal">Criar Login - Serasa</a>
                    <?php } ?>
                    
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
                                <label class="form-label fs-5 fw-bold mb-3">Tipo:</label>
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Selecione" data-allow-clear="true" name="tipo" id="tipo">
                                    <option></option>
                                    <?php
                                        for ($a = 0; $a < count($tipos); $a++) {
                                            $selected = "";
                                            if (isset($_GET["t"])) {
                                                if ($tipos[$a]['id'] == $_GET["tipo"]) {
                                                    $selected = "selected";
                                                }
                                            }
                                            echo '<option value="' . $tipos[$a]['id'] . '" ' . $selected . '>' . $tipos[$a]['name'] . '</option>';
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
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
                <thead>
                    <tr class="fw-bolder text-muted bg-light">
                        <th class="ps-4 w-150px min-w-150px rounded-start">Status</th>
                        <th>Tipo</th>
                        <th>Login</th>
                        <th>Descrição</th>
                        <th class="w-200px min-w-200px rounded-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data) { ?>
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <tr>
                                <td class="fw-bold fs-7 ps-4">
                                    <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                        <?php echo $data[$i]["Status"]["name"] ?>
                                    </span>
                                </td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["LoginConsulta"]["tipo"] == 1 ? 'Manual' : 'Robô'; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["LoginConsulta"]["login"]; ?></td>
                                <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["LoginConsulta"]["descricao"]; ?></td>
                                <td class="fw-bold fs-7 ps-4">
                                    <a href="<?php echo $this->base . '/customers/edit_login_consulta/' . $id . '/' . $data[$i]["LoginConsulta"]["id"]; ?>" class="btn btn-info btn-sm">
                                        Editar
                                    </a>

                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base . '/customers/delete_login_consulta/' . $id . '/' . $data[$i]["LoginConsulta"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">Nenhum registro encontrado</td>
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
            $("#t").val(null).trigger('change');
            $("#tipo").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function () {
            $("#busca").submit();
        });

        var usuarios = JSON.parse('<?php echo json_encode($usuarios_json); ?>');
        $('#contato').on('change', function(){
            var contato_id = parseInt($(this).val());

            for (idx in usuarios) {
                if(parseInt(usuarios[idx].id) == contato_id){
                    $('#login_cpf').val(usuarios[idx].cpf);
                    $('#login_email').val(usuarios[idx].email);
                    $('#filial').val(usuarios[idx].filial);
                }
            }
        })

        $('#criar-login-btn').on('click', function(event){
            var valida_form = true;
            var msg = '';

            if($('#filial').val() == ''){
                valida_form = false;
                msg = 'Preencha uma filial';
            }

            if($('input[name="produtos[]"]:checked').length < 1){
                valida_form = false;
                msg = 'Selecione ao menos um produto';
            }

            if($('#contato').val() == ''){
                valida_form = false;
                msg = 'Selecione um contato';
            }

            if(!valida_form){
                alert(msg);
                event.preventDefault();
            }
        })
    });
</script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo $this->base; ?>/customers/create_logon_serasa">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Criar Login - Serasa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Contato:</label>
                        <select class="form-control" name="contato" id="contato">
                            <option value="">Selecione</option>
                            <?php foreach ($usuarios as $key => $u){ ?> 
                                <option value="<?php echo $u['CustomerUser']['id']; ?>"><?php echo $u['CustomerUser']['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" value="<?php echo $cliente['Customer']['documento']; ?>" name="cnpj">
                    <input type="hidden" value="<?php echo $cliente['Customer']['nome_primario']; ?>" name="razao">
                    <input type="hidden" value="<?php echo $cliente['Customer']['endereco']; ?>" name="logradouro">
                    <input type="hidden" value="<?php echo $cliente['Customer']['bairro']; ?>" name="bairro">
                    <input type="hidden" value="<?php echo $cliente['Customer']['complemento']; ?>" name="complemento">
                    <input type="hidden" value="<?php echo $cliente['Customer']['numero']; ?>" name="numero">
                    <input type="hidden" value="<?php echo $cliente['Customer']['cidade']; ?>" name="cidade">
                    <input type="hidden" value="<?php echo $cliente['Customer']['estado']; ?>" name="uf">
                    <input type="hidden" value="<?php echo $cliente['Customer']['id']; ?> - <?php echo $cliente['Customer']['nome_primario']; ?>" name="txtLogon">
                    <input type="hidden" value="<?php echo $id; ?>" name="id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">E-mail:</label>
                            <input type="text" name="email" id="login_email" class="form-control" value="" readonly>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">CPF:</label>
                            <input type="text" name="cpf" id="login_cpf" class="form-control" value="" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">CEP</label>
                            <input type="text" name="cep" class="form-control" value="<?php echo $cliente['Customer']['cep']; ?>" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="recipient-name" class="col-form-label">Número:</label>
                            <input type="text" name="numero" class="form-control" value="<?php echo $cliente['Customer']['numero']; ?>" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="recipient-name" class="col-form-label">Filial:</label>
                            <input type="text" name="filial" id="filial" class="form-control" value="" maxlength="4" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-2">
                            <label for="recipient-name" class="col-form-label">DDD</label>
                            <input type="text" name="ddd" class="form-control" value="<?php echo $ddd; ?>" readonly>
                        </div>
                        <div class="mb-3 col-md-7">
                            <label for="recipient-name" class="col-form-label">Telefone:</label>
                            <input type="text" name="tel" class="form-control" value="<?php echo $tel; ?>" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="recipient-name" class="col-form-label">Ramal:</label>
                            <input type="text" name="ramal" class="form-control" value="0">
                        </div>
                    </div>

                    <label for="prod-name" class="col-form-label mb-3">Produtos:</label>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="me_proteja"> MEPROTEJA EMP DISTRI
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="confie"> CONFIE DISTR NV
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="bureau_distr"> BUREAU DISTRIBUIDOR
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="concentre"> CONCENTRE DISTRIBUID
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="crednet"> CREDNET DISTRIBUIDOR
                            </label>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="infobusca"> INFOBUSCA DIST COMPLETO
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="relato"> RELATO MAIS DISTRIBUIDOR
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="credit_rat"> CREDIT RATING DISTRIBUIDOR
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="ser_empr_distr"> SERASA EMPRESAS DISTRIBUIDOR V1
                            </label>
                            <label class="mb-3">
                                <input type="checkbox" name="produtos[]" value="cred_light"> DISTR CREDNET LIGTH
                            </label>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-primary" id="criar-login-btn">Criar Login</button>
                </div>
            </form>
        </div>
    </div>
</div>