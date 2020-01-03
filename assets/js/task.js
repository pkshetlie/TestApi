import moment from "moment";
import {popError} from "./app";
import {doIt} from "./app";

//Affichage de la liste des tâches
function taskDrawList(url){
    $.ajax({
        url: url,
        type: 'GET',
        dataType:'json',
        success: function (data) {
            taskGenTable(data);
        },
        error: popError
    });
    return false;
}

//récuperations de liste
$(document).on('getlist', function (data, userid) {
    return taskDrawList("/api/v1/task/"+userid);
});
$(document).on('click', '.getList', function () {
    let $t = $(this);
    let url = $t.attr('href');
    return taskDrawList(url);
});

//suppression d'une tâche
$(document).on('click', '.delete-task-btn', function () {
    if(confirm('La suppresison d\'une tâche est définitive, voulez vous continuer ?')) {
        let $t = $(this);
        let url = $t.attr('href');
        doIt(url);
        taskDrawList("/api/v1/task/" + $t.data('user'));
    }
    return false;
});

// récuperation du formulaire d'ajout d'une tâche
$(document).on('click', '.add-task-btn', function () {
    let $t = $(this);
    let $modal =$("#modalForm");
    $.ajax({
        url: "/task/"+$t.data('user')+"/new",
        dataType:'html',
        type:'get',
        success:function(data){
            $modal.find('.modal-title').html("Nouvelle tâche");
            $modal.find('.modal-body').html(data);
            $modal.modal("show");
        },
        error: function(){
            Swal.fire("Impossible de charger le formulaire");
        }
    });
    return false;
});

// récuperation du formulaire d'édition d'une tâche
$(document).on('click', '.edit-task-btn', function () {
    let $t = $(this);
    let $modal =$("#modalForm");

    $.ajax({
        url: "/task/"+$t.data('task')+"/edit",
        dataType:'html',
        type:'get',
        success:function(data){
            $modal.find('.modal-title').html("Modifier une tâche");
            $modal.find('.modal-body').html(data);
            $modal.modal("show");
        },
        error: function(){
            Swal.fire("Impossible de charger le formulaire");
        }
    });
    return false;
});

// generation du contenu de la table
function taskGenTable(data) {
    let taskTable = $("#tasks tbody");
    taskTable.find('tr').remove();
    if (data !== undefined && data.length > 0) {
        for (let i = 0; i < data.length; i++) {
            let task = data[i];
            taskTable.append(taskTemplateLine(task));
        }
    } else {
        taskTable.append(taskEmptyTemplate());
    }
}

// generation d'un ligne contenant le resultat si aps d'objets retournés.
function taskEmptyTemplate() {
    return "<tr><td colspan='5'>Aucune tâche pour le moment</td></tr>";
}

//recuperation du status sous forme de string
function taskGetStatusStr(str){
    switch (str) {
        case 10:
            return 'Débutée';
        case 20:
            return "En cours";
        case 30:
            return "Terminé";
        default:
            return "Inconnu";
    }
}

//generation du template d'une ligne
function taskTemplateLine(task) {
    return "<tr>" +
        "<td>" + task.title + "</td>" +
        "<td>" + task.description + "</td>" +
        "<td>" + moment(task.created_at,"YYYY-MM-DDTHH:mm:ss+00:00").format('DD/MM/YYYY HH:mm') + "</td>" +
        "<td>" + taskGetStatusStr(task.status) + "</td>" +
        "<td>" +
        "<a class='btn btn-sm btn-primary edit-task-btn' data-task='"+task.id+"' href='#'><i class='fas fa-edit'></i></a>" +
        "<a class='btn btn-sm btn-danger delete-task-btn' data-user='"+task.user.id+"' href='/api/v1/task/delete/"+task.id+"'><i class='fas fa-trash'></i></a>" +
        "</td>" +
        "</tr>"
}