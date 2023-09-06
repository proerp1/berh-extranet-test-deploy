function calculatePricePerDay() {
    // Get the values from the fields
    var unitPrice = $('#unit_price').val().replace('.', '');
    unitPrice = unitPrice.replace(',', '.');
    var quantity = $('#quantity').val();

    // Convert unitPrice to float
    unitPrice = parseFloat(unitPrice);

    // Calculate the price per day
    var pricePerDay = unitPrice * quantity;

    // Insert the calculated value into the price_per_day field
    var pricePerDayFormatted = pricePerDay.toFixed(2).replace('.', ',');
    $('#price_per_day').val(pricePerDayFormatted);
}

$(document).ready(function () {

    $('.money_field').maskMoney({
        decimal: ',',
        thousands: '.',
        precision: 2
    });

    // Listen to changes in unit_price and quantity fields
    $('#unit_price, #quantity').on('change', function () {
        calculatePricePerDay();
    });

    $('#benefit_id').on('change', function () {
        var benefitId = $(this).val();
        if (benefitId) {
            $.ajax({
                url: base_url + '/customer_users/getBenefitUnitPrice',
                type: 'POST',
                data: {
                    benefit_id: benefitId
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    $('#unit_price').val(data.unit_price);
                    calculatePricePerDay();
                }
            });
        }
    });
});