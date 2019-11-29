$(document).ready(() => {
    /**
     * Fonction jQuery qui permet, au submit d'un form, de boucler sur chaque input avec
     * la classe get et d'y ajouter l'attribut disabled si celui-ci est vide,
     * afin d'obtenir une requette GET plus propre
     */
    $("form.get").submit(() => {
        $("input").each(
            (iteration, element) => $(element).val() === '' ? $(element).attr("disabled", true) : null
        )
    });

    $('#book_author option:first').css("color","#134245");

});