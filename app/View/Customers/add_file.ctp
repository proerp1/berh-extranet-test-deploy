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
                    <?php echo $this->Form->input('status_id', [
                        "class" => "form-select mb-3 mb-lg-0",
                        "data-control" => "select2",
                        "empty" => "Selecione",
                        "onchange" => "handleStatusChange(this.value)"
                    ]); ?>
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
                            <a download href="<?php echo $this->webroot.'files/customer_file/file/'.$this->request->data["CustomerFile"]["id"].'/'.$this->request->data["CustomerFile"]["file"] ?>"><?php echo $this->request->data["CustomerFile"]["file"] ?></a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Pedido</label>
                    <?php echo $this->Form->input('order_id', [
                        "type" => "number",
                        "class" => "form-control mb-3 mb-lg-0",
                        "placeholder" => "Digite o pedido",
                        "pattern" => "[0-9]*",
                        "inputmode" => "numeric",
                        "id" => "order_id"
                    ]); ?>
                    <div id="order_id_error" class="text-danger" style="display:none;">O campo Pedido é obrigatório.</div>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Motivo</label>
                    <?php echo $this->Form->input('motivo', [
                        "type" => "text",
                        "class" => "form-control mb-3 mb-lg-0",
                        "placeholder" => "Digite o motivo",
                        "id" => "motivo"
                    ]); ?>
                    <div id="motivo_error" class="text-danger" style="display:none;">O campo Motivo é obrigatório.</div>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customers/files/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar" onclick="return validateForm()">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function handleStatusChange(statusId) {
    const orderIdField = document.getElementById('order_id');
    const motivoField = document.getElementById('motivo');
    const orderIdError = document.getElementById('order_id_error');
    const motivoError = document.getElementById('motivo_error');

    orderIdError.style.display = 'none';
    motivoError.style.display = 'none';

    // Sempre desbloqueia o campo "Pedido" ao mudar de status
    orderIdField.disabled = false;

    if (statusId == 101) { // Concluído
        orderIdField.required = true;
        motivoField.required = true;
    } else if (statusId == 102) { // Cancelado
        orderIdField.disabled = true;
        motivoField.required = true;
    } else {
        orderIdField.required = false;
        motivoField.required = false;
    }
}

function validateForm() {
    const statusId = document.querySelector('select[name="data[CustomerFile][status_id]"]').value;
    const orderIdField = document.getElementById('order_id');
    const motivoField = document.getElementById('motivo');
    const orderIdError = document.getElementById('order_id_error');
    const motivoError = document.getElementById('motivo_error');

    let isValid = true;
    orderIdError.style.display = 'none';
    motivoError.style.display = 'none';

    if (statusId == 101) { // Concluído
        if (!orderIdField.value) {
            orderIdError.style.display = 'block';
            isValid = false;
        }
        if (!motivoField.value) {
            motivoError.style.display = 'block';
            isValid = false;
        }
    }

    if (statusId == 102) { // Cancelado
        if (!motivoField.value) {
            motivoError.style.display = 'block';
            isValid = false;
        }
    }

    return isValid; // Permite o envio do formulário
}
</script>
