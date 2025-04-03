<?php
if (isset($id)) {
    $url = $this->here;
    echo $this->element("abas_suppliers", array('id' => $id, 'url' => $url));
}
?>
<?php echo $this->Html->script("html_editor/summernote", array('block' => 'script')); ?>
<?php echo $this->Html->script("html_editor/summernote-pt-BR", array('block' => 'script')); ?>
<?php echo $this->Html->css("html_editor/summernote", array('block' => 'css')); ?>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-7 py-3">
                <?php echo $this->Form->create('Supplier', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
                
                <div class="col-auto d-flex align-items-center">
                    <div class="form-check form-switch mb-0">
                        <label for="registro_cobranca" class="form-check-label ms-2">Registro Cobrança</label>

                        <?php echo $this->Form->input('registro_cobranca', array(
                            "type" => "checkbox",
                            "id" => "registro_cobranca",
                            "div" => false,
                            "label" => false,
                            "class" => "form-check-input"
                        )); ?>
                    </div>
                </div>
                <br>

                <!-- Campo para valor, oculto por padrão -->
                <div id="campo_valor" style="display: none;" class="mb-4 col-12">
                    <div class="row">
                    <div class="col-6">
                    <label for="valor">Quantidade</label>
                    <?php echo $this->Form->input('valor', [
                        "id" => "valor", 
                        "placeholder" => "Quantidade de Tempo ou Dias", 
                        "class" => "form-control mb-3 mb-lg-0", 
                        "style" => "margin-top: 6px;" 
                    ]); ?>
                </div>


                <div class="col-6">
                    <label for="unidade_tempo">Unidade de Tempo</label>
                    <?php
                        echo $this->Form->input('unidade_tempo', [
                            'type' => 'select', 
                            'options' => ['' => 'Selecione', 'hrs' => 'Horas', 'dias' => 'Dias'], 
                            'id' => 'unidade_tempo', 
                            'class' => 'form-select mt-2', 
                        ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Realiza gestão eficiente?</label>
                <?php 
                    echo $this->Form->select('realiza_gestao_eficiente', [1 => 'Sim', 0 => 'Não'], ["class" => "form-select mb-3 mb-lg-0","data-control" => "select2","empty" => "Selecione"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Modalidade</label>
                <?php echo $this->Form->input('Supplier.modalidade_id', ["class" => "form-select mb-3 mb-lg-0","data-control" => "select2","empty" => "Selecione","options" => $modalidades]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tecnologia</label>
                <?php echo $this->Form->input('Supplier.tecnologia_id', ["class" => "form-select mb-3 mb-lg-0","data-control" => "select2","empty" => "Selecione","options" => $tecnologias]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
                <?php echo $this->Form->input('tipo_pessoa', ["id" => "tipo_pessoa", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['2' => 'Jurídica', '1' => 'Física']]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Região</label>
                <?php echo $this->Form->input('regioes', ["id" => "regioes", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['1' => 'Norte', '2' => 'Nordeste', '3' => 'Centro-Oeste', '4' => 'Sudeste', '5' => 'Sul']]); ?>
            </div>


</div>




        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Razão social</label>
                <?php echo $this->Form->input('razao_social', ["id" => "nome_secundario", "placeholder" => "Razão social", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome fantasia</label>
                <?php echo $this->Form->input('nome_fantasia', ["id" => "nome_primario", "placeholder" => "Nome fantasia", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col-2">
                <label class="fw-semibold fs-6 mb-2">Tipo Repasse</label>
                <?php echo $this->Form->input('transfer_fee_type', ["options" => [1 => 'Valor', 2 => 'Percentual'], "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "type" => "select"]); ?>
            </div>

            <div class="mb-7 col-2">
                <label class="fw-semibold fs-6 mb-2">Repasse</label>
                <?php echo $this->Form->input('transfer_fee_percentage', ["placeholder" => "Repasse", "class" => "form-control mb-3 mb-lg-0 money_exchange", "type" => "text"]); ?>
            </div>


            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">CNPJ</label>
                    <?php echo $this->Form->input('documento', ["id" => "documento", "placeholder" => "CNPJ", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">RG</label>
                    <?php echo $this->Form->input('rg', ["id" => "rg", "placeholder" => "RG", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Inscrição Estadual</label>
                    <?php echo $this->Form->input('inscricao_estadual', ["id" => "ie", "placeholder" => "Inscrição Estadual", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Inscrição Municipal</label>
                    <?php echo $this->Form->input('inscricao_municipal', ["id" => "im", "placeholder" => "Inscrição Municipal", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Contato</label>
                    <?php echo $this->Form->input('contato', ["placeholder" => "Contato", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label for="cep" class="form-label">CEP</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                        <?php echo $this->Form->input('cep', ["id" => "cep", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                    </div>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Endereço</label>
                    <?php echo $this->Form->input('endereco', ["id" => "endereco", "placeholder" => "Endereço", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Número</label>
                    <?php echo $this->Form->input('numero', ["id" => "numero", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

               
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Complemento</label>
                    <?php echo $this->Form->input('complemento', ["id" => "complemento", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Bairro</label>
                    <?php echo $this->Form->input('bairro', ["id" => "bairro", "placeholder" => "Bairro", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Cidade</label>
                    <?php echo $this->Form->input('cidade', ["id" => "cidade", "placeholder" => "Cidade", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Estado</label>
                    <?php echo $this->Form->input('estado', ["id" => "estado", "placeholder" => "Estado", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Endereço Faturamento</label>
                    <?php echo $this->Form->input('enderecofaturamento', ["id" => "enderecofaturamento", "placeholder" => "Endereço", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Número</label>
                    <?php echo $this->Form->input('numerofaturamento', ["id" => "numerofaturamento", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Telefone comercial</label>
                    <?php echo $this->Form->input('tel_comercial', ["placeholder" => "Telefone comercial", "required" => false, "class" => "form-control mb-3 mb-lg-0 telefone"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Telefone residencial</label>
                    <?php echo $this->Form->input('tel_residencial', ["placeholder" => "Telefone residencial", "required" => false, "class" => "form-control mb-3 mb-lg-0 telefone"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Operadora</label>
                    <?php echo $this->Form->input('operadora', ['options' => ['OI' => 'OI', 'VIVO' => 'VIVO', 'TIM' => 'TIM', 'CLARO' => 'CLARO'], 'empty' => 'Selecione', "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Celular</label>
                    <?php echo $this->Form->input('celular', ["placeholder" => "Celular", "class" => "form-control telefone mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">E-mail</label>
                    <?php echo $this->Form->input('email', ["placeholder" => "E-mail", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Site</label>
                    <?php echo $this->Form->input('site', ["placeholder" => "Site", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">URL</label>
                    <div class="input-group">
                        <?php echo $this->Form->input('url', ["placeholder" => "URL", "class" => "form-control mb-3 mb-lg-0", "id" => "urlInput", "autocomplete" => "off"]);  ?>
                        <button class="btn btn-outline-secondary" type="button" id="copyButton">
                            <i class="fas fa-copy"></i>
                            <i class="fas fa-check" style="display: none;"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Login</label>
                    <?php echo $this->Form->input('login', ["placeholder" => "Login", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Senha</label>
                    <?php echo $this->Form->input('senha', ["placeholder" => "Senha", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">Tipo Conta</label>
                    <?php echo $this->Form->input('account_type_id', array("id" => "tipo_conta", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $bank_account_type)); ?>
                </div>

                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">Banco</label>
                    <?php echo $this->Form->input('bank_code_id', array("id" => "bank_name", "placeholder" => "Nome Banco", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $banks));  ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-7">
                    <?php $payment_method = ['1' => 'Boleto', '3' => 'Cartão de crédito', '6' => 'Crédito em conta corrente', '5' => 'Cheque', '4' => 'Depósito', '7' => 'Débito em conta', '8' => 'Dinheiro', '2' => 'Transferência', '9' => 'Desconto', '11' => 'Pix', '10' => 'Outros']; ?>
                    <label class="fw-semibold fs-6 mb-2">Forma de pagamento</label>
                    <?php echo $this->Form->input('payment_method', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => $payment_method]); ?>
                </div>

                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">Agência</label>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->Form->input('branch_number', array("id" => "agencia", "placeholder" => "Agência", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->Form->input('branch_digit', array("id" => "agencia_digito", "placeholder" => "Dígito", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2 required">Conta</label>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->Form->input('acc_number', array("id" => "conta", "placeholder" => "Conta", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->Form->input('acc_digit', array("id" => "conta_digito", "placeholder" => "Dígito", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-7">
                    <?php
                    $options = [
                        '' => 'Selecione',
                        'cnpj' => 'CNPJ',
                        'cpf' => 'CPF',
                        'email' => 'e-mail',
                        'celular' => 'celular',
                        'chave' => 'chave',
                        'qr code' => 'Qr code',
                        'aleatoria' => 'Aleatoria',

                    ];
                    ?>
                    <label class="fw-semibold fs-6 mb-2">Tipo Chave</label>
                    <?php echo $this->Form->input('pix_type', ['placeholder' => 'Tipo PIX', "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $options]); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2">Chave PIX</label>
                    <?php echo $this->Form->input('pix_id', ['type' => 'text', 'placeholder' => 'PIX', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                </div>

                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2">Valor Boleto</label>
                    <?php echo $this->Form->input('valor_boleto', ["placeholder" => "0,00", "class" => "form-control mb-3 mb-lg-0 money_exchange", "type" => "text"]); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2">Valor 1° Via</label>
                    <?php echo $this->Form->input('valor_1_via', ["placeholder" => "0,00", "class" => "form-control mb-3 mb-lg-0 money_exchange", "type" => "text"]); ?>
                </div>

                <div class="col-md-6 mb-7">
                    <label class="fw-semibold fs-6 mb-2">Valor 2° Via</label>
                    <?php echo $this->Form->input('valor_2_via', ["placeholder" => "0,00", "class" => "form-control mb-3 mb-lg-0 money_exchange", "type" => "text"]); ?>
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
                    <a href="<?php echo $this->base . '/suppliers' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
            </form>
        </div>
    </div>



    <script>
        function tipo_cliente() {
            if ($("#tipo_pessoa").val() == 1) {
                $("#documento").parent().find("label").text("CPF");
                $("#documento").attr('placeholder', "CPF");
                $("#nome_primario").parent().find("label").text("Nome");
                $("#nome_primario").attr('placeholder', "Nome");
                $("#nome_secundario").parent().hide();
                $("#documento").mask("999.999.999-99");
                $("#ie").parent().hide();
                $("#im").parent().hide();
                $("#rg").parent().show();
            } else {
                $("#documento").parent().find("label").text("CNPJ");
                $("#documento").attr('placeholder', "CNPJ");
                $("#nome_primario").parent().find("label").text("Nome Fantasia");
                $("#nome_primario").attr('placeholder', "Nome Fantasia");
                $("#nome_secundario").parent().show();
                $("#documento").mask("99.999.999/9999-99");
                $("#ie").parent().show();
                $("#im").parent().show();
                $("#rg").parent().hide();
            }
        }

        $(document).ready(function() {

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

        $("#cep").change(function() {
    var $el = $(this);
    var cep = $el.val().replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cep.length !== 8) {
        alert('Informe um CEP válido.');
        return;
    }

    $.ajax({
        url: 'https://viacep.com.br/ws/' + cep + '/json/',
        type: "get",
        dataType: "json",
        beforeSend: function() {
            $el.parent().find('span > i').removeClass('fas fa-map-marker');
            $el.parent().find('span > i').addClass('fas fa-spinner fa-spin');
        },
        success: function(data) {
            if ("erro" in data) {
                alert('CEP não encontrado.');
            } else {
                $("#endereco").val(data.logradouro);
                $("#bairro").val(data.bairro);
                $("#cidade").val(data.localidade);
                $("#estado").val(data.uf);
            }
        },
        error: function() {
            alert('Erro ao buscar o CEP. Tente novamente.');
        },
        complete: function() {
            $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
            $el.parent().find('span > i').addClass('fas fa-map-marker');
        }
    });
});


            tipo_cliente();


            $("#tipo_pessoa").change(function() {
                tipo_cliente();
            });

            $("#cep").mask("99999-999");

            $('.money_exchange').maskMoney({
                decimal: ',',
                thousands: '.',
                precision: 2
            });

            $(".telefone").focusout(function() {
                var phone, element;
                element = $(this);
                element.unmask();
                phone = element.val().replace(/\D/g, '');
                if (phone.length > 10) {
                    element.mask("(99) 99999-999?9");
                } else {
                    element.mask("(99) 9999-9999?9");
                }
            }).trigger('focusout');
        });

        document.getElementById('copyButton').addEventListener('click', function() {
            var urlInput = document.getElementById('urlInput');
            urlInput.select();
            document.execCommand('copy');

            // Mostrar o ícone de checkmark
            var checkIcon = this.querySelector('.fa-check');
            checkIcon.style.display = 'inline-block';

            // Tornar o botão verde temporariamente
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-success');

            // Reverter para o estado original após 1 segundo
            setTimeout(function() {
                checkIcon.style.display = 'none';
                document.getElementById('copyButton').classList.remove('btn-success');
                document.getElementById('copyButton').classList.add('btn-outline-secondary');
            }, 1000);
        });
        
        document.addEventListener('DOMContentLoaded', function() {
    const registroCobranca = document.getElementById('registro_cobranca');
    const campoValor = document.getElementById('campo_valor');
    const valorInput = document.getElementById('valor');
    const unidadeTempoSelect = document.getElementById('unidade_tempo');

    // Verifique o estado do campo de checkbox ao carregar a página
    if (registroCobranca.checked) {
        campoValor.style.display = 'block';
    } else {
        campoValor.style.display = 'none';
    }

    // Evento de mudança no checkbox
    registroCobranca.addEventListener('change', function() {
        if (this.checked) {
            campoValor.style.display = 'block';
        } else {
            campoValor.style.display = 'none';
            // Resetar os campos
            valorInput.value = '';  // Limpar o campo "Quantidade"
            unidadeTempoSelect.value = '';  // Definir "Selecione" como valor selecionado
        }
    });
});

    </script>
    