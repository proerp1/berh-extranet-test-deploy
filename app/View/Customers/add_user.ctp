<script type="text/javascript">
    $(document).ready(function(){
        $(".tel").mask("(99) 9999-9999");
        $(".cpf").mask("999.999.999-99");
        $(".cel").focusout(function(){
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
    })
</script>

<?php
    $url = $this->base.'/customers/users';
    echo $this->element('abas_customers', ['id' => $id, 'url' => $url]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUser', ['id' => 'js-form-submit', 'action' => $form_action, 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <input type="hidden" name="data[CustomerUser][customer_id]" value="<?php echo $id; ?>">
            <input type="hidden" name="query_string" value="<?php echo $_SERVER['QUERY_STRING']; ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ['class' => 'form-select mb-3 mb-lg-0', 'data-control' => 'select2', 'empty' => 'Selecione']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', ['placeholder' => 'Nome', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Email</label>
                <?php echo $this->Form->input('email', ['placeholder' => 'Email', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <?php if (isset($this->request->data['CustomerUser'])) { ?>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Usuário</label>
                    <?php echo $this->Form->input('username', ['disabled' => true, 'placeholder' => 'Usuário', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                </div>
            <?php } ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                <?php echo $this->Form->input('tel', ['placeholder' => 'Telefone', 'class' => 'form-control tel mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Celular</label>
                <?php echo $this->Form->input('cel', ['placeholder' => 'Celular', 'class' => 'form-control cel mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">CPF</label>
                <?php echo $this->Form->input('cpf', ['placeholder' => 'CPF', 'class' => 'form-control cpf mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Filial - Serasa</label>
                <?php echo $this->Form->input('filial', ['placeholder' => 'Filial - Serasa', 'class' => 'form-control mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Acessar negativação?</label>
                <?php echo $this->Form->input('acessar_negativacao', ['options' => ['N' => 'Não', 'S' => 'Sim'], 'data-control' => 'select2', 'empty' => 'Selecione', 'class' => 'form-select mb-3 mb-lg-0']); ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/users/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                    <?php if (isset($this->request->data['CustomerUser'])) { ?>
                        <a href="javascript:" onclick="confirm('<h3>Deseja mesmo reenviar a senha?</h3>', '<?php echo $this->base.'/customers/reenviar_senha/'.$id.'/'.$user_id; ?>')" class="btn btn-warning"><i class="fa fa-retweet"></i> Reenviar senha</a>

                        <?php if (CakeSession::read('Auth.User.group_id') == 1) { ?>
                            <a href="javascript:" onclick="confirm('<h3>Antes de acessar a area do cliente, verifique se todas as sessões foram encerradas.</h3>', '<?php echo Configure::read('Areadoassociado.link').'users/bypass_login/'.$hash; ?>')" class="btn btn-primary"><i class="fa fa-key"></i> Bypass</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>
