<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('BancoPadrao', ["id" => "js-form-submit", "url" => ['controller' => 'banco_padrao', 'action' => 'index'], "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Banco</label>
                <?php echo $this->Form->input('bank_id', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "empty" => "Selecione", 'value' => $current['BancoPadrao']['bank_id']]);?>
            </div>

            <?php if ($current['BancoPadrao']['user_updated_id']) { ?>
                <div class="mb-2 col">
                    <label class="fw-semibold fs-6 mb-2">Última alteração: <?= $current['UserUpdated']['name'] ?> às <?= $current['BancoPadrao']['updated'] ?></label>
                </div>
            <?php } ?>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

        </form>
    </div>
</div>