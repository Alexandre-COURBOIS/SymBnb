$('#add-image').click(function () {

    //Je récupère le numéro des futurs champs que je vais créer.
    const index = +$('#widgets_counter').val();
    // Je récupère le prototype des entrées
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    // J'injecte ce code au sein de la div
    $('#annonce_images').append(tmpl);

    $('#widgets_counter').val(index + 1);
    console.log(index);
    //Assimilation du bouton de suppression :
    deleteButtons();

});


function deleteButtons() {
    $('button[data-action="delete"]').click(function () {

        const target = this.dataset.target;

        $(target).remove();
    });
}

function updateCounter() {

    const count = +$('#annonce_images div.form-group').length;

    console.log(count);
    $('#widgets_counter').val(count);
}

//Gestion de la suppression pour l'édit, j'active la fonction si les images sont déjà présentes

updateCounter();
deleteButtons();

