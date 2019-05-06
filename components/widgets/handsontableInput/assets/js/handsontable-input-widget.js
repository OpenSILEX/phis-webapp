//******************************************************************************
//                         handsontable-input-widget.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
var form = document.querySelector('form');
form.onsubmit = function() {
    var inputsGroup  = document.querySelector('#' + inputGroupDivId);
    inputsGroup.innerHTML = '';
    var tds = document.querySelectorAll('.htCore td');
    tds.forEach(function(td) {
        var input = document.createElement('input');  
        input.setAttribute('name', inputName);
        input.setAttribute('value', td.innerHTML);
        inputsGroup.appendChild(input);
    });
};

window.onload = function() {
    var addRowButton = document.getElementById(addRowButtonId);
    var removeRowButton = document.getElementById(removeRowButtonId); 

    Handsontable.dom.addEvent(addRowButton, 'click', function () {
        handsonTable.alter('insert_row', handsonTable.countRows());
    });

    Handsontable.dom.addEvent(removeRowButton, 'click', function () {
        handsonTable.alter('remove_row', handsonTable.countRows() - 1);
    });
};


