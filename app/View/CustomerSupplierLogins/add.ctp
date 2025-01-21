<?php if ($tipo == 1) { ?>
    <?php echo $this->element("abas_customers", ['id' => $id]); ?>
<?php } else { ?>
    <?php echo $this->element("abas_suppliers", ['id' => $id]); ?>
<?php } ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerSupplierLogin', array("id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false])); ?>
            <?php if (isset($id)) { ?>
                <textarea name="log_old_value" style="display:none"><?php echo json_encode(['CustomerSupplierLogin' => $this->request->data['CustomerSupplierLogin']]); ?></textarea>
            <?php } ?>

            <div class="row">
                <?php if ($tipo == 1) { ?>
                    <input type="hidden" name="data[CustomerSupplierLogin][customer_id]" value="<?php echo $id ?>">

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Fornecedor</label>
                        <?php echo $this->Form->input('supplier_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="data[CustomerSupplierLogin][supplier_id]" value="<?php echo $id ?>">

                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Cliente</label>
                        <?php echo $this->Form->input('customer_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione"]);?>
                    </div>
                <?php } ?>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">URL</label>
                    <?php echo $this->Form->input('url', array("id" => "url", "placeholder" => "URL", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Login</label>
                    <?php echo $this->Form->input('login', array("id" => "login", "placeholder" => "Login", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>

                <div class="mb-7 col">
                    <label class="fw-semibold fs-6 mb-2">Senha</label>
                    <?php echo $this->Form->input('password', array("id" => "password", "placeholder" => "Senha", "class" => "form-control mb-3 mb-lg-0"));  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/customer_supplier_logins/index/'.$tipo.'/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '') ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>