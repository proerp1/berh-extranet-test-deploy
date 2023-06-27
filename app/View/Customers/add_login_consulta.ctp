<?php
    $url = $this->base.'/customers/login_consulta/';
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
    
    $excluido = '';
    if(isset($this->data['LoginConsulta']['status_serasa'])){
        $excluido = $this->data['LoginConsulta']['status_serasa'] == 3 ? 'disabled="disabled"' : '';
    }
?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('LoginConsulta', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <input type="hidden" name="data[LoginConsulta][customer_id]" value="<?php echo $id ?>">

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Login</label>
                <?php echo $this->Form->input('login', array("type" => "text", "placeholder" => "Login", "class" => "form-control mb-3 mb-lg-0", "disabled" => isset($login_id))); ?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('descricao', array("type" => "text", "placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0")); ?>
            </div>

            <div class="mb-7">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', array("data-control" => "select2", "class" => "form-select mb-3 mb-lg-0", "empty" => "Selecione"));  ?>
            </div>

            <?php if (isset($login_id)) { ?>
                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Blindado</label>
                    <p><?php echo $this->request->data['LoginConsulta']['login_blindado'] == 1 ? 'Sim' : 'Não'; ?></p>
                </div>
            <?php } else { ?>
                <div class="mb-7">
                    <div class="form-check form-check-custom form-check-solid">
                        <?php echo $this->Form->input('login_blindado', array("type" => "checkbox", "class" => "form-check-input")); ?>
                        <label class="form-check-label" for="LoginConsultaLoginBlindado">
                            Blindado
                        </label>
                    </div>
                </div>
            <?php } ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/login_consulta/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php if (isset($this->data['LoginConsulta']) && $this->data['LoginConsulta']['tipo'] == 2 && $cliente['Resale']['robo_disponivel'] == 1): ?>
                        <hr>
                        <a href="#" class="btn btn-danger" id="btn-del-serasa" <?php echo $excluido; ?> >Excluir Serasa</a>
                        <a href="#" class="btn btn-info" id="btn-reset-pwd-serasa" <?php echo $excluido; ?> >Resetar Senha Serasa</a>
                        <a href="#" class="btn btn-primary" id="btn-act-deact" <?php echo $excluido; ?> >Ativar/Desativar Serasa</a>
                        <a href="#" class="btn btn-success" id="btn-add-acesso-serasa" <?php echo $excluido; ?> >Incluir Acesso</a>
                        <a href="#" class="btn btn-warning" id="btn-del-acesso-serasa" <?php echo $excluido; ?> >Remover Acesso</a>
                    <?php endif ?>
                    <?php if(isset($this->data['LoginConsulta']) && $this->request->data['LoginConsulta']['login_blindado'] == 1): ?>
                        <hr>
                        <a href="#" class="btn btn-primary" id="btn-act-deact-blindado" <?php echo $excluido; ?>>Reset Logon Blindado</a>
                    <?php endif ?>
                </div>
            </div>

            <?php if (isset($this->data['LoginConsulta']) && $this->data['LoginConsulta']['tipo'] == 2 && $cliente['Resale']['robo_disponivel'] == 1): ?>
                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Vinculado ao usuário</label>
                    <p><?php echo $this->data['CustomerUser']['name'].' - '.$this->data['CustomerUser']['email'].' - Filial '.$this->data['LoginConsulta']['filial'];  ?></p>
                </div>

                <div class="mb-7">
                    <label class="fw-semibold fs-6 mb-2">Status Serasa</label>
                    <p><?php echo $this->data['LoginConsulta']['status_serasa'] == 1 ? 'Ativo' : ($this->data['LoginConsulta']['status_serasa'] == 3 ? 'Excluído' : 'Inativo');  ?></p>
                </div>
            <?php endif ?>

            <?php if (isset($login_id) and CakeSession::read("Auth.User.group_id") == 1): ?>
                <i>Alterado por <?php echo $this->request->data['UsuarioAlteracao']['name'] ?> em <?php echo date('d/m/Y H:i:s', strtotime($this->request->data['LoginConsulta']['updated'])) ?></i>
            <?php endif ?>
        </form>
    </div>
</div>

<!-- Modal -->
<?php if(isset($this->data['LoginConsulta']['status_serasa'])){ ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="complete-form" action="<?php echo $this->base; ?>/customers/create_logon_serasa">
                <input type="hidden" value="<?php echo $cliente['Customer']['documento']; ?>" name="cnpj">
                <input type="hidden" value="<?php echo $cliente['Customer']['id']; ?>" name="cliente_id">
                <input type="hidden" value="<?php echo $login_id; ?>" name="id">
                <input type="hidden" value="<?php echo $this->data['LoginConsulta']['filial']; ?>" name="hidden_filial">
                <input type="hidden" value="<?php echo $this->data['LoginConsulta']['login']; ?>" name="login">
                <input type="hidden" value="<?php echo $cliente['Customer']['nome_primario']; ?>" name="razao">
                
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Criar Login - Serasa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="<?php echo $cliente['Customer']['documento']; ?>" name="cnpj">
                    <input type="hidden" value="<?php echo $id; ?>" name="id">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">Contato:</label>
                            <input type="hidden" name="contato" value="<?php echo $this->data['CustomerUser']['id'];  ?>">
                            <input type="text" name="contato_name" class="form-control" value="<?php echo $this->data['CustomerUser']['name'];  ?>" readonly>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">E-mail:</label>
                            <input type="text" name="email" id="login_email" class="form-control" value="<?php echo $this->data['CustomerUser']['email']; ?>" readonly>
                        </div>
                    </div>

                    <div class="row additional-1">
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
                            <input type="text" name="filial" class="form-control" value="1">
                        </div>
                    </div>

                    <div class="row additional-2">
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

                    <label for="prod-name" class="col-form-label mb-3 additional-3">Produtos:</label>

                    <div class="row additional-4">
                        <div class="mb-3 col-md-6">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="me_proteja"> MEPROTEJA EMP DISTRI
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="confie"> CONFIE DISTR NV
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="bureau_distr"> BUREAU DISTRIBUIDOR
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="concentre"> CONCENTRE DISTRIBUID
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="crednet"> CREDNET DISTRIBUIDOR
                            </label>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="infobusca"> INFOBUSCA DIST COMPLETO
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="relato"> RELATO MAIS DISTRIBUIDOR
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="credit_rat"> CREDIT RATING DISTRIBUIDOR
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="ser_empr_distr"> SERASA EMPRESAS DISTRIBUIDOR V1
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="produtos[]" value="cred_light"> DISTR CREDNET LIGTH
                            </label>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-primary" id="options-login-btn" data-loading-text="Aguarde...">Criar Login</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myModalSimple" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="confirm-simple-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabelSimple">#msg# - Serasa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="msg-simple"></span>
                    <input type="hidden" value="<?php echo $cliente['Customer']['documento']; ?>" name="cnpj">
                    <input type="hidden" value="<?php echo $cliente['Customer']['id']; ?>" name="cliente_id">
                    <input type="hidden" value="<?php echo $login_id; ?>" name="id">
                    <input type="hidden" value="<?php echo $this->data['LoginConsulta']['login']; ?>" name="login">
                    <input type="hidden" value="<?php echo $this->data['LoginConsulta']['status_serasa']; ?>" name="status_serasa" id="status_serasa">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-primary btn-action-modal-simple" id="options-login-btn-simple" data-loading-text="Aguarde...">Criar Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .mb-3 {
        margin-bottom: 10px;
    }

    .checkbox-inline {
        margin-left: 0px !important;
        margin-bottom: 5px;
    }
</style>

<script>
    $( document ).ready(function() {
        var usuarios = JSON.parse('<?php echo json_encode($usuarios_json); ?>');
        var form_url = "<?php echo $this->base; ?>";
        $('#btn-reset-pwd-serasa').on('click', function(){
            $('.additional-1').hide();
            $('.additional-2').hide();
            $('.additional-3').hide();
            $('.additional-4').hide();
            $('#myModalLabel').text('Resetar Senha - Serasa');
            $('#options-login-btn').text('Resetar Senha');

            $('#complete-form').attr('action', form_url+'/customers/reset_senha_logon_serasa')

            $('#myModal').modal('show');
        })
        $('#btn-add-acesso-serasa').on('click', function(){
            $('.additional-1').hide();
            $('.additional-2').hide();
            $('.additional-3').show();
            $('.additional-4').show();
            $('#myModalLabel').text('Incluir Acesso - Serasa');
            $('#options-login-btn').text('Incluir Acesso');

            $('#complete-form').attr('action', form_url+'/customers/add_access_logon_serasa')

            $('#myModal').modal('show');
        })
        $('#btn-del-acesso-serasa').on('click', function(){
            $('.additional-1').hide();
            $('.additional-2').hide();
            $('.additional-3').show();
            $('.additional-4').show();
            $('#myModalLabel').text('Remover Acesso - Serasa');
            $('#options-login-btn').text('Remover Acesso');

            $('#complete-form').attr('action', form_url+'/customers/remove_access_logon_serasa')

            $('#myModal').modal('show');
        })
        $('#btn-act-deact').on('click', function(){
            var status_serasa = $('#status_serasa').val() == '2' ? 'Ativar' : 'Desativar';
            var login = $('#LoginConsultaLogin').val();
            $('#myModalLabelSimple').text(status_serasa+' Login - Serasa');
            $('#options-login-btn-simple').text(status_serasa+' Login');
            $('#msg-simple').text('Deseja mesmo '+status_serasa+' o  Login '+login+'?');

            $('#confirm-simple-form').attr('action', form_url+'/customers/act_deact_serasa')

            $('#myModalSimple').modal('show');
        })

        $('#btn-act-deact-blindado').on('click', function(){
            var status_serasa = 'Resetar';
            var login = $('#LoginConsultaLogin').val();
            $('#myModalLabelSimple').text(status_serasa+' Logon Blindado - Serasa');
            $('#options-login-btn-simple').text(status_serasa+' Logon Blindado');
            $('#msg-simple').text('Deseja mesmo '+status_serasa+' o  Logon Blindado '+login+'?');

            $('#confirm-simple-form').attr('action', form_url+'/customers/reset_blindado_serasa')

            $('#myModalSimple').modal('show');
        })

        $('#btn-del-serasa').on('click', function(){
            var status_serasa = 'Excluir';
            var login = $('#LoginConsultaLogin').val();
            $('#myModalLabelSimple').text(status_serasa+' Login - Serasa');
            $('#options-login-btn-simple').text(status_serasa+' Login');
            $('#msg-simple').text('Deseja mesmo '+status_serasa+' o  Login '+login+'?');

            $('#confirm-simple-form').attr('action', form_url+'/customers/delete_logon_serasa')

            $('#myModalSimple').modal('show');
        })

        if($('#btn-del-serasa').attr('disabled') == 'disabled'){
            $('#btn-reset-pwd-serasa').unbind("click");
            $('#btn-add-acesso-serasa').unbind("click");
            $('#btn-del-acesso-serasa').unbind("click");
            $('#btn-act-deact').unbind("click");
            $('#btn-del-serasa').unbind("click");
        }


        $('#options-login-btn').on('click', function(event){
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
        $('#contato').on('change', function(){
            var contato_id = parseInt($(this).val());

            for (idx in usuarios) {
                if(parseInt(usuarios[idx].id) == contato_id){
                    $('#login_cpf').val(usuarios[idx].cpf);
                    $('#login_email').val(usuarios[idx].email);
                }
            }
        })
    });
</script>

<?php } ?>