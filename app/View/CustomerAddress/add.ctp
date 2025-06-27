<?php
echo $this->Html->script("html_editor/summernote", array('block' => 'script'));
echo $this->Html->script("html_editor/summernote-pt-BR", array('block' => 'script'));
echo $this->Html->css("html_editor/summernote", array('block' => 'css'));
if (isset($customer_id)) {
    $url = $this->here;
    echo $this->element("abas_customers", array('id' => $customer_id, 'url' => $url));
}
?>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerAddress', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ["id" => "name", "placeholder" => "Nome", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>
        </div>
        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]); ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Usuário/Beneficiário</label>
                <?php echo $this->Form->input('customer_user_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => $customer_user_ids]); ?>
            </div>

            <div class="mb-7 col">
                <label for="zip_code" class="form-label">CEP</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                    <?php echo $this->Form->input('zip_code', ["id" => "zip_code", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
                </div>
            </div>


        </div>

        <div class="row">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Endereço</label>
                <?php echo $this->Form->input('address_line', ["id" => "address_line", "placeholder" => "Endereço", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Número</label>
                <?php echo $this->Form->input('address_number', ["id" => "address_number", "placeholder" => "Número", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Complemento</label>
                <?php echo $this->Form->input('address_complement', ["id" => "address_complement", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>
        </div>

        <div class="row">


            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Bairro</label>
                <?php echo $this->Form->input('neighborhood', ["id" => "neighborhood", "placeholder" => "Bairro", "required" => false, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cidade</label>
                <?php echo $this->Form->input('city', ["id" => "city", "placeholder" => "Cidade", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Estado</label>
                <?php echo $this->Form->input('state', ["id" => "state", "placeholder" => "Estado", "required" => true, "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col-12">
                <label class="fw-semibold fs-6 mb-2">Observações</label>
                <?php echo $this->Form->input('obs', array("placeholder" => "Observações", "id" => "summernote", "class" => "form-control mb-3 mb-lg-0")); ?>
            </div>
        </div>


        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customer_address/index/' . $customer_id ?>"
                   class="btn btn-light-dark">Voltar</a>
                <button type="submit" id="salvar-btn" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('#summernote').summernote({
            lang: 'pt-BR',
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize', 'fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['group', ['video', 'link', 'picture', 'hr']],
                ['misc', ['codeview', 'undo', 'redo']],
                ['help', ['help']],
            ]
        });

        $("#zip_code").change(function () {
            var $el = $(this);
            var cep = $el.val().replace(/\D/g, ''); // Remove caracteres não numéricos
            $("#salvar-btn").attr('disabled', true);

            if (cep.length !== 8) {
                alert('Informe um CEP válido.');
                return;
            }

            $.ajax({
                url: 'https://viacep.com.br/ws/' + cep + '/json/',
                type: "get",
                dataType: "json",
                beforeSend: function () {
                    $el.parent().find('span > i').removeClass('fas fa-map-marker');
                    $el.parent().find('span > i').addClass('fas fa-spinner fa-spin');
                },
                success: function (data) {
                    if ("erro" in data) {
                        alert('CEP não encontrado.');
                    } else {
                        $("#address_line").val(data.logradouro);
                        $("#neighborhood").val(data.bairro);
                        $("#city").val(data.localidade);
                        $("#state").val(data.uf);
                        $("#salvar-btn").attr('disabled', false);
                    }
                },
                error: function () {
                    alert('Erro ao buscar o CEP. Tente novamente.');
                },
                complete: function () {
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                }
            });
        });

        $("#zip_code").mask("99999-999");
    });
</script>
    