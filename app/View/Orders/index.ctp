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
                    <th>Desconto</th>
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
                            <td class="fw-bold fs-7 ps-4"><?php echo 'R$' . $data[$i]["Order"]["desconto"]; ?></td>
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

<div class="modal fade" id="modal_gerar_arquivo" role="dialog">
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
                        <?php echo $this->Form->input('customer_id', array("id" => "customer_id", "required" => false, 'label' => false, "class" => "form-select form-select-solid fw-bolder", "data-placeholder" => "Selecione", "data-allow-clear" => "true", "options" => $customers)); ?>
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
                        </div>
                        <p id="message_classification" style="color: red; margin: 0; display:none">Data do período inicial e agendamento deverá ser maior que hoje e maior que 5 dias úteis</p>
                    </div>
                    <div class="mt-10 mb-10">
                        <label class="mb-2">Utilizar Dias Úteis</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[working_days_type]" value="1" id="diasUteisChk1" checked="checked" />
                                    <label class="form-check-label" for="diasUteisChk1">
                                        Padrão
                                    </label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[working_days_type]" value="2" id="diasUteisChk2" />
                                    <label class="form-check-label" for="diasUteisChk2">
                                        Cadastro de Beneficiários
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="mb-7 col">
                        <label class="fw-semibold fs-6 mb-2 required">Dias Úteis</label>
                        <?php echo $this->Form->input('working_days', ["class" => "form-control mb-3 mb-lg-0", 'required' => true, 'div' => false, 'label' => false]); ?>
                        <p id="message_wd" style="color: red; margin: 0; display:none"></p>
                    </div>
                    <div class="mt-10">
                        <label class="mb-2">Criação de Pedidos</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[is_consolidated]" value="1" id="flexRadioChecked1" checked="checked" />
                                    <label class="form-check-label" for="flexRadioChecked1">
                                        Por Cliente
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[is_consolidated]" value="2" id="flexRadioChecked2" />
                                    <label class="form-check-label" for="flexRadioChecked2">
                                        Por Grupo Econômico
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <label class="mb-2">Pedido Parcial</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[is_partial]" value="2" id="flexRadioChecked2" checked="checked" />
                                    <label class="form-check-label" for="flexRadioChecked2">
                                        Todos beneficiários
                                    </label>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="data[is_partial]" value="1" id="flexRadioChecked1" />
                                    <label class="form-check-label" for="flexRadioChecked1">
                                        Sim
                                    </label>
                                </div>
                            </div>
                            
                        </div>
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

    $('#order_creation_form').on('submit', function(event) {
        const creditReleaseDateValue = $('#credit_release_date').val();
        const periodFromValue = $('#period_from').val();
        const periodToValue = $('#period_to').val();
        const workingDaysValue = $('#working_days').val();

        $('#message_wd').val('');
        $('#message_classification').val('');

        // Mostra uma mensagem de erro se algum dos campos estiver vazio
        if (!creditReleaseDateValue || !periodFromValue || !periodToValue) {
            $('#message_classification').text('Todos os campos de data devem ser preenchidos.').show();
            event.preventDefault();
            return; // Evita a execução adicional
        }

        if (workingDaysValue <= 0 && $('input[name="data[working_days_type]"]:checked').val() == 1) {
            $('#message_wd').text('Campo Dias Úteis deve ser maior que zero').show();
            event.preventDefault();
            return; // Evita a execução adicional
        }

        let currDate = new Date();
        currDate.setHours(0, 0, 0, 0); // reinicia a parte de tempo
        const futureDate = addWorkingDays(currDate, 4);

        // Converte os valores de string para objetos Date
        const creditReleaseDate = new Date(creditReleaseDateValue.split('/').reverse().join('-') + 'T00:00:00');
        const periodFromDate = new Date(periodFromValue.split('/').reverse().join('-') + 'T00:00:00');
        const periodToDate = new Date(periodToValue.split('/').reverse().join('-') + 'T00:00:00');

        // Verifica se period_to é posterior a period_from
        if (periodToDate <= periodFromDate) {
            $('#message_classification').text('A data "Até" deve ser posterior à data "De".').show();
            event.preventDefault();
            return; // Evita a execução adicional
        }

        // Verifica se creditReleaseDate é maior que hoje + 5 dias úteis
        if (creditReleaseDate < futureDate) {
            $('#message_classification').text('Data do período inicial e agendamento deverá ser maior que hoje e maior que 4 dias úteis.').show();
            event.preventDefault();
            return; // Evita a execução adicional
        }

        // Se todas as validações passarem, esconde a mensagem
        $('#message_classification').hide();
    });

    $(function() {
        $('input[name="data[working_days_type]"]').on('change', function() {
            alert(1);
            const isWorkingDaysType1 = $(this).val() == 1;
            const isWorkingDaysType2 = $(this).val() == 2;

            if (isWorkingDaysType2) {
                $('#working_days').val(0);
                $('#working_days').prop('readonly', true);
            } else {
                $('#working_days').val(0);
                $('#working_days').prop('readonly', false);
            }
        });
    });
</script>
