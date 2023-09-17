<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Supplier', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tipo de pessoa</label>
                <?php echo $this->Form->input('tipo_pessoa', ["id" => "tipo_pessoa", "class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'options' => ['2' => 'Jurídica', '1' => 'Física']]); ?>
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

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Repasse</label>
                <?php echo $this->Form->input('transfer_fee_percentage', ["placeholder" => "Repasse", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>
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

        $("#cep").change(function() {
            var $el = $(this);

            $.ajax({
                url: 'https://api.postmon.com.br/v1/cep/' + $(this).val(),
                type: "get",
                beforeSend: function() {
                    $el.parent().find('span > i').removeClass('fas fa-map-marker');
                    $el.parent().find('span > i').addClass('fas fa-spinner fa-spin');
                },
                success: function(data) {
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                    $("#endereco").val(data["logradouro"]);
                    $("#bairro").val(data["bairro"]);
                    $("#cidade").val(data["cidade"]);
                    $("#estado").val(data["estado"]);
                },
                error: function() {
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                    alert('Informe um CEP válido.');
                }
            });
        });

        tipo_cliente();

        $("#tipo_pessoa").change(function() {
            tipo_cliente();
        });

        $("#cep").mask("99999-999");
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
</script>