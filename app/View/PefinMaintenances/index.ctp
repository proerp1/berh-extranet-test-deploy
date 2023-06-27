<script type="text/javascript">
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    })
</script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('PefinMaintenance', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="mb-7">
                <label class="form-label">Valor</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <?php echo $this->Form->input('value', ["type" => "text", "placeholder" => "Valor", "class" => "form-control money_exchange mb-3 mb-lg-0"]);  ?>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>

        </form>
    </div>
</div>