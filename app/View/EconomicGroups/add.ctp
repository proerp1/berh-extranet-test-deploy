<?php
    $form_action = isset($economicGroupId) ? 'edit' : 'add';
    echo $this->element("abas_customers", array('id' => $id));
    
?>

<script type="text/javascript">
    $(document).ready(function(){
        // Máscara para o campo de Documento
        $("#documento").mask("99.999.999/9999-99");
        $("#cep").mask("99999-999");
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
    });
   


</script>


<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('EconomicGroup', ['id' => 'js-form-submit', 'url' => ['action' => $form_action, $id, isset($economicGroupId) ? $economicGroupId : ''], 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', array("id" => "name", "placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">CNPJ</label>
                <?php echo $this->Form->input('document', array("id" => "documento", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Razão social</label>
                <?php echo $this->Form->input('nome_primario', array("id" => "nome_primario", "placeholder" => "Razão social", "class" => "form-control mb-3 mb-lg-0"));  ?>
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

        <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base . '/economic_groups/index/'.$id.'/' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar">Salvar</button>
        </div>

        </form>
    </div>
</div>
