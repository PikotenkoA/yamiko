'use strict';

let addToCartButtons;

addToCartButtons = document.querySelectorAll('.js-add-to-cart');

addToCartButtons.forEach((button)=>{
    button.addEventListener('click', (event)=>{
        event.preventDefault();

        fetch(button.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
            })
            .then((response)=> {
                return response.text();
            })
            .then((body)=> {
                document.getElementById('header-cart').innerHTML =body;
            })
    });

});

//addToCartButtons.forEach(function (button){
//
// });
