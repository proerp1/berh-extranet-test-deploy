<script type="text/javascript">
    $(document).ready(function() {
        $("#cep").mask("99999-999");
        $(".tel").mask("(99) 9999-9999");
        $(".cpf").mask("999.999.999-99");
        $(".cel").focusout(function() {
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
                    $('#estado').trigger('change.select2');
                },
                error: function(){
                    $el.parent().find('span > i').removeClass('fas fa-spinner fa-spin');
                    $el.parent().find('span > i').addClass('fas fa-map-marker');
                    alert('Informe um CEP válido.');
                }
            });
        });
    })
</script>

<?php
$url = $this->base . '/customers_users/index';
echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
if($user_id){
    echo $this->element('abas_customer_users', ['id' => $id, 'url' => $url]);
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUserAddress', ['id' => 'js-form-submit', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[CustomerUserAddress][customer_id]" value="<?php echo $id; ?>">
        <input type="hidden" name="data[CustomerUserAddress][customer_user_id]" value="<?php echo $id; ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Tipo Endereço</label>
                <?php echo $this->Form->input('address_type_id', array("id" => "tipo_endereço", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $address_type)); ?>
            </div>
            <div class="mb-7 col">
                <label for="cep" class="form-label required">CEP</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                    <?php echo $this->Form->input('zip_code', array("id" => "cep", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Endereço</label>
                <?php echo $this->Form->input('address_line', array("id" => "endereco", "placeholder" => "Endereço", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Número</label>
                <?php echo $this->Form->input('address_number', array("id" => "numero", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Complemento</label>
                <?php echo $this->Form->input('address_complement', array("id" => "complemento", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Cidade</label>
                <?php echo $this->Form->input('city', array("id" => "cidade", "placeholder" => "Cidade", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Bairro</label>
                <?php echo $this->Form->input('neighborhood', array("id" => "bairro", "placeholder" => "Bairro", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Estado</label>
                <?php echo $this->Form->input('state', array("id" => "estado", "placeholder" => "Estado", "required" => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", 'options' => $estados)); ?>
            </div>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customer_users/addresses/' . $id . '/'.$user_id.'/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>