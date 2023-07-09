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
$url = $this->base . '/customers/users';
echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUser', ['id' => 'js-form-submit', 'action' => $form_action, 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[CustomerUser][customer_id]" value="<?php echo $id; ?>">
        <input type="hidden" name="query_string" value="<?php echo isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ['class' => 'form-select mb-3 mb-lg-0', 'data-control' => 'select2', 'empty' => 'Selecione']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ['placeholder' => 'Nome', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Email</label>
                <?php echo $this->Form->input('email', ['placeholder' => 'Email', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                <?php echo $this->Form->input('tel', ['placeholder' => 'Telefone', 'class' => 'form-control tel mb-3 mb-lg-0']); ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular</label>
                <?php echo $this->Form->input('cel', ['placeholder' => 'Celular', 'class' => 'form-control cel mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">CPF</label>
                <?php echo $this->Form->input('cpf', ['placeholder' => 'CPF', 'class' => 'form-control cpf mb-3 mb-lg-0']); ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">RG</label>
                <?php echo $this->Form->input('rg', ['placeholder' => 'RG', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Emissor</label>
                <?php echo $this->Form->input('emissor_rg', ['placeholder' => 'Emissor', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Estado Emissor</label>
                <?php echo $this->Form->input('emissor_estado', array("id" => "emissor_estado", "placeholder" => "Estado", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome da Mãe</label>
                <?php echo $this->Form->input('nome_mae', ['placeholder' => 'Nome da Mãe', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Sexo</label>
                <?php echo $this->Form->input('sexo', ['placeholder' => 'Sexo', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Data Nascimento</label>
                <?php echo $this->Form->input('data_nascimento', ['placeholder' => 'Data Nascimento', 'class' => 'form-control mb-3 mb-lg-0']); ?>
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
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Complemento</label>
                <?php echo $this->Form->input('complemento', array("id" => "complemento", "placeholder" => "Complemento", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Número</label>
                <?php echo $this->Form->input('numero', array("id" => "numero", "placeholder" => "Número", "required" => false, "class" => "form-control mb-3 mb-lg-0"));  ?>
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

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customers/users/' . $id . '/?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                <?php if (isset($this->request->data['CustomerUser'])) { ?>
                    <a href="javascript:" onclick="confirm('<h3>Deseja mesmo reenviar a senha?</h3>', '<?php echo $this->base . '/customers/reenviar_senha/' . $id . '/' . $user_id; ?>')" class="btn btn-warning"><i class="fa fa-retweet"></i> Reenviar senha</a>

                    <?php if (CakeSession::read('Auth.User.group_id') == 1) { ?>
                        <a href="javascript:" onclick="confirm('<h3>Antes de acessar a area do cliente, verifique se todas as sessões foram encerradas.</h3>', '<?php echo Configure::read('Areadoassociado.link') . 'users/bypass_login/' . $hash; ?>')" class="btn btn-primary"><i class="fa fa-key"></i> Bypass</a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        </form>
    </div>
</div>