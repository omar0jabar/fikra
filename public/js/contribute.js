const inputAmount = $('#contributor_contributionAmount');
const paymentMethod = $('input[id^=contributor_chosenPayment]');
const amountDebited = $('#amount-debited');
const inputAmountDebited = $('#input-amount-debited');

function countAmountDebited() {
    let amount = parseInt(inputAmount.val());
    let method = parseFloat($('input[id^=contributor_chosenPayment]:checked').val());
    let hiddenAmount = 0;
    let result = "0 MAD";
    if (amount && amount != 'undefined' && amount != null && !isNaN(amount)) {
        if (method && method != 'undefined' && method != null) {
            result = amount * method / 100;
            result = amount + result;
            hiddenAmount = result
            result = result + " MAD";
        } else {
            hiddenAmount = amount
            result = amount + " MAD";
        }
    }
    amountDebited.html(result);
    inputAmountDebited.val(hiddenAmount)
}

inputAmount.on("change", function () {
    countAmountDebited()
})

inputAmount.on("keyup", function () {
    countAmountDebited();
})

paymentMethod.on("change", function () {
    countAmountDebited();
})