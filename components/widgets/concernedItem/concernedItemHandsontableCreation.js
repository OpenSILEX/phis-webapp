//******************************************************************************
//                    concernedItemHandsontableCreation.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

var handsonTableId;
var concernedItemUriColumnHeader;
var concernedItemUriColumnPlaceholder;

function generateHandsontable () {
    var hotElement = document.querySelector('#' + handsonTableId);        
    var handsontable = new Handsontable(hotElement, {
        startRows: 1,
        columns: [
            {
                data: 'concernedItemsUris',
                type: 'text',
                required: true,
                placeholder: concernedItemUriColumnPlaceholder
            }
        ],
        rowHeaders: true,
        colHeaders: [
            "<b>" + concernedItemUriColumnHeader + "</b>",
        ],
        manualRowMove: true,
        manualColumnMove: true,
        contextMenu: true,
        filters: true,
        dropdownMenu: true,
        cells: function(row, col, prop) {
            var cellProperties = {};

            return cellProperties;
        },
        afterGetColHeader: function (col, th) {
            if (col === 1 | col === 2 | col === 3 ) {
                th.style.color = "red";
            }
        }
    });
}

function loadScript (url, callback) {
    // Adding the script tag to the head
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    head.appendChild(script);
}  

function loadCss (url) {
    var cssId = 'handsontable-css';
    if (!document.getElementById(cssId))
    {
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.id   = cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = url;
        link.media = 'all';
        head.appendChild(link);
    }
}  
        
var CONCERNED_ITEM_HANDSONTABLE_CREATION = CONCERNED_ITEM_HANDSONTABLE_CREATION || (function(){

    return {
        init : function(args) {
            handsonTableId = args[0];
            concernedItemUriColumnHeader = args[1];
            concernedItemUriColumnPlaceholder = args[2];
            
            loadCss('https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css');
            loadScript('https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js', generateHandsontable);
        }
    };
}());