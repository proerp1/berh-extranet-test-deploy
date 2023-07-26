<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-0 py-3">
        <?php echo $this->element("aba_orders"); ?>
    </div>
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Order', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Cliente</label>
                <input type="text" name="" id="" class="form-control">
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Período</label>
                <input type="text" name="" id="" class="form-control">
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Dias Úteis</label>
                <input type="text" name="" id="" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Observações</label>
                <textarea name="" id="" cols="30" rows="10" class="form-control"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="mb-7 col">
                <input type="submit" value="Salvar" class="btn btn-success">
            </div>
        </div>

        

        </form>
    </div>

</div>