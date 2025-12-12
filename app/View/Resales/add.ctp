<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: '.',
            thousands: '',
            precision: 2
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

        $("#cep").mask("99999-999");
        $("#documento").mask("99.999.999/9999-99");
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
</script>

<?php
    if (isset($id)) {
        echo $this->element("abas_resales", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Resale', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <?php if (isset($id)) { ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(array('Resale' => $this->request->data['Resale'])); ?></textarea>
            <?php } ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Tipo</label>
                    <?php echo $this->Form->input('tipo', array("id" => "tipo", "data-control" => "select2", "empty" => "Selecione", 'options' => array('1' => 'Revenda', '2' => 'Parceiro', '3' => 'Executivo'), "class" => "form-select mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Tipo Pessoa</label>
                    <?php echo $this->Form->input('tipo_pessoa', array("id" => "tipo", "data-control" => "select2", "empty" => "Selecione", 'options' => array('1' => 'Física', '2' => 'Jurídica'), "class" => "form-select mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta bancária</label>
                <?php echo $this->Form->input('bank_account_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Razão social</label>
                <?php echo $this->Form->input('razao_social', ["id" => "nome_secundario", "placeholder" => "Razão social", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome fantasia</label>
                <?php echo $this->Form->input('nome_fantasia', ["id" => "nome_primario", "placeholder" => "Nome fantasia", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">E-mail</label>
                <?php echo $this->Form->input('email', ["placeholder" => "E-mail", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">CNPJ</label>
                <?php echo $this->Form->input('cnpj', ["id" => "documento", "placeholder" => "CNPJ", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Inscrição Estadual</label>
                <?php echo $this->Form->input('inscricao_estadual', ["id" => "ie", "placeholder" => "Inscrição Estadual", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Inscrição Municipal</label>
                <?php echo $this->Form->input('inscricao_municipal', ["id" => "im", "placeholder" => "Inscrição Municipal", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">CEP</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                    <?php echo $this->Form->input('cep', ["id" => "cep", "required" => false, "placeholder" => "CEP", "class" => "form-control mb-3 mb-lg-0"]);  ?>
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
                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                <?php echo $this->Form->input('telefone', ["placeholder" => "Telefone", "required" => false, "class" => "form-control telefone mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular</label>
                <?php echo $this->Form->input('celular', ["placeholder" => "Celular", "class" => "form-control telefone mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular 2</label>
                <?php echo $this->Form->input('celular2', ["placeholder" => "Celular 2", "class" => "form-control telefone mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Site</label>
                <?php echo $this->Form->input('site', ["placeholder" => "Site", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Vencimento</label>
                <?php echo $this->Form->input('vencimento_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <label for="cep" class="form-label">Valor recebido por cliente</label>
                <div class="input-group">
                    <?php echo $this->Form->input('valor_recebido_cliente', ["type" => "text", "placeholder" => "Valor recebido por cliente", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação</label>
                <?php echo $this->Form->input('observation', ['id' => 'observation', 'class' => 'form-control']); ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/resales' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

        </form>
    </div>
</div>