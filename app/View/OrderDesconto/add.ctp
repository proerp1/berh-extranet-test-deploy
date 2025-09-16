<?php
    if (isset($order_id)) {
        $url = $this->here;
        echo $this->element("../Orders/_abas", array('id' => $order_id, 'url' => $url));
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('OrderDesconto', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>
        <input type="hidden" name="data[OrderDesconto][order_id]" value="<?= $order_id ?>">
        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Tipo</label>
              <?php
                $types = [
                    "Selecione",
                    "REEMBOLSO",
                    "ECONOMIA = CREDITA CONTA",
                    "AJUSTE = CREDITA E DEBITA",
                    "INCONSISTENCIA = SOMENTE CREDITA",
                    "SALDO",
                    "BOLSA DE CREDITO",
                    "CONTESTACAO GE = SOMENTE DEBITA",
                    "RECEITA DERIVADA = SOMENTE CREDITA)",
                ]
              ?>
              <?php echo $this->Form->input('tipo', ["class" => "form-select mb-3 mb-lg-0", "data-control" => "select2", "options" => $types]);?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2 required">Valor</label>
                <?php echo $this->Form->input('valor', ["placeholder" => "Valor", "required" => true, "class" => "form-control money_exchange", 'type' => "text", "step" => 0.01]);  ?>
            </div>


        </div>
        <div class="row">
            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Descrição</label>
              <?php echo $this->Form->input('descricao', ["placeholder" => "Descrição", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>
        </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base . '/order_desconto/index/'.$order_id ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php echo $this->Html->script('moeda', array('block' => 'script')); ?>
<script>
    $(document).ready(function(){
        $('.money_exchange').maskMoney({
            decimal: ',',
            thousands: '.',
            precision: 2
        });
    });
</script>