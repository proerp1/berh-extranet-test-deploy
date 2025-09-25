<!-- Campo hidden para passar as condições para o JavaScript -->
<input type="hidden" id="conditions-data" value="<?php echo $conditionsJson; ?>">

<div class="row mb-xl-5">
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2 total-value" id="subtotal-value">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Subtotal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2 total-value" id="repasse-value">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Repasse</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2 total-value" id="taxa-value">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Taxa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2 total-value" id="total-value">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-lg-100">
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <div class="m-0">
                    <img alt="Icone" src="<?php echo $this->base."/img/basketball.svg" ?>" style="height: 2.5rem !important; width: 2.5rem !important;" />
                </div>

                <div class="d-flex flex-column my-7">
                    <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2 total-value" id="economia-value">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Carregando economia...</span>
                        </div>
                    </span>
                    <div class="m-0">
                        <span class="fw-bold fs-6 text-gray-400">Economia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        showTotalsLoading();
        loadTotals();
    });

    function showTotalsLoading() {
        $('.total-value').html('<div class="spinner-border spinner-border-sm" role="status"></div>');
    }

    function loadTotals() {
        $.ajax({
            url: '/reports/getTotalOrders',
            method: 'POST',
            data: {
                conditions: $('#conditions-data').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateBasicTotalsDisplay(response.totals);
                    
                    loadEconomia();
                } else {
                    showTotalsError();
                }
            },
            error: function() {
                showTotalsError();
            }
        });
    }

    function loadEconomia() {
        $('#economia-value').html('<div class="spinner-border spinner-border-sm text-warning" role="status"><span class="sr-only">Calculando economia...</span></div>');
        
        $.ajax({
            url: '/reports/getTotalEconomia',
            method: 'POST',
            data: {
                conditions: $('#conditions-data').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#economia-value').text(formatMoney(response.economia));
                } else {
                    $('#economia-value').html('<span class="text-danger">Erro</span>');
                }
            },
            error: function() {
                $('#economia-value').html('<span class="text-danger">Erro ao calcular</span>');
            }
        });
    }

    function updateBasicTotalsDisplay(totals) {
        $('#subtotal-value').text(formatMoney(totals.subtotal));
        $('#repasse-value').text(formatMoney(totals.transfer_fee));
        $('#tpp-value').text(formatMoney(totals.total_tpp));
        $('#taxa-value').text(formatMoney(totals.commission_fee));
        $('#desconto-value').text(formatMoney(totals.desconto));
        $('#total-value').text(formatMoney(totals.total));
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value || 0);
    }

    function showTotalsError() {
        $('.total-value').html('<span class="text-danger">Erro ao carregar</span>');
    }
</script>