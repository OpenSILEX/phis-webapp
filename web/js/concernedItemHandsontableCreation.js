//******************************************************************************
//                    concernedItemHandsontableCreation.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 15 Apr. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

var concernedItemUriColumnHeader;
var concernedItemUriColumnPlaceholder;
var handsontable;
var handsontableDiv;
var handsontableId;
            
loadCss('https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.css');
loadScript(
        'https://cdnjs.cloudflare.com/ajax/libs/handsontable/0.37.0/handsontable.full.min.js', 
        generateHandsontable);
document.querySelector('button.btn').on("click",function() {
    alert('jiij');
}); 
        
function generateHandsontable () {
    handsontableDiv = document.querySelector('#' + handsontableId);        
    handsontable = new Handsontable(handsontableDiv, {
        startRows: 1,
        dataSchema: {concerneditemsuris: null},
        colHeaders: [
            "<b>" + concernedItemUriColumnHeader + "</b>",
        ],
        columns: [
            {
                data: 'concerneditemsuris',
                type: 'text',
                required: true,
                placeholder: concernedItemUriColumnPlaceholder
            }
        ],
        rowHeaders: true,
        manualRowMove: true,
        manualColumnMove: true,
        contextMenu: true,
        filters: true
    });
}

function createHandsontableTextAreasForPost() {
    alert("jifggnfnfgnxfnji");
    handsontableDiv
            .getElementsByTagName('textarea')
            .name =  'EventCreation[concernedItemsUris[]]';
}

function loadScript(url, callback) {
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

function loadCss(url) {
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
            handsontableId = args[0];
            concernedItemUriColumnHeader = args[1];
            concernedItemUriColumnPlaceholder = args[2];  
        }
    };
}());