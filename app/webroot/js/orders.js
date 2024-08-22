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
    $('[data-kt-customer-table-filter="reset"]').on('click', function() {
        $("#t").val(null).trigger('change');
        $("#f").val(null).trigger('change');
        $("#de").val(null);
        $("#ate").val(null);
        $("#de_pagamento").val(null);
        $("#ate_pagamento").val(null);

        $("#busca").submit();
    });

    $('#q').on('change', function() {
        $("#busca").submit();
    });

    $('#modal_gerar_arquivo').on('show.bs.modal', function() {
        $('#customer_id').select2({
            dropdownParent: $('#modal_gerar_arquivo')
        });

        $('#clone_order_select').select2({
            dropdownParent: $('#modal_gerar_arquivo')
        });
    });

    $(".datepicker2").datepicker({
        startView: 1,
        minViewMode: 1,
        language: "pt-BR",
        format: 'mm/yyyy',
        autoclose: true
    });

    $(".duedate_datepicker").datepicker({
        format: 'dd/mm/yyyy',
        weekStart: 1,
        startDate: "today",
        orientation: "bottom auto",
        autoclose: true,
        language: "pt-BR",
        todayHighlight: true,
        toggleActive: true
    });
    $('.duedate_datepicker').mask('99/99/9999');

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

    $('.clone_order').on('change', function() {
        var val = $('.clone_order:checked').val();

        if (val == 1) {
            $("#clone_order_select").attr('required', true);
            $("#clone_order_select").parent().parent().removeClass('d-none');
            $(".div-new-order").addClass('d-none');
        } else {
            $("#clone_order_select").attr('required', false);
            $("#clone_order_select").parent().parent().addClass('d-none');
            $(".div-new-order").removeClass('d-none');
        }
    });

    $('input[name="data[working_days_type]"]').on('change', function() {
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

    $('input[name="data[is_consolidated]"]').on('change', function() {
        const isConsolidated1 = $(this).val() == 1;
        const isConsolidated2 = $(this).val() == 2;

        if (isConsolidated1) {
            $('.opcao_grupo_economico').hide();
        } else {
            $('.opcao_grupo_economico').show();
        }
    });

    $('input[name="data[is_beneficio]"]').on('change', function() {
        const isBeneficio1 = $(this).val() == 1;
        const isBeneficio2 = $(this).val() == 2;

        if (isBeneficio1) {
            $('.opcao_tipo_beneficio').hide();
        } else {
            $('.opcao_tipo_beneficio').show();
        }
    });

    $('#customer_id').on('change', function() {
        // load all grupo economico and beneficio by customer
        const customerId = $(this).val();
        $.ajax({
            url: base_url + '/orders/getEconomicGroupAndBenefitByCustomer',
            type: 'POST',
            responseType: 'json',
            data: {
                customer_id: customerId
            },
            success: function(data) {
                const obj = JSON.parse(data);
                const economicGroups = obj.economicGroups;
                const benefits = obj.benefits;

                // clear all options
                $('#grupo_selecionado').empty();

                // add new options
                $('#grupo_selecionado').append('<option value="">Todos</option>');

                economicGroups.forEach(function(economicGroup) {
                    $('#grupo_selecionado').append('<option value="' + economicGroup.EconomicGroup.id + '">' + economicGroup.EconomicGroup.name + '</option>');
                });
            }
        });
    });

    $("#customer_id").on("change", function() {
        var el = $(this);
        var source = $("#template_order").html();
        var template = Handlebars.compile(source);

        var customer_id = $(this).val();

        $.ajax({
            url: base_url + "/orders/getOrderByCustomer/" + customer_id,
            type: "post",
            dataType: "json",
            beforeSend: function(xhr) {
                $(".loading_img").remove();
                el.parent().append("<img src='" + base_url + "/img/loading.gif' class='loading_img'>");
            },
            success: function(data) {
                $(".loading_img").remove();
                var html_opt = "<option value=''>Selecione</option>";

                $.each(data, function(index, value) {
                    var context = {
                        name: value,
                        id: index
                    };
                    html_opt += template(context);
                });

                $("#clone_order_select").html(html_opt);
            }
        });
    });
})