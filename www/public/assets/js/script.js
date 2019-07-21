var tva = 1.2;
var fraisPort = 5.40;
var shiplimit = 30;

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

// recherche d'une chaine dans la page avec surlignage en rouge quand trouvé
function search() {
    var texte = $("#searchSaisie").val();
    var offset = 0;
    var span = "<span style='background-color:yellow'>";
    var span2 = "</span>";
    var re = new RegExp('(' + texte + ')(?![^<]*>)', "gi");
    $('span').contents().unwrap();

    var content = $('.content').html();


    content = content.replace(re, span + '$&' + span2);
    $('.content').html(content);

    $("#searchSaisie").val("");
}

/* $("#searchSaisie").on("change", search); */

/* $("#searchButton").on("click", search); */

// calcul du prix dans les champs ddu bon de commande
function calcPrice(obj, id, originalPrice) {
    var qty = obj.value;
    var pHT = originalPrice * qty;
    var pTTC = pHT * tva;

    // le total de la ligne
    document.getElementById('HT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
    document.getElementById('TTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

    // totaux commande
    totaux();

}

// calcul du prix dans les champs du panier
function calcPriceCart(obj, id, originalPrice) {
    var qty = obj.value;
    var pHT = originalPrice * qty;
    var pTTC = pHT * tva;

    // le total de la ligne
    document.getElementById('PHT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
    document.getElementById('PTTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";

    // totaux panier
    totaux('P');

}

// supprime la ligne du panier
function deleteOfCart(id, originalPrice) {
    // appel AJAX pour lancer un post de suppression de la ligne de commande du panier
    $.post("/deletecart", { idBeer: id },
        function(data) {
            // on vide la partie panier
            document.getElementById('PHT_' + id).innerHTML = "";
            document.getElementById('PTTC_' + id).innerHTML = "";
            document.getElementById('PQTY_' + id).value = "";
            if (document.getElementById('trcart_' + id)) {
                document.getElementById('trcart_' + id).hidden = true;
            }

            // totaux panier et commande
            totaux('P', data);
            totaux();
        });
}

// calcule le total du panier (si $prefix = 'P') ou du coté commande
function totaux(prefix = "", totalPanier = 0) {
    var totalHT = 0.0;
    var totalTTC = 0.0;
    var total = 0;
    var frais = fraisPort;

    var qtys = document.getElementsByClassName(prefix + "QTY");
    var prixHT = document.getElementsByClassName("HT");
    for (var i = 0; i < qtys.length; i++) {
        var pqty = parseInt(qtys[i].value);
        var ht = parseFloat(prixHT[i].value);
        var ttc = ht * tva;
        if (pqty > 0) {
            total += pqty;
            totalHT += ht * pqty;
            totalTTC += ttc * pqty;
        }
    }
    if (total === 0) {
        total = totalPanier;
    }
    if (prefix !== "") {
        if (total > 0) {
            document.getElementById('panier').innerHTML = Number(total);
            document.getElementById('titrePanier').innerHTML = "Votre PANIER";
            if (document.getElementById('commander')) {
                document.getElementById('commander').disabled = false;
            }
        } else {
            document.getElementById('panier').innerHTML = "";
            document.getElementById('titrePanier').innerHTML = "Votre panier est vide";
            if (document.getElementById('commander')) {
                document.getElementById('commander').disabled = true;
            }
            if (document.getElementById('titresPanier')) {
                document.getElementById('titresPanier').hidden = true;
            }
            if (document.getElementById('panierTotal')) {
                document.getElementById('panierTotal').hidden = true;
            }
            if (document.getElementById('panierPort')) {
                document.getElementById('panierPort').hidden = true;
            }
        }

    }

    if (totalTTC > shiplimit) {
        frais = 0;
    }

    // les totaux du panier
    if (document.getElementById(prefix + 'FRAIS')) {
        document.getElementById(prefix + 'FRAIS').innerHTML = String(frais.toFixed(2)).replace('.', ',') + "€";
        if (total > 0) {
            document.getElementById(prefix + 'HT').innerHTML = String(totalHT.toFixed(2)).replace('.', ',') + "€";
            document.getElementById(prefix + 'TTC').innerHTML = String(totalTTC.toFixed(2)).replace('.', ',') + "€";
            document.getElementById(prefix + 'QTY').innerHTML = Number(total);
        } else {
            document.getElementById(prefix + 'HT').innerHTML = "";
            document.getElementById(prefix + 'TTC').innerHTML = "";
            document.getElementById(prefix + 'QTY').innerHTML = "";
        }
    }

}

// affichage de la carte modale d'une bière pour l'ajouter au panier
function getProductsModal(title, img, content, price, id) {
    $('#modal-message').removeAttr('class').text('');

    $('#modal-title').text(title);
    $('#modal-body-img').attr('src', img).attr('alt', title);
    $('#modal-body').text(content);
    $('#modal-body-price').text(price + '€');
    $('#QTY_id').val(1);
    $('#product_id').attr('onclick', 'addCart(' + id + ', "QTY_id")');

}

// ajouter une bière au panier par la carte modale
function addCart(id, qtyId) {
    var qty = document.getElementById(qtyId).value;
    if (qty <= 0) {
        return;
    }
    // appel AJAX pour lancer un post d'insertion en base de la ligne de commande du panier
    $.post("/addToCart", { idBeer: id, quantity: qty },
        function(data) {
            document.getElementById('panier').innerHTML = data;
        });
}

// ajouter au panier par la modale
function addToCart(id, originalPrice) {
    var qty = parseInt(document.getElementById('QTY_' + id).value);
    if (qty == 0) {
        return;
    }

    // appel AJAX pour lancer un post d'insertion en base de la ligne de commande du panier
    $.post("/updatecart", { idBeer: id, quantity: qty, price: originalPrice, addqty: true },
        function(data) {
            afficheCart(data, id, originalPrice);
        });
}

// update la ligne du panier dans la page commande ou panier
function updateCart(id, originalPrice, prefix = "") {
    var qty = parseInt(document.getElementById(prefix + 'QTY_' + id).value);
    if (qty == 0) {
        return;
    }
    // appel AJAX pour lancer un post de modification de la ligne de commande du panier
    $.post("/updatecart", { idBeer: id, quantity: qty, price: originalPrice, addqty: false },
        function(data) {
            afficheCart(data, id, originalPrice);
        });
}

// affichage du panier et de la commande
function afficheCart(data, id, originalPrice) {
    //console.log(data);
    obj = JSON.parse(data);
    if (obj) {
        var pHT = originalPrice * obj.beer_qty;
        var pTTC = pHT * tva;

        // on vide la partie commande si elle existe
        if (document.getElementById('HT_' + id)) {
            document.getElementById('HT_' + id).innerHTML = String(originalPrice.toFixed(2)).replace('.', ',') + "€";
            document.getElementById('TTC_' + id).innerHTML = String((originalPrice * tva).toFixed(2)).replace('.', ',') + "€";
            document.getElementById('QTY_' + id).value = 0;
        }
        // on remplit la ligne du panier
        if (document.getElementById('PQTY_' + id)) {
            document.getElementById('PQTY_' + id).value = obj.beer_qty; //qty;
            document.getElementById('PHT_' + id).innerHTML = String(pHT.toFixed(2)).replace('.', ',') + "€";
            document.getElementById('PTTC_' + id).innerHTML = String(pTTC.toFixed(2)).replace('.', ',') + "€";
        }
        // totaux panier
        totaux('P');
        totaux();
    }
}

// lecture en base et affichage des coordonnées d'un user_infos
// à partir d'un select contenant la liste des user_infos_id
function selectClient() {
    var index = document.getElementById("clients").selectedIndex;
    var id = document.getElementById("clients").options[index].value;
    //console.log(id);
    // appel AJAX pour lancer un post 
    $.post("/getClient", { idClient: id },
        function(data) {
            obj = JSON.parse(data);
            if (obj) {
                for (var input in obj) {
                    if (document.getElementById(input)) {
                        document.getElementById(input).value = obj[input];
                    }
                }
            }
        });
}

// lecture en base et affichage des coordonnées d'un user_infos
// à partir d'un navbar
function selectAdresse(id) {
    // appel AJAX pour lancer un post 
    $.post("/getClient", { user_infos_id: id },
        function(data) {
            elts = document.getElementsByClassName("nav-link active");
            for (var elt = 0; elt < elts.length; elt++) {
                $('#' + elts[elt].id).removeClass("active");
            }
            $('#a_' + id).addClass("active");
            obj = JSON.parse(data);
            //document.getElementById("user_infos_id").value = obj["id"];
            if (obj) {
                for (var input in obj) {
                    if (document.getElementById(input)) {
                        document.getElementById(input).value = obj[input];
                    }
                }
            }

        });
}

// sélection d'un état de commande 
// pour afficher seulement les commandes ayant cet état
function selectStatus(id) {
    // var index = document.getElementById("status").selectedIndex;
    //var id = document.getElementById("status").options[index].value;
    console.log(id);
    // appel AJAX pour lancer un post 
    $.post("/admin/orders/", { idStatus: id },
        function(data) {});
}