<?php $url_novo = $this->base . "/benefits/add/"; ?>
<div class="card mb-5 mb-xl-8">
    <form action="<?php echo $this->Html->url(array("controller" => "orders", "action" => "index")); ?>" role="form" id="busca" autocomplete="off">
        <div class="card-header border-0 pt-6 pb-6">
            <div class="card-title">
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <span class="position-absolute ms-6">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control form-control-solid ps-15" id="q" name="q" value="<?php echo isset($_GET["q"]) ? $_GET["q"] : ""; ?>" placeholder="Buscar" />
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <a href="#" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modal_gerar_arquivo">
                        <i class="fas fa-file"></i>
                        Novo Pedido
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-0 py-3">
        <?php echo $this->element("pagination"); ?>
        <br>
        <div class="table-responsive">
            <?php echo $this->element("table"); ?>
            <thead>
                <tr class="fw-bolder text-muted bg-light">
                    <th class="ps-4 w-250px min-w-250px rounded-start">Status</th>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Período</th>
                    <th>Subtotal</th>
                    <th>Repasse</th>
                    <th>Taxa</th>
                    <th>Total</th>
                    <th>Usuário</th>
                    <th>Data de criação</th>
                    <th class="w-200px min-w-200px rounded-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data) { ?>
                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                        <tr>
                            <td class="fw-bold fs-7 ps-4">
                                <span class='badge <?php echo $data[$i]["Status"]["label"] ?>'>
                                    <?php echo $data[$i]["Status"]["name"] ?>
                                </span>
                            </td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["id"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Customer"]["nome_primario"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["Order"]["order_period_from"] . ' - ' . $data[$i]["Order"]["order_period_to"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["subtotal"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["transfer_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["commission_fee"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["total"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]["CustomerCreator"]["name"] != '' ? $data[$i]["CustomerCreator"]["name"] : $data[$i]["Creator"]["name"]; ?></td>
                            <td class="fw-bold fs-7 ps-4"><?php echo $data[$i]['Order']['created'] ?></td>
                            <td class="fw-bold fs-7 ps-4">
                                <a href="<?php echo $this->base . '/orders/edit/' . $data[$i]["Order"]["id"]; ?>" class="btn btn-info btn-sm">
                                    Editar
                                </a>
                                <?php if ($data[$i]["Status"]["id"] == '83') { ?>
                                    <a href="javascript:" onclick="verConfirm('<?php echo $this->base . '/orders/delete/' . $data[$i]["Order"]["id"]; ?>');" rel="tooltip" title="Excluir" class="btn btn-danger btn-sm">
                                        Excluir
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td class="fw-bold fs-7 ps-4" colspan="12">Nenhum registro encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
        <?php echo $this->element("pagination"); ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[data-kt-customer-table-filter="reset"]').on('click', function() {
            $("#t").val(null).trigger('change');
            $("#q").val(null);

            $("#busca").submit();
        });

        $('#q').on('change', function() {
            $("#busca").submit();
        });

        $(".datepicker2").datepicker({
            startView: 1,
            minViewMode: 1,
            language: "pt-BR",
            format: 'mm/yyyy',
            autoclose: true
        });
    });
</script>

<div class="modal fade" tabindex="-1" id="modal_gerar_arquivo" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gerar Pedido</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form autocomplete="off" action="<?php echo $this->base . '/orders/createOrder' ?>" id="order_creation_form" class="form-horizontal" method="post">
                <input autocomplete="off" name="hidden" type="text" style="display:none;">
                <div class="modal-body">
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Cliente</label>
                        <?php echo $this->Form->input('customer_id', array("id" => "customer_positions_id", "required" => false, 'label' => false, "class" => "form-select form-select-solid fw-bolder", "data-kt-select2" => "true", "data-placeholder" => "Selecione", "data-allow-clear" => "true", "options" => $customers)); ?>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Período</label>
                        <div class="input-group">
                            <div class="input-daterange input-group" id="datepicker">
                                <input class="form-control" id="period_from" role="presentation" autocomplete="off" name="period_from">
                                <span class="input-group-text" style="padding: 5px;"> até </span>
                                <input class="form-control" id="period_to" role="presentation" autocomplete="off" name="period_to">
                            </div>
                        </div>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2">Agendamento do crédito previsto</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <?php echo $this->Form->input('credit_release_date', ["type" => "text", "class" => "form-control mb-3 mb-lg-0 datepicker", 'div' => false, 'label' => false]);  ?>
                            <p id="message_classification" style="color: red; margin: 0; display:none">Data do período inicial e agendamento deverá ser maior que hoje e maior que 5 dias úteis</p>
                        </div>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                        <?php echo $this->Form->input('working_days', ["class" => "form-control mb-3 mb-lg-0", 'required' => true, 'div' => false, 'label' => false]); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Gerar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addWorkingDays(startDate, daysToAdd) {
        let endDate = startDate;
        while (daysToAdd > 0) {
            endDate.setDate(endDate.getDate() + 1); // Move to next day
            if (endDate.getDay() !== 0 && endDate.getDay() !== 6) { // If it's not a weekend
                daysToAdd--; // Decrement the days to add
            }
        }
        return endDate;
    }

    $(document).ready(function() {
        $('#order_creation_form').on('submit', function(event) {

            const inputValue = $('#credit_release_date').val();
            const initalInputValue = $('#period_from').val();

            let currDate = new Date();
            currDate.setHours(0, 0, 0, 0); // reset the time part
            const futureDate = addWorkingDays(currDate, 5);

            if (inputValue != '') {
                $('#message_classification').hide();

                // Convert inputValue to a Date object and today's date to a Date object
                const inputDate = new Date(inputValue.split('/').reverse().join('-') + 'T00:00:00');
                const periodInitialDate = new Date(initalInputValue.split('/').reverse().join('-') + 'T00:00:00');

                // Check if dates is greater than today +5 working days
                if (inputDate < futureDate || periodInitialDate < futureDate) {
                    $('#message_classification').show();
                    event.preventDefault();
                }
            }

        });
    });
</script>