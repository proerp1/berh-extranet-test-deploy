<?php echo $this->Html->script("html_editor/summernote", array('block' => 'script')); ?>
<?php echo $this->Html->script("html_editor/summernote-pt-BR", array('block' => 'script')); ?>
<?php echo $this->Html->css("html_editor/summernote", array('block' => 'css')); ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#summernote').summernote({
            lang: 'pt-BR',
            height: 200,
            toolbar : [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize', 'fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['group', [ 'video', 'link', 'picture', 'hr' ]],
                ['misc', [ 'codeview', 'undo', 'redo' ]],
                ['help', [ 'help' ]],
            ]
        });

        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });

        $(document).ready(function(){
            function buscarCEP(cepInput, enderecoInput, bairroInput, cidadeInput, estadoInput) {
                var $el = $(cepInput);
                
                $.ajax({
                    url: 'https://viacep.com.br/ws/' + $el.val() + '/json/',
                    type: "GET",
                    dataType: "json",
                    beforeSend: function(){
                        $el.parent().find('span > i').removeClass('fas fa-map-marker');
                        $el.parent().find('span > i').addClass('fas fa-spinner fa-spin');
                    }
                }).done(function(data) {
                    if (!data.erro) {
                        $(enderecoInput).val(data.logradouro);
                        $(bairroInput).val(data.bairro);
                        $(cidadeInput).val(data.localidade);
                        $(estadoInput).val(data.uf);
                    } else {
                        alert("CEP não encontrado.");
                    }
                }).fail(function(){
                    alert("Erro ao consultar o CEP. Tente novamente.");
                }).always(function() {
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                });
            }

            $("#cep").change(function() {
                buscarCEP("#cep", "#endereco", "#bairro", "#cidade", "#estado");
            });

            $("#cepentrega").change(function() {
                buscarCEP("#cepentrega", "#enderecoentrega", "#bairroentrega", "#cidadeentrega", "#estadoentrega");
            });

            $("#cep, #cepentrega").mask("99999-999");

            $("#mesmo_endereco").change(function() {
                if($(this).is(":checked")) {
                    $("#cepentrega").val($("#cep").val());
                    $("#enderecoentrega").val($("#endereco").val());
                    $("#numeroentrega").val($("#numero").val());
                    $("#complementoentrega").val($("#complemento").val());
                    $("#bairroentrega").val($("#bairro").val());
                    $("#cidadeentrega").val($("#cidade").val());
                    $("#estadoentrega").val($("#estado").val());
                } else {
                    $("#cepentrega, #enderecoentrega, #numeroentrega, #complementoentrega, #bairroentrega, #cidadeentrega, #estadoentrega").val('');
                }
            });
        });



        
        tipo_cliente();

        $("#tipo_pessoa").change(function(){
            tipo_cliente();
        });

        <?php if ($form_action == 'edit'): ?>
            $(".situacao").on("change", function(){
                var val = $(this).val();
                var el = $(this);

                $(".error").remove();
                $(".js-salvar").attr('disabled', false);
                if (val == 5) {
                    $.ajax({
                        url: base_url+"/customers/check_income/",
                        type: "post",
                        data: {id : <?php echo $id ?>},
                        dataType: "json",
                        beforeSend: function(xhr){
                            el.parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
                        },
                        success: function(data){
                            console.log(data);
                            $(".loading_img").remove(); 
                            if (data > 0) {
                                el.parent().append("<span class='help-inline error'>Esse cliente tem contas a pagar em aberto!</span>");
                                $(".js-salvar").attr('disabled', true);
                            };
                        }
                    });
                };
            });
        <?php endif ?>

        $("#documento").on("change", function(){
            var val = $(this).val();
            var el = $(this);

            $(".error").remove();
            $(".js-salvar").attr('disabled', false);
            $.ajax({
                url: base_url+"/customers/check_documento/",
                type: "post",
                data: {id : "<?php echo isset($id) ? $id : 0 ?>", doc: val},
                dataType: "json",
                beforeSend: function(xhr){
                    el.parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
                },
                success: function(data){
                    console.log(data);
                    $(".loading_img").remove(); 
                    if (data > 0) {
                        el.parent().append("<span class='help-inline error'>Esse documento já está cadastrado no sistema</span>");
                        $(".js-salvar").attr('disabled', true);
                    };
                }
            });
        });

        $("#cep").mask("99999-999");
        $("#cepentrega").mask("99999-999");
        $("#CustomerCreated").mask("99/99/9999");
        $("#CustomerCpfResponsavel").mask("999.999.999-99")
        $(".telefone").focusout(function(){
            var phone, element;
            element = $(this);
            element.unmask();
            phone = element.val().replace(/\D/g, '');
            if(phone.length > 10) {
                element.mask("(99) 99999-999?9");
            } else {
                element.mask("(99) 9999-9999?9");
            }
        }).trigger('focusout');
    });

    function tipo_cliente(){
        if($("#tipo_pessoa").val() == 1){
            $("#documento").parent().find("label").text("CPF");
            $("#documento").attr('placeholder', "CPF");
            $("#nome_primario").parent().find("label").text("Nome");
            $("#nome_primario").attr('placeholder', "Nome");
            $("#nome_secundario").parent().find("label").text("Sobrenome");
            $("#nome_secundario").attr('placeholder', "Sobrenome");
            $("#CustomerCpfResponsavel").parent().find("label").text("CPF Responsável");
            $("#CustomerCpfResponsavel").attr('placeholder', "CPF Responsável");
            formatCpf('#documento');
            $("#ie").parent().hide();
        } else {
            $("#documento").parent().find("label").text("CNPJ");
            $("#documento").attr('placeholder', "CNPJ");
            $("#nome_primario").parent().find("label").text("Razão social");
            $("#nome_primario").attr('placeholder', "Razão social");
            $("#nome_secundario").parent().find("label").text("Nome fantasia");
            $("#nome_secundario").attr('placeholder', "Nome fantasia");
            $("#CustomerCpfResponsavel").parent().find("label").text("CPF Responsável");
            $("#CustomerCpfResponsavel").attr('placeholder', "CPF Responsável");
            formatCnpj('#documento');
            $("#ie").parent().show();
        }
    }
