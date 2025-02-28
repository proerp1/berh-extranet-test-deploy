<?php
    $form_action = isset($economicGroupId) ? 'edit' : 'add';
    echo $this->element("abas_customers", array('id' => $id));
    
?>

<script type="text/javascript">
    
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
            $("#documento").mask("99.999.999/9999-99");


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
                <?php echo $this->Form->input('razao_social', array("id" => "razao_social", "placeholder" => "Razão social", "class" => "form-control mb-3 mb-lg-0"));  ?>
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


        <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base . '/economic_groups/index/'.$id.'/' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar">Salvar</button>
        </div>

        </form>
    </div>
</div>
