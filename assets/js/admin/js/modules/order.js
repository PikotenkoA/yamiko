'use strict';

window.addEventListener('load', () => {
    let container, amountElement;

    container = document.getElementsByClassName('js-update-amount').item(0).closest('.field-container');
    amountElement = document.querySelector('.js-amount');
    if (container) {
        container.addEventListener('input', (event) => {
            if (!event.target.classList.contains('js-update-amount')) {
                return;
            }

            let amount = 0;
            let rows = container.querySelectorAll('.sonata-ba-tbody tr');

            rows.forEach((row, index) => {
                let priceElement = row.querySelector('[name*=price]');
                let countElement = row.querySelector('[name*=count]');

                amount += priceElement.value * countElement.value;
            });
            amountElement.value = amount;
        })
    }
});