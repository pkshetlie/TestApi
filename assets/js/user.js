import moment from "moment";

const Swal = require('sweetalert2');
//Affichage de la liste des taches
$(document).on('click', '.getlist', function () {
    let $t = $(this);
    let url = $t.attr('href');
    $.ajax({
        url: url,
        type: 'GET',
        dataType:'json',
        success: function (data) {
            genTable(data);
        },
        error: popError
    });
    return false;
});

// ajout d'une tache
$(document).on('click', '.add-task-btn', function () {
    let $t = $(this);
    let url = $t.attr('href');
    let user = $t.data('user');
    let Task = {
        title: null,
        description: null,
        status: null,
        user: null
    };
    Swal.fire({
        title: "Titre de la nouvelle tache",
        input: 'text',
        inputPlaceholder: "Titre de la tache",
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Le titre est obligatoire !'
            }
        }
    }).then((result) => {
        if (result !== undefined) {
            Task.title = result.value;
            Task.user = user;
            Swal.fire({
                title: "Description de la tache",
                input: 'textarea',
                inputPlaceholder: "Description de la tache",
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'La description est obligatoire !'
                    }
                }
            }).then((result) => {
                if (result !== undefined) {
                    Task.description = result.value;
                    Swal.fire({
                        title: "Etat de la tache",
                        input: 'select',
                        inputOptions: {
                            debutee: 'Débutée',
                            en_cours: 'En cours',
                            terminee: 'Terminée',
                        },
                        showCancelButton: true,
                        inputValidator: (value) => {
                            return new Promise((resolve) => {
                                if (value !== undefined) {
                                    resolve()
                                } else {
                                    resolve('Il faut sélectionner une valeur')
                                }
                            })
                        }
                    }).then((result) => {
                        console.log(result)
                        if (result !== undefined) {
                            Task.status = result.value;
                            $.ajax({
                                url: url,
                                data: Task,
                                type: 'POST',
                                dataType:'json',
                                success: function (data) {
                                    genTable(data);
                                },
                                error: popError
                            });
                        }
                    });
                }
            });
        }
    });
    return false;
});

// template d'erreur
function popError(data) {
   let resp = data.responseJSON;
    Swal.fire({
        title: "Erreur",
        text: resp.error !== undefined ? resp.error : "Erreur lors de la création de la tache.",
        icon: 'error'
    });
}

// generation du contenu de la table
function genTable(data) {
    let taskTable = $("#tasks tbody");
    taskTable.find('tr').remove();
    if (data !== undefined && data.length > 0) {
        for (let i = 0; i < data.length; i++) {
            let task = data[i];
            taskTable.append(taskTemplateLine(task));
        }
    } else {
        taskTable.append(emptyTemplate());
    }
}

// generation d'un ligne contenant le resultat si aps d'objets retournés.
function emptyTemplate() {
    return "<tr><td colspan='5'>Aucune tache pour le moment</td></tr>";

}

//recuperation du status sous forme de string
function getStatusStr(str){
    switch (str) {
        case '10':
            return 'Débutée';
        case '20':
            return "En cours";
        case '30':
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
        "<td>" + moment(task.created_at,"YYYY-MM-DDTHH:mm:ss+00:00").format('DD/MM/YYYY hh:mm') + "</td>" +
        "<td>" + getStatusStr(task.status) + "</td>" +
        "<td>" +
        "<a class='btn btn-sm btn-primary edit-task-btn' data-task='"+task.id+"' href='#'><i class='fas fa-edit'></i></a>" +
        "<a class='btn btn-sm btn-danger delete-task-btn' data-task='"+task.id+"' href='#'><i class='fas fa-trash'></i></a>" +
        "</td>" +
        "</tr>"
}