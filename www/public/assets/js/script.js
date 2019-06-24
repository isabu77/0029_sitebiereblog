function search() {
    var texte = $("#searchSaisie").val();
    var offset = 0;
    var span = "<span style='background-color:yellow'>";
    var span2 = "</span>";
    var re = new RegExp('(' + texte + ')(?![^<]*>)', "gi");
    $('span').contents().unwrap();

    var content = $('#contenu').html();
    content = content.replace(re, span + '$&' + span2);
    $('#contenu').html(content);

    $("#searchSaisie").val("");
}

/* $("#searchSaisie").on("change", search); */

/* $("#searchButton").on("click", search); */