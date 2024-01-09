<?php
    $url = $this->base.'comunicados';
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Comunicado', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[Comunicado][comunicado_id]" value="<?php echo $id ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Categoria</label>
                <?php echo $this->Form->input('categorias_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Titulo</label>
                <?php echo $this->Form->input('titulo', array("placeholder" => "Titulo", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação</label>
                <?php echo $this->Form->input('observacao', ["id" => "nome_primario", "placeholder" => "Titulo do comunicado", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Documento</label>
                <div class="col-sm-5">
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                    <?php if (isset($this->request->data["Comunicado"])): ?>
                        <br>
                        <a download href="<?php echo $this->base.'/files/comunicado/file/'.$this->request->data["Comunicado"]["id"].'/'.$this->request->data["Comunicado"]["file"] ?>"><?php echo $this->request->data["Comunicado"]["file"] ?></a>
                    <?php endif ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/comunicados' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Enviar</button>
                </div>
            </div>
        </form>
    </div>
</div>