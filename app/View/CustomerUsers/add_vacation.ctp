<?php
$url = $this->base . '/customers_users/bank_info';
echo $this->element('abas_customers', ['id' => $customer_id]);
if ($user_id) {
    echo $this->element('abas_customer_users', ['id' => $customer_id, 'user_id' => $user_id, 'url' => $url]);
}
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('CustomerUserVacation', ['id' => 'js-form-submit', 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[CustomerUserVacation][customer_user_id]" value="<?php echo $user_id; ?>">

        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">De</label>
                <?php echo $this->Form->input('start_date', array("type" => "text", "id" => "start_date", "placeholder" => "De", "required" => false, "autocomplete" => "one-time-code", "class" => "form-control datepicker mb-3 mb-lg-0"));  ?>
            </div>
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Até</label>
                <?php echo $this->Form->input('end_date', array("type" => "text", "id" => "end_date", "placeholder" => "Até", "required" => false, "autocompl1ete" => "one-time-code", "class" => "form-control datepicker mb-3 mb-lg-0"));  ?>
            </div>
        </div>

        <div class="mb-7">
            <div class="col-sm-offset-2 col-sm-9">
                <a href="<?php echo $this->base . '/customer_users/vacations/' . $user_id . '?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
                <button type="submit" class="btn btn-success js-salvar">Salvar</button>
            </div>
        </div>
        </form>
    </div>
</div>