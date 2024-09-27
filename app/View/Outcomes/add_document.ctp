<?php
    $url = $this->base.'/outcomes/documents';
    echo $this->element("abas_outcomes", array('id' => $id, 'url' => $url));
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Docoutcome', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[Docoutcome][outcome_id]" value="<?php echo $id ?>">

            <div class="row">
                <!-- Campo Nome -->
                <div class="mb-7 col-md-6">
                    <label class="fw-semibold fs-6 mb-2">Nome</label>
                    <?php echo $this->Form->input('name', array("placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0", 'required' => true)); ?>
                </div>

                <!-- Campo Tipo do Documento -->
                <div class="mb-7 col">
    <label class="fw-semibold fs-6 mb-2">Tipo do Documento</label>
    <?php echo $this->Form->input('Docoutcome.tipo_documento_id', [
        'type' => 'select',
        'options' => $tiposDocumentos,
        'empty' => 'Selecione um tipo de documento',
        'class' => 'form-control mb-3 mb-lg-0',
        'required' => true
    ]); ?>
</div>

            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Documento</label>
                <div class="col-sm-5">
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento")); ?>
                    <?php if (isset($this->request->data["Docoutcome"])): ?>
                        <br>
                        <a download href="<?php echo $this->base.'/files/docoutcome/file/'.$this->request->data["Docoutcome"]["id"].'/'.$this->request->data["Docoutcome"]["file"] ?>"><?php echo $this->request->data["Docoutcome"]["file"] ?></a>
                    <?php endif ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/outcomes/documents/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
