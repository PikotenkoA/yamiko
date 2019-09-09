'use strict';

window.addEventListener('load', () => {
    let container;
    let attributeValues = null;

    // получаем таблицу с атрибутами
    container = document.querySelector('.form-group[id$="_attributeValues"]');

    if (container) { // Выполняем, только если на странице есть таблица с атрибутами
        initAttributeValues(); // сохраняем все возможные значения атрибутов

        $(document).on('sonata.add_element', function() { // При добавлении нового атрибута
            $('.js-product-attribute').trigger('change'); // вызываем событие изменения атрибута для фильтрации фозможных значений
        });

        $(container).on('change', (event) => { // На событие change
            if (event.target.tagName === 'SELECT' // если элемент select
                && event.target.classList.contains('js-product-attribute' // и в class есть js-product-attribute
                )) {
                filterAttributeValues(event.target); // фильтруем список значений атрибута
            }
        });
    }

    function initAttributeValues() { // получаем список возможных значений
        let result = [];
        let valuesSelect = container.querySelector('select.js-product-attribute-value'); // Ищем селект со значенями атрибутов

        if (!valuesSelect) { // если не найден
            $(document).on('sonata.add_element', function() { // при добавлении нового атрибута
                if (attributeValues === null) {
                    initAttributeValues(); // запускаем получение списка возможных значений
                }
            });

            return;
        }

        valuesSelect.options.forEach((option) => { // для каждой опции селекта
            result.push({ // сохраняем ее параметры в массив
                value: option.value,
                label: option.innerHTML,
                attributeId: option.dataset.attributeId
            });
        });

        attributeValues = result; // сохраняем в переменной
    }

    function filterAttributeValues(attributeSelect) { // фильтрация значений атрибутов
        let attributeId = attributeSelect.selectedOptions.item(0).value; // получает айди текущего атрибута
        let row = attributeSelect.closest('tr'); // ищем строку, в которой находится текущий атрибут и его значения
        let valueSelect = row.querySelector('select.js-product-attribute-value'); // находим селект со значениями

        valueSelect.innerHTML = ''; // очищаем список опций селекта

        attributeValues.forEach((item) => { // для каждого из возможных значений атрибутов
            if (item.attributeId === attributeId) { // если айди атриьута у значения совпадает с выбранным
                let option = document.createElement('option'); // создаем новый option
                option.value = item.value;
                option.innerHTML = item.label;
                valueSelect.add(option); // добавляем в select
            }
        });

        $(valueSelect).trigger('change'); // запускаем собітие изменения селекта для обновления опций в select2
    }

});