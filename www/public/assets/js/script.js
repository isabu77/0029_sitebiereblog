function removeAccents(strAccents) {
    var strAccents = strAccents.split('');
    var strAccentsOut = new Array();
    var strAccentsLen = strAccents.length;
    var accents =
        'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽž';
    var accentsOut =
        "AAAAAAaaaaaaOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSsYyyZz";
    for (var y = 0; y < strAccentsLen; y++) {
        if (accents.indexOf(strAccents[y]) != -1) {
            strAccentsOut[y] =
                accentsOut.substr(accents.indexOf(strAccents[y]), 1);
        } else
            strAccentsOut[y] = strAccents[y];
    }
    strAccentsOut = strAccentsOut.join('');
    return strAccentsOut;
}

function search() {
    var texte = $("#searchSaisie").val();
    var offset = 0;
    var span = "<span style='background-color:yellow'>";
    var span2 = "</span>";
    var re = new RegExp('(' + texte + ')(?![^<]*>)', "gi");
    $('span').contents().unwrap();

    var content = $('.contenu').html();


    content = content.replace(re, span + '$&' + span2);
    $('.contenu').html(content);

    $("#searchSaisie").val("");
}

/* $("#searchSaisie").on("change", search); */

/* $("#searchButton").on("click", search); */

function calcPrice(obj, id, originalPrice) {
    var qty = obj.value;
    var pHT = originalPrice * qty;
    var pTTC = pHT * 1.2;

    // le total de la ligne
    document.getElementById('HT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
    document.getElementById('TTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

    // totaux commande
    totaux();

}

function calcPriceCart(obj, id, originalPrice) {
    var qty = obj.value;
    var pHT = originalPrice * qty;
    var pTTC = pHT * 1.2;

    // le total de la ligne
    document.getElementById('PHT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
    document.getElementById('PTTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

    // totaux panier
    totaux('P');

}

// ajouter au panier
function addToCart(id, originalPrice) {
    var qty = parseInt(document.getElementById('QTY_' + id).value);
    if (qty == 0) {
        return;
    }

    // appel AJAX pour lancer un post d'insertion en base de la ligne de commande du panier
    $.post("/addcart", { idBeer: id, quantity: qty, price: originalPrice },
        function(data) {
            //alert(data);
            if (data == "ok") {
                // ajouter la quantité saisie à celle du panier  
                var pqty = parseInt(document.getElementById('PQTY_' + id).value);
                if (pqty > 0 && pqty != NaN) {
                    qty += pqty;
                }
                var pHT = originalPrice * qty;
                var pTTC = pHT * 1.2;

                // on vide la partie commande
                document.getElementById('HT_' + id).innerHTML = String(originalPrice.toFixed(2)).replace('.', ',') + "€";
                document.getElementById('TTC_' + id).innerHTML = String((originalPrice * 1.2).toFixed(2)).replace('.', ',') + "€";
                document.getElementById('QTY_' + id).value = 0;

                // on remplit la ligne du panier
                document.getElementById('PQTY_' + id).value = qty;
                document.getElementById('PHT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
                document.getElementById('PTTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

                // totaux panier
                totaux('P');
                totaux();

            }
        });
}
// update la ligne du panier
function updateCart(id, originalPrice) {
    var qty = parseInt(document.getElementById('QTY_' + id).value);
    if (qty == 0) {
        return;
    }
    // appel AJAX pour lancer un post de modification de la ligne de commande du panier
    $.post("/updatecart", { idBeer: id, quantity: qty, price: originalPrice },
        function(data) {
            if (data == "ok") {
                var qty = document.getElementById('QTY_' + id).value;
                var pHT = originalPrice * qty;
                var pTTC = pHT * 1.2;
                var frais = 5.40;

                // on vide la partie commande
                document.getElementById('HT_' + id).innerHTML = String(originalPrice.toFixed(2)).replace('.', ',') + "€";
                document.getElementById('TTC_' + id).innerHTML = String((originalPrice * 1.2).toFixed(2)).replace('.', ',') + "€";
                document.getElementById('QTY_' + id).value = 0;

                // on remplit la ligne du panier
                document.getElementById('PQTY_' + id).value = qty;
                document.getElementById('PHT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
                document.getElementById('PTTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

                // totaux panier
                totaux('P');
                totaux();
            }
        });
}

// delete la ligne du panier
function deleteOfCart(id, originalPrice) {
    // appel AJAX pour lancer un post de suppression de la ligne de commande du panier
    $.post("/deletecart", { idBeer: id },
        function(data) {
            //console.log("delete");
            //alert(data);
            if (data == "ok") {
                // on vide la partie panier
                document.getElementById('PHT_' + id).innerHTML = "";
                document.getElementById('PTTC_' + id).innerHTML = "";
                document.getElementById('PQTY_' + id).value = "";

                // totaux panier et commande
                totaux('P');
                totaux();
            }
        });
}

// totaux calcule le total du panier (si $prefix = 'P') ou du coté commande
function totaux($prefix = "") {
    var totalHT = 0.0;
    var totalTTC = 0.0;
    var total = 0;
    var frais = 5.40;

    var qtys = document.getElementsByClassName($prefix + "QTY");
    var prixHT = document.getElementsByClassName("HT");
    for (var i = 0; i < qtys.length; i++) {
        var pqty = parseInt(qtys[i].value);
        var ht = parseFloat(prixHT[i].value);
        var ttc = ht * 1.2;
        if (pqty > 0) {
            total += pqty;
            totalHT += ht * pqty;
            totalTTC += ttc * pqty;
        }
    }
    //if ($prefix !== "")
    //    document.getElementById('commander').disabled = (total == 0);

    if (totalTTC > 30) {
        frais = 0;
    }

    // les totaux du panier
    document.getElementById($prefix + 'FRAIS').innerHTML = String(frais.toFixed(2)).replace('.', ',') + "€";
    if (total > 0) {
        document.getElementById($prefix + 'HT').innerHTML = String(totalHT.toFixed(2)).replace('.', ',') + "€";
        document.getElementById($prefix + 'TTC').innerHTML = String(totalTTC.toFixed(2)).replace('.', ',') + "€";
        document.getElementById($prefix + 'QTY').innerHTML = Number(total);
    } else {
        document.getElementById($prefix + 'HT').innerHTML = "";
        document.getElementById($prefix + 'TTC').innerHTML = "";
        document.getElementById($prefix + 'QTY').innerHTML = "";
    }

}