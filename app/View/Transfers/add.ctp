<script type="text/javascript">
    $(document).ready(function() {
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Transfer', ["id" => "js-form-submit", "class" => "form-horizontal col-md-12", "action" => "/".$form_action."/", "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta origem</label>
                <?php echo $this->Form->input('bank_account_origin_id', ["empty" => "Selecione", "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Conta destino</label>
                <?php echo $this->Form->input('bank_account_dest_id', ["empty" => "Selecione", "data-control" => "select2", "class" => "form-select mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7">
                <label class="form-label">Valor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('value', ["type" => "text", "placeholder" => "Valor", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observação</label>
                <?php echo $this->Form->input('observation', ["placeholder" => "Observação", "class" => "form-control mb-3 mb-lg-0"]); ?>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/transfers'; ?>" class="btn btn-light-dark">Voltar</a>
                    <?php if ($form_action == 'edit') { ?>
                        <?php if ($this->request->data['Transfer']['status_id'] == 29) { ?>
                            <a href="<?php echo $this->base.'/transfers/aprovar/'.$id; ?>" class="btn btn-success">Aprovar</a>
                            <a href="<?php echo $this->base.'/transfers/reprovar/'.$id; ?>" class="btn btn-danger">Reprovar</a>
                        <?php } ?>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-success js-salvar" data-loading-text="Aguarde...">Salvar</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>