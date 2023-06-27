<?php
    $url = $this->base.'/customers/documents';
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Document', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[Document][customer_id]" value="<?php echo $id ?>">

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Nome</label>
                <?php echo $this->Form->input('name', array("placeholder" => "Nome", "class" => "form-control mb-3 mb-lg-0"));  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Documento</label>
                <div class="col-sm-5">
                    <?php echo $this->Form->input('file', array("div" => false, "label" => false, "required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                    <?php if (isset($this->request->data["Document"])): ?>
                        <br>
                        <a download href="<?php echo $this->base.'/files/document/file/'.$this->request->data["Document"]["id"].'/'.$this->request->data["Document"]["file"] ?>"><?php echo $this->request->data["Document"]["file"] ?></a>
                    <?php endif ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/documents/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>