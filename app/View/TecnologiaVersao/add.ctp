<?php
echo $this->element("abas_tecnologia", ['id' => $tecnologia_id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('TecnologiaVersao', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Nome</label>
                    <?php echo $this->Form->input('nome', ["placeholder" => "Nome", 'required' => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Tipo</label>
                    <?php echo $this->Form->input('tipo', ["placeholder" => "Nome", 'required' => true, 'empty' => 'Selecione', "class" => "form-control select2 mb-3 mb-lg-0", 'options' => ['cadastro' => 'Cadastro', 'credito' => 'Credito']]);  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Header</label>
                    <?php echo $this->Form->input('header', ["placeholder" => "Header", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>
            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Extensão Arquivo</label>
                    <?php echo $this->Form->input('extensao_arquivo', ["placeholder" => "Extensão Arquivo", 'required' => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Encoding</label>
                    <?php echo $this->Form->input('encoding', ["placeholder" => "Encoding", 'required' => true, "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Separador</label>
                    <?php echo $this->Form->input('separador', ["placeholder" => "Separador", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>
            <div class="row">
                <div class="mb-7 col-3">
                    <label class="fw-semibold fs-6 mb-2">Tamanho</label>
                    <?php echo $this->Form->input('campo_tamanho', ["id" => "campo_tamanho", "placeholder" => "Tamanho", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col-3">
                    <label class="fw-semibold fs-6 mb-2">Formato</label>
                    <?php echo $this->Form->input('campo_formato', ["id" => "campo_formato", "placeholder" => "Formato", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
                <div class="mb-7 col-4">
                    <label class="fw-semibold fs-6 mb-2">Campos Disponíveis</label>
                    <?php echo $this->Form->input('select_campo', ["id" => "select_campo", "placeholder" => "Nome", 'empty' => 'Selecione para adicionar ao campo ao lado', "class" => "form-control select2 mb-3 mb-lg-0", 'options' => $fields]);  ?>
                </div>
                <div class="mb-7 col-2">
                    <button type="button" class="btn btn-info w-100 js-add-campo mt-8">Adicionar Campo</button>
                </div>
            </div>
            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Campos</label>
                    <?php echo $this->Form->input('campos', ["id" => "campos", "placeholder" => "Campos", "class" => "form-control mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/tecnologia_versao/index/'.$tecnologia_id ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
    	</form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(".js-add-campo").on('click', function () {
            const novo_campo = $('#select_campo').val();
            const novo_campo_tamanho = $('#campo_tamanho').val();
            const novo_campo_formato = $('#campo_formato').val();

            if (!novo_campo) return

            let campos_val = $('#campos').val();
            campos_val += `${novo_campo}|${novo_campo_tamanho}|${novo_campo_formato};`
            $("#campos").val(campos_val);

            $('#select_campo').val('');
            $('#campo_tamanho').val('');
            $('#campo_formato').val('');
        })
    })
</script>