</script>

<script id="template_cidade" type="text/x-handlebars-template">
    <option value="{{id}}">{{name}}</option>
</script>

<?php
    if (isset($id)) {
        $url = $this->here;
        echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Customer', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
            <?php if (isset($id)) { ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(array('Customer' => $this->request->data['Customer'])); ?></textarea>
            <?php } ?>

            <?php if (isset($id)) { ?>
                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Código</label>
                        <p><?php echo $this->request->data['Customer']['codigo_associado'] ?></p>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Situação</label>
                        <p><?php echo $this->request->data["Status"]['name'] ?></p>
                    </div>
                
                    
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Data de cadastro</label>
                        <p><?php echo $this->request->data['Customer']['created'] ?></p>
                    </div>

              
                    
                </div>
            <?php } ?>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Revenda</label>
                    <?php echo $this->Form->input('cod_franquia', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Executivo</label>
                    <?php echo $this->Form->input('seller_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "required" => true ]);?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Taxa (%)</label>
                    <?php echo $this->Form->input('commission_fee_percentage', ["type" => "text", "id" => "commission_fee_percentage", "class" => "form-control money_exchange mb-3 mb-lg-0", "placeholder" => "Comissão"]); ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
                    <?php echo $this->Form->input('tipo_pessoa', array("id" => "tipo_pessoa", "data-control" => "select2", "empty" => "Selecione", 'options' => array('1' => 'Física', '2' => 'Jurídica'), "class" => "form-select mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">CNPJ</label>
                    <?php echo $this->Form->input('documento', array("id" => "documento", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Razão social</label>
                    <?php echo $this->Form->input('nome_primario', array("id" => "nome_primario", "placeholder" => "Razão social", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Nome fantasia</label>
                    <?php echo $this->Form->input('nome_secundario', array("id" => "nome_secundario", "placeholder" => "Nome fantasia", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">IE</label>
                    <?php echo $this->Form->input('ie', array("id" => "ie", "placeholder" => "IE", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">CPF Responsável</label>
                    <?php echo $this->Form->input('cpf_responsavel', array("placeholder" => "CPF Responsável", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Responsável</label>
                    <?php echo $this->Form->input('responsavel', array("placeholder" => "Responsável", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <h3 class="mb-4">Endereço de Faturamento</h3>

            <div class="row">

                <div class="mb-7 col">
                    <label for="cep" class="form-label required">CEP</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                        <?php echo $this->Form->input('cep', array("id" => "cep", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Endereço</label>
                    <?php echo $this->Form->input('endereco', array("id" => "endereco", "placeholder" => "Endereço", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Número</label>
                    <?php echo $this->Form->input('numero', array("id" => "numero", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Complemento</label>
                    <?php echo $this->Form->input('complemento', array("id" => "complemento", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Cidade</label>
                    <?php echo $this->Form->input('cidade', array("id" => "cidade", "placeholder" => "Cidade", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Bairro</label>
                    <?php echo $this->Form->input('bairro', array("id" => "bairro", "placeholder" => "Bairro", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Estado</label>
                    <?php echo $this->Form->input('estado', array("id" => "estado", "placeholder" => "Estado", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <?php echo $this->Form->input('mesmo_endereco', array( "type" => "checkbox", "id" => "mesmo_endereco","div" => false, "label" => false, "class" => "form-check-input"));  ?>
                <label for="mesmo_endereco" class="form-check-label">Usar o mesmo endereço de faturamento na Entrega</label>
            </div>
            <br>

            <h3 class="mb-4">Endereço de Entrega</h3>

            <div class="row">

                <div class="mb-7 col">
                    <label for="cep" class="form-label required">CEP</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                        <?php echo $this->Form->input('cepentrega', array("id" => "cepentrega", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                    </div>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Endereço</label>
                    <?php echo $this->Form->input('enderecoentrega', array("id" => "enderecoentrega", "placeholder" => "Endereço", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Número</label>
                    <?php echo $this->Form->input('numeroentrega', array("id" => "numeroentrega", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Complemento</label>
                    <?php echo $this->Form->input('complementoentrega', array("id" => "complementoentrega", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                </div>

                <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Cidade</label>
                    <?php echo $this->Form->input('cidadeentrega', array("id" => "cidadeentrega", "placeholder" => "Cidade", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Bairro</label>
                    <?php echo $this->Form->input('bairroentrega', array("id" => "bairroentrega", "placeholder" => "Bairro", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Estado</label>
                    <?php echo $this->Form->input('estadoentrega', array("id" => "estadoentrega", "placeholder" => "Estado", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Telefone 1</label>
                    <?php echo $this->Form->input('telefone1', array("placeholder" => "Telefone Fixo", "class" => "form-control telefone mb-3 mb-lg-0")); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Ramal</label>
                    <?php echo $this->Form->input('ramal', array("placeholder" => "Ramal", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Telefone 2</label>
                    <?php echo $this->Form->input('telefone2', array("placeholder" => "Telefone Celular", "required" => false, "class" => "form-control telefone mb-3 mb-lg-0")); ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">E-mail Principal 1</label>
                    <?php echo $this->Form->input('email', array("placeholder" => "E-mail Principal 1", "type" => "text", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">E-mail</label>
                    <?php echo $this->Form->input('email1', array("placeholder" => "E-mail", "type" => "text", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Juros e multa?</label>
                    <?php echo $this->Form->input('cobrar_juros', array('options' => array('N' => 'Não', 'S' => 'Sim'), "data-control" => "select2", "empty" => "Selecione", "class" => "form-select mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Enviar email?</label>
                    <?php 
                    echo $this->Form->input('enviar_email', array('options' => array('0' => 'Não', '1' => 'Sim'),'data-control' => 'select2','empty' => 'Selecione','class' => 'form-select mb-3 mb-lg-0','default' => '1' ));  
                    ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Exibir Demanda Judicial?</label>
                    <?php 
                    echo $this->Form->input('exibir_demanda', array('options' => array('0' => 'Não', '1' => 'Sim'),'data-control' => 'select2','empty' => 'Selecione','class' => 'form-select mb-3 mb-lg-0','default' => '0' ));  
                    ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Cobrar taxa do boleto?</label>
                    <?php echo $this->Form->input('cobrar_taxa_boleto', array('options' => array('0' => 'Não', '1' => 'Sim'), "data-control" => "select2", 'empty' => 'Selecione', "class" => "form-select mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Emitir nota fiscal?</label>
                    <?php echo $this->Form->input('emitir_nota_fiscal', array('options' => array('N' => 'Não', 'S' => 'Sim', 'A' => 'Antecipada'), "data-control" => "select2", 'empty' => 'Selecione', "class" => "form-select mb-3 mb-lg-0",'default' => 'S'));  ?>
                </div>
            </div>

            <?php if ($is_admin) { ?>
                <div class="row">
                    <div class="mb-7 col-2">
                        <label class="form-label">SAldo Inicial</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <?php echo $this->Form->input('economia_inicial', ['type' => 'text', 'placeholder' => 'Economia inicial', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                        </div>
                    </div>

                    <div class="mb-7 col-2">
                        <label class="form-label">Data saldo inicial</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <?php echo $this->Form->input('dt_economia_inicial', ['type' => 'text', 'placeholder' => 'Data economia inicial', 'class' => 'form-control datepicker mb-3 mb-lg-0']); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="mb-7 col-2">
                    <label class="fw-semibold fs-6 mb-2">Elegível para gestão econômico</label>
                    <?php echo $this->Form->input('flag_gestao_economico', array('options' => array('N' => 'Não', 'S' => 'Sim'), "data-control" => "select2", 'empty' => 'Selecione', "class" => "form-select mb-3 mb-lg-0",'default' => 'S'));  ?>
                </div>

                <div class="mb-7 col-2">
                    <label class="form-label">Margem de segurança</label>
                    <div class="input-group">
                        <span class="input-group-text">%</span>
                        <?php echo $this->Form->input('porcentagem_margem_seguranca', ['type' => 'text', 'placeholder' => 'Margem de segurança', 'class' => 'form-control money_exchange mb-3 mb-lg-0','default' => '0']); ?>
                    </div>
                </div>

                <div class="mb-7 col-2">
                    <label class="form-label">Incluir qtde. mínina diária</label>
                    <?php echo $this->Form->input('qtde_minina_diaria', array("id" => "qtde_minina_diaria", "data-control" => "select2", "empty" => "Selecione", 'options' => array('2' => 'Sim', '0' => 'Não'), "class" => "form-select mb-3 mb-lg-0",'default' => '2'));  ?>
                </div>

                <div class="mb-7 col-2">
                    <label class="form-label">Tipos de GE</label>
                    <?php echo $this->Form->input('tipo_ge', array("id" => "tipo_ge", "data-control" => "select2", "empty" => "Selecione", 'options' => array('1' => 'Pré', '2' => 'Pós', '3' => 'Garantido'), "class" => "form-select mb-3 mb-lg-0",'default' => '2'));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Logo (Altura imagem: 60px)</label>
                    <div class="col-sm-5">
                        <?php echo $this->Form->input('logo', array("div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o logo"));  ?>
                        <?php if (isset($this->request->data["Customer"])): ?>
                            <br>
                            <a download href="<?php echo $this->base.'/files/customer/logo/'.$this->request->data["Customer"]["id"].'/'.$this->request->data["Customer"]["logo"] ?>"><?php echo $this->request->data["Customer"]["logo"] ?></a>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col-12">
                    <label class="fw-semibold fs-6 mb-2">Observação Notal fiscal</label>
                    <?php echo $this->Form->input('observacao_notafiscal', array("placeholder" => "Observação Notal fiscal", "id" => "summernote_notafiscal" , "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col-12">
                    <label class="fw-semibold fs-6 mb-2">Observações</label>
                    <?php echo $this->Form->input('observacao', array("placeholder" => "Observações", "id" => "summernote" , "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>


            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base; ?>/customers/" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
