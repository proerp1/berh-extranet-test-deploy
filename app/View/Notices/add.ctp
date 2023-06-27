<script>
    $(document).ready(function() {
        $("#NoticeType").on('change', function(event) {
            var val = $(this).val();

            if (val == 1) {
                $("#NoticeDescription").parent().show();
                $("#NoticeFile").parent().parent().parent().hide();
            } else {
                $("#NoticeDescription").parent().hide();
                $("#NoticeFile").parent().parent().parent().show();
            }
        });

        if ($("#NoticeType").val() == 2) {
            $("#NoticeDescription").parent().hide();
            $("#NoticeFile").parent().parent().parent().show();
        }
    });
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Notice', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false], 'enctype' => 'multipart/form-data']); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Tipo</label>
                <?php echo $this->Form->input('type', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", "options" => [1 => "Texto", 2 => "Imagem"]]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Titulo</label>
                <?php echo $this->Form->input('title', ["placeholder" => "Titulo", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
                <?php echo $this->Form->input('description', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col" style="display: none;">
                <label class="fw-semibold fs-6 mb-2">Documento</label>
                <div class="col-sm-5">
                    <?php echo $this->Form->input('file', array("required" => false, "notEmpty" => true, "data-ui-file-upload" => true, "class" => "btn-primary", 'type' => 'file', "title" => "Escolha o documento"));  ?>
                    <?php if ($this->request->data["Notice"]["file"] != ''): ?>
                        <a href="<?php echo $this->base.'/files/notice/file/'.$this->request->data["Notice"]["id"].'/'.$this->request->data["Notice"]["file"] ?>" target="_blank">
                            <img class="img-thumbnail" loading="lazy" src="<?php echo $this->base.'/files/notice/file/'.$this->request->data["Notice"]["id"].'/'.$this->request->data["Notice"]["file"] ?>">
                        </a>
                    <?php endif ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/notices' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>