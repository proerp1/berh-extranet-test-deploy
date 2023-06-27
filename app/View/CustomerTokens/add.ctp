<?php
    echo $this->element("abas_customers", ['id' => $id]);
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerToken', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if (isset($id)) { ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(['CustomerToken' => $this->request->data['CustomerToken']]); ?></textarea>
            <?php } ?>
            <input type="hidden" name="data[CustomerToken][customer_id]" value="<?php echo $id ?>">

            <div class="mb-7">
                <label for="cep" class="form-label">Data de expiração</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <?php echo $this->Form->input('expire_date', array("type" => "text", "placeholder" => "Data de expiração", "class" => "form-control datepicker mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Status</label>
                <?php echo $this->Form->input('status_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customer_tokens/index/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

            <?php if (isset($token_id) and CakeSession::read("Auth.User.group_id") == 1): ?>
                <i>Alterado por <?php echo $this->request->data['UsuarioAlteracao']['name'] ?> em <?php echo date('d/m/Y H:i:s', strtotime($this->request->data['CustomerToken']['updated'])) ?></i>
            <?php endif ?>
        </form>
    </div>
</div>