<script>
    var total_pedidos = [<?php echo $totalReceivedRaw; ?>];
    var total_desconto = [<?php echo $totalDiscountRaw; ?>];
    var mes_atual = '<?php echo date('m/Y'); ?>';
</script>
<style>
    #kt_charts_widget_6 .apexcharts-xaxis {display: none}
</style>
<?php
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/index.js', array('inline' => false));
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/xy.js', array('inline' => false));
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/themes/Animated.js', array('inline' => false));
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/percent.js', array('inline' => false));
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/radar.js', array('inline' => false));
    echo $this->Html->script('https://cdn.amcharts.com/lib/5/themes/Animated.js', array('inline' => false));
    echo $this->Html->script('widget/charts/widget-evolucao-pedidos.js', array('inline' => false));
    // echo $this->Html->script('widget/charts/widget-radar-dash.js', array('inline' => false));
    echo $this->Html->script('widget/charts/widget-ranking-operadoras.js', array('inline' => false));
    echo $this->Html->script('widget/charts/widget-gestao-eficiente.js', array('inline' => false));
    echo $this->Html->script('widget/charts/widget-10.js', array('inline' => false));
?>
<style type="text/css">
    .er-count {
        font-size: 20px !important;
    }
</style>

<form action="" role="form" id="busca" autocomplete="off">
    <div class="card mb-xl-10">
        <div class="card-body">
            <div class="row g-5 g-xl-10">
                <div class="col-3">
                    <label class="form-label fs-5 fw-bold mb-3">Data:</label>
                    <div class="input-group input-daterange" id="datepicker">
                        <input class="form-control" id="de" name="de" value="<?php echo isset($_GET["de"]) ? $_GET["de"] : ""; ?>" autocomplete="off">
                        <span class="input-group-text" style="padding: 5px;"> até </span>
                        <input class="form-control" id="ate" name="ate" value="<?php echo isset($_GET["ate"]) ? $_GET["ate"] : ""; ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Filtrar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row g-5 g-xl-10 mb-xl-10">
    <div class="col-xl-4 mb-5 mb-xl-0">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7 mb-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Gestão eficiente</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-6">Demonstrativo de resultado</span>
                </h3>
            </div>
            <div class="card-body d-flex justify-content-between flex-column">
                <div class="d-flex flex-wrap d-grid gap-5 mb-10">
                    <div class="border-end-dashed border-1 border-gray-300 pe-xxl-7 me-xxl-5">
                        <div class="d-flex mb-2">
                            <span class="fs-4 fw-bold text-gray-400 me-1">R$</span>
                            <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2"><?php echo $totalReceived; ?></span>
                        </div>
                        <span class="fs-6 fw-bold text-gray-400">Valor dos itens</span>
                    </div>
                    <div class="m-0">
                        <div class="d-flex align-items-center mb-2">
                            <span class="fs-4 fw-bold text-gray-400 align-self-start me-1">R$</span>
                            <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2"><?php echo $totalDiscount; ?></span>
                            
                        </div>
                        <span class="fs-6 fw-bold text-gray-400">Economia prevista</span>
                    </div>
                </div>
                <div id="kt_apexcharts_gestao_eficiente" class="w-100 h-300px"></div>
                
            </div>
        </div>
    </div>
    <div class="col-xxl-8">
        <div class="card card-flush overflow-hidden h-md-100">
            <div class="card-header py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-dark">Evolução de pedidos</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-6">Demonstrativo</span>
                </h3>
            </div>
            <div class="card-body pt-4">
                <div id="kt_charts_widget_23" class="h-400px w-100"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-5 g-xl-10 mb-xl-10">
    <div class="col-xl-8 mb-5 mb-xl-10">
        <div class="card card-flush overflow-hidden h-md-100">
            <div class="card-header py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-dark">Evolução de economia</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-6">Demonstrativo</span>
                </h3>
            </div>
            <div class="card-body pt-4">
                <div id="chart_economia" class="h-400px w-100"></div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card card-flush h-lg-100">
            <div class="card-header py-7 mb-3">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Ranking Operadoras</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-6">Demonstrativo</span>
                </h3>
            </div>
            <div class="card-body py-0 ps-6 mt-n12">
                <div id="kt_charts_widget_6"></div>
            </div>
        </div>
    </div>
</div>