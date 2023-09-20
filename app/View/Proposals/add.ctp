<?php
    echo $this->element('abas_customers', ['id' => $id]);
    ?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Proposal', ['id' => 'js-form-submit', 'action' => $form_action, 'method' => 'post', 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

            <div class="row">
                <div class="mb-7 col-4">
                    <label class="form-label">Data da proposta</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <?php echo $this->Form->input('date', ['type' => 'text', 'placeholder' => 'Data da proposta', 'class' => 'form-control datepicker mb-3 mb-lg-0']); ?>
                    </div>
                </div>

                <div class="mb-7 col-4">
                    <label class="form-label">Data da previsão de fechamento</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <?php echo $this->Form->input('expected_closing_date', ['type' => 'text', 'placeholder' => 'Data da previsão de fechamento', 'class' => 'form-control datepicker mb-3 mb-lg-0']); ?>
                    </div>
                </div>

                <div class="mb-7 col-4">
                    <label class="form-label">Data do fechamento</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <?php echo $this->Form->input('closing_date', ['type' => 'text', 'placeholder' => 'Data do fechamento', 'class' => 'form-control datepicker mb-3 mb-lg-0']); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6 mb-7">
                    <div style="background-color: #f3f3f3;border-radius: 10px;padding: 10px;">
                        <h3>Vale Transporte</h3>

                        <div class="row">
                            <div class="mb-7 col-4">
                                <label class="form-label">Taxa administrativa</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('transport_adm_fee', ['type' => 'text', 'placeholder' => 'Taxa administrativa', 'class' => 'form-control percent_format mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Taxa de entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('transport_deli_fee', ['type' => 'text', 'placeholder' => 'Taxa de entrega', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">PGE*</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('management_feel', ['type' => 'text', 'placeholder' => 'PGE', 'class' => 'form-control percent_format mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="fw-semibold fs-6 mb-2">Qtde de Colaboradores</label>
                                <?php echo $this->Form->input('transport_workers_qty', ['placeholder' => 'Qtde de Colaboradores', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Valor por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('transport_workers_price', ['type' => 'text', 'placeholder' => 'Valor por colaborador', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Total por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('transport_workers_price_total', ['type' => 'text', 'readonly' => true, 'placeholder' => 'Total por colaborador', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <i>*PGE Participação Gestão Eficiente</i>
                        </div>
                    </div>
                </div>

                <div class="col-6 mb-7">
                    <div style="background-color: #f3f3f3;border-radius: 10px;padding: 10px;">
                        <h3>Vale Refeição/Alimentação</h3>

                        <div class="row">
                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa administrativa</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('meal_adm_fee', ['type' => 'text', 'placeholder' => 'Taxa administrativa', 'class' => 'form-control percent_format mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa de entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('meal_deli_fee', ['type' => 'text', 'placeholder' => 'Taxa de entrega', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="fw-semibold fs-6 mb-2">Qtde de Colaboradores</label>
                                <?php echo $this->Form->input('meal_workers_qty', ['placeholder' => 'Qtde de Colaboradores', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Valor por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('meal_workers_price', ['type' => 'text', 'placeholder' => 'Valor por colaborador', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Total por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('meal_workers_price_total', ['type' => 'text', 'readonly' => true, 'placeholder' => 'Total por colaborador', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-6 mb-7">
                    <div style="background-color: #f3f3f3;border-radius: 10px;padding: 10px;">
                        <h3>Vale Combustível</h3>

                        <div class="row">
                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa administrativa</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('fuel_adm_fee', ['type' => 'text', 'placeholder' => 'Taxa administrativa', 'class' => 'form-control percent_format mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa de entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('fuel_deli_fee', ['type' => 'text', 'placeholder' => 'Taxa de entrega', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="fw-semibold fs-6 mb-2">Qtde de Colaboradores</label>
                                <?php echo $this->Form->input('fuel_workers_qty', ['placeholder' => 'Qtde de Colaboradores', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Valor por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('fuel_workers_price', ['type' => 'text', 'placeholder' => 'Valor por colaborador', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Total por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('fuel_workers_price_total', ['type' => 'text', 'readonly' => true, 'placeholder' => 'Total por colaborador', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 mb-7">
                    <div style="background-color: #f3f3f3;border-radius: 10px;padding: 10px;">
                        <h3>Cartão Multi</h3>

                        <div class="row">
                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa administrativa</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <?php echo $this->Form->input('multi_card_adm_fee', ['type' => 'text', 'placeholder' => 'Taxa administrativa', 'class' => 'form-control percent_format mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-6">
                                <label class="form-label">Taxa de entrega</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('multi_card_deli_fee', ['type' => 'text', 'placeholder' => 'Taxa de entrega', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="fw-semibold fs-6 mb-2">Qtde de Colaboradores</label>
                                <?php echo $this->Form->input('multi_card_workers_qty', ['placeholder' => 'Qtde de Colaboradores', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Valor por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('multi_card_workers_price', ['type' => 'text', 'placeholder' => 'Valor por colaborador', 'class' => 'form-control money_exchange mb-3 mb-lg-0']); ?>
                                </div>
                            </div>

                            <div class="mb-7 col-4">
                                <label class="form-label">Total por colaborador</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <?php echo $this->Form->input('multi_card_workers_price_total', ['type' => 'text', 'readonly' => true, 'placeholder' => 'Total por colaborador', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-7 col-4">
                    <label class="form-label">Total geral</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <?php echo $this->Form->input('total_price', ['type' => 'text', 'readonly' => true, 'placeholder' => 'Total geral', 'class' => 'form-control mb-3 mb-lg-0']); ?>
                    </div>
                </div>
            </div>

            <div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/proposals/index/'.$id.'/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-light-dark">Voltar</a>
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

        $('.percent_format').maskMoney({
            decimal: '.',
            thousands: '',
            precision: 2
        });

        $("#ProposalTransportWorkersQty, #ProposalTransportWorkersPrice").on("change", function () {
            calculateTotal("#ProposalTransportWorkersQty", "#ProposalTransportWorkersPrice", "#ProposalTransportWorkersPriceTotal");
        });

        $("#ProposalMealWorkersQty, #ProposalMealWorkersPrice").on("change", function () {
            calculateTotal("#ProposalMealWorkersQty", "#ProposalMealWorkersPrice", "#ProposalMealWorkersPriceTotal");
        });

        $("#ProposalFuelWorkersQty, #ProposalFuelWorkersPrice").on("change", function () {
            calculateTotal("#ProposalFuelWorkersQty", "#ProposalFuelWorkersPrice", "#ProposalFuelWorkersPriceTotal");
        });

        $("#ProposalMultiCardWorkersQty, #ProposalMultiCardWorkersPrice").on("change", function () {
            calculateTotal("#ProposalMultiCardWorkersQty", "#ProposalMultiCardWorkersPrice", "#ProposalMultiCardWorkersPriceTotal");
        });

        $("#ProposalTransportWorkersPriceTotal, #ProposalMealWorkersPriceTotal, #ProposalFuelWorkersPriceTotal, #ProposalMultiCardWorkersPriceTotal").on("change", function () {
            var total = parseFloat(transformPrice($('#ProposalTransportWorkersPriceTotal').val())) + parseFloat(transformPrice($('#ProposalMealWorkersPriceTotal').val())) + parseFloat(transformPrice($('#ProposalFuelWorkersPriceTotal').val())) + parseFloat(transformPrice($('#ProposalMultiCardWorkersPriceTotal').val()));

            $('#ProposalTotalPrice').val(formata_dinheiro(total, 2, ',', '.'));
        });
    });

    function calculateTotal(qtyInputId, priceInputId, totalInputId) {
        var qty = parseInt(transformPrice($(qtyInputId).val()));
        var price = parseFloat(transformPrice($(priceInputId).val()));

        var total = 0;
        if (qty > 0 && price > 0) {
            total = qty * price;
        }

        $(totalInputId).val(formata_dinheiro(total, 2, ',', '.'));
        $(totalInputId).change();
    }

    function transformPrice(price)
    {
        if (price != '') {
            var newPrice = replaceAll(price, ".", "");
            newPrice = replaceAll(newPrice, ",", ".");

            return newPrice;
        }

        return 0;
    }
</script>