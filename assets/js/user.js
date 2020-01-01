import {popError} from "./app";
import {doIt} from "./app";

//Affichage de la liste des taches
function userDrawList(url){
    $.ajax({
        url: url,
        type: 'GET',
        dataType:'json',
        success: function (data) {
            userGenTable(data);
        },
        error: popError
    });
    return false;
}
//récuperations de liste
$(document).on('getlistUser', function (data, userid) {
    return userDrawList("/api/v1/user/");
});
//suppression d'une tache
$(document).on('click', '.delete-user-btn', function () {
    if(confirm('La suppression d\'un utilisateur et de ses taches est définitive, voulez vous continuer ?')) {
        let $t = $(this);
        let url = $t.attr('href');
        doIt(url);
        userDrawList("/api/v1/user/")
    }
    return false;
});

// récuperation du formulaire d'ajout d'une tache
$(document).on('click', '.add-user-btn', function () {
    let $t = $(this);
    let $modal =$("#modalForm");
    $.ajax({
        url: "/user/new",
        dataType:'html',
        type:'get',
        success:function(data){
            $modal.find('.modal-title').html("Nouvel utilisateur");
            $modal.find('.modal-body').html(data);
            $modal.modal("show");
        },
        error: function(){
            Swal.fire("Impossible de charger le formulaire");
        }
    });
    return false;
});

// récuperation du formulaire d'édition d'une tache
// $(document).on('click', '.edit-user-btn', function () {
//     let $t = $(this);
//     let $modal =$("#modalForm");
//
//     $.ajax({
//         url: "/user/"+$t.data('task')+"/edit",
//         dataType:'html',
//         type:'get',
//         success:function(data){
//             $modal.find('.modal-title').html("Modifier une tache");
//             $modal.find('.modal-body').html(data);
//             $modal.modal("show");
//         },
//         error: function(){
//             Swal.fire("Impossible de charger le formulaire");
//         }
//     });
//     return false;
// });

// generation du contenu de la table
function userGenTable(data) {
    let usersTable = $("#users tbody");
    usersTable.find('tr').remove();
    if (data !== undefined && data.length > 0) {
        for (let i = 0; i < data.length; i++) {
            let user = data[i];
            usersTable.append(userTemplateLine(user));
        }
    } else {
        usersTable.append(userEmptyTemplate());
    }
}

// generation d'un ligne contenant le resultat si aps d'objets retournés.
function userEmptyTemplate() {
    return "<tr><td colspan='4'>Aucun utilisateur pour le moment</td></tr>";
}
//generation du template d'une ligne
function userTemplateLine(user) {
    return "<tr>" +
        "<td>" + user.id + "</td>" +
        "<td>" + user.name + "</td>" +
        "<td>" + user.email + "</td>" +
        "<td>" +
        "<a href='/api/v1/tast/"+user.id+"' class='getList btn btn-sm btn-secondary' title='Voir les taches'><i class='fas fa-list'></i></a>"+
        "<a data-user='"+ user.id +"' href=\"#\" class=\"add-task-btn btn btn-sm btn-success\" title=\"Ajouter une tache\"><i class=\"fas fa-plus\"></i></a>"+
        "<a class='btn btn-sm btn-danger delete-user-btn'  href='/api/v1/user/delete/"+user.id+"' title='Supprimer un utilisateur et ses taches'><i class='fas fa-trash'></i></a>" +
        "</td>" +
        "</tr>"
}