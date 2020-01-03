/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');
const $ = require('jquery');
require('bootstrap');
//require("@fortawesome/fontawesome-free");

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const Swal = require('sweetalert2');


// #region errorPopper
export function popError(data) {
    let resp = data.responseJSON;
    Swal.fire({
        title: "Erreur",
        text: resp.error !== undefined ? resp.error : "Erreur lors de la création de la tâche.",
        icon: 'error'
    });
}
// #endregion

// #region ajaxForm
$(document).on('submit','form',function(){
    let $t = $(this);
    $.ajax({
        url: $t.attr('action'),
        type: $t.attr('method'),
        data: $t.serialize(),
        dataType:'json',
        success:function(data){
            $("#modalForm").modal('hide');
            $(document).trigger($t.data('trigger'), $t.data('data'));
        },
        error:popError
    });
    return false;
});
// #endregion
export function doIt(url){
    $.ajax({
        url: url,
        type: 'GET',
        async: false,
        dataType:'json',
        error: popError
    });
    return false;
}