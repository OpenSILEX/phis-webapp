//******************************************************************************
//                         handsontable-input-widget.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
$("form").submit(function(e) {
    var inputsGroup  = $('#' + inputGroupDivId);
    inputsGroup.empty();
    for (var i = 0; i < handsonTable.countRows(); i++) {
        inputsGroup.append($("<input type=\"text\" name=\"" + inputName + "\" value=\"" + handsonTable.getDataAtCell(i, 0) + "\"></input>"));
    }
    return true;
});

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


