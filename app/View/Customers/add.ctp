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
            decimal: '.',
            thousands: '',
            precision: 2
        });

        $('.money_exchange').on("change", function(){
            var val = $(this).val();

            if (val > 100) {
                alert('O desconto não pode ser maior que 100%');
                $(this).val('');
            }
        });

        $("#cep").change(function() {
            var $el = $(this);
            
            $.ajax({
                url: 'https://api.postmon.com.br/v1/cep/' + $(this).val(),
                type: "get",
                beforeSend: function(){
                    $el.parent().find('span > i').removeClass('fas fa-map-marker');
                    $el.parent().find('span > i').addClass('fas fa-spinner fa-spin');
                },
                success: function(data){
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                    $("#endereco").val(data["logradouro"]);
                    $("#bairro").val(data["bairro"]);
                    $("#cidade").val(data["cidade"]);
                    $("#estado").val(data["estado"]);
                },
                error: function(){
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                    alert('Informe um CEP válido.');
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

        $("#CustomerCodFranquia").on("change", function(){
            var el = $(this);
            var source   = $("#template_cidade").html();
            var template = Handlebars.compile(source);

            var resale_id = $(this).val(); 

            $.ajax({
                url: base_url+"/customers/find_sellers/",
                type: "post",
                data: {resale_id: resale_id},
                dataType: "json",
                beforeSend: function(xhr){
                    $(".loading_img").remove();
                    el.parent().append("<img src='"+base_url+"/img/loading.gif' class='loading_img'>");
                },
                success: function(data){
                    $(".loading_img").remove();
                    var html_opt  = "<option value=''>Selecione</option>";

                    $.each(data, function(index, value) {
                      var context = {name: value.Seller.nome_fantasia, id: value.Seller.id};
                      html_opt    += template(context);
                    });

                    $("#CustomerSellerId").html(html_opt);
                }
            });
        });

        $("#cep").mask("99999-999");
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
            $("#documento").mask("999.999.999-99");
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
            $("#documento").mask("99.999.999/9999-99");
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
        <?php echo $this->Form->create('Customer', array("id" => "js-form-submit", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
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
                        <?php echo $this->Form->input('created', array("type" => "text", "required" => true, "placeholder" => "Data de Cadastro", "class" => "form-control datepicker mb-3 mb-lg-0"));  ?>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Vencimento</label>
                        <?php echo $this->Form->input('vencimento', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['05' => '05', '10' => '10', '15' => '15', '20' => '20', '25' => '25']]);?>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Revenda</label>
                    <?php echo $this->Form->input('cod_franquia', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Executivo</label>
                    <?php echo $this->Form->input('seller_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Taxa (%)</label>
                    <?php echo $this->Form->input('commission_fee_percentage', ["class" => "form-control mb-3 mb-lg-0", "Placeholder" => "Comissão"]);?>
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

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2 required">Telefone 1</label>
                    <?php echo $this->Form->input('telefone1', array("placeholder" => "Telefone 1", "class" => "form-control telefone mb-3 mb-lg-0")); ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Ramal</label>
                    <?php echo $this->Form->input('ramal', array("placeholder" => "Ramal", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Telefone 2</label>
                    <?php echo $this->Form->input('telefone2', array("placeholder" => "Telefone 2", "required" => false, "class" => "form-control telefone mb-3 mb-lg-0")); ?>
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

            <?php for ($i=0; $i < 1; $i++) { ?>
                <div class="row">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Operadora</label>
                        <?php echo $this->Form->input('operadora'.($i > 0 ? $i : ''), array('options' => array('OI' => 'OI', 'VIVO' => 'VIVO', 'TIM' => 'TIM', 'CLARO' => 'CLARO'), 'empty' => 'Selecione', "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0"));  ?>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Celular</label>
                        <?php echo $this->Form->input('celular'.($i > 0 ? $i : ''), array("placeholder" => "Celular", "class" => "form-control telefone mb-3 mb-lg-0"));  ?>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Juros e multa?</label>
                    <?php echo $this->Form->input('cobrar_juros', array('options' => array('N' => 'Não', 'S' => 'Sim'), "data-control" => "select2", "empty" => "Selecione", "class" => "form-select mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Enviar email?</label>
                    <?php echo $this->Form->input('enviar_email', array('options' => array('0' => 'Não', '1' => 'Sim'), "data-control" => "select2", "empty" => "Selecione", "class" => "form-select mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Cobrar taxa do boleto?</label>
                    <?php echo $this->Form->input('cobrar_taxa_boleto', array('options' => array('0' => 'Não', '1' => 'Sim'), "data-control" => "select2", 'empty' => 'Selecione', "class" => "form-select mb-3 mb-lg-0"));  ?>
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
