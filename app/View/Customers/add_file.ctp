<?php
    $url = $this->base.'/customers/files';
    echo $this->element("abas_customers", array('id' => $id, 'url' => $url));
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerFile', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[CustomerFile][customer_id]" value="<?php echo $id ?>">

            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Status</label>
                    <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Layout</label>
                    <div class="col-sm-5">
                        <br>
                        <p><?php echo $this->request->data["Layout"]["name"] ?></p>
                    </div>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Arquivo</label>
                    <div class="col-sm-5">
                        <?php if (isset($this->request->data["CustomerFile"])): ?>
                            <br>
                            <a download href="<?php echo 'https://cliente.berh.com.br/files/customer_file/file/'.$this->request->data["CustomerFile"]["id"].'/'.$this->request->data["CustomerFile"]["file"] ?>"><?php echo $this->request->data["CustomerFile"]["file"] ?></a>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/files/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
