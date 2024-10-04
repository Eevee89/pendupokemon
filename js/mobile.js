function getRandomPokemon() {
    let index = Math.floor(Math.random() * 996);
    return pokemons[index];
}

let poke = "";
let found = 0;
let errors = 0;
let score = 0;

$(document).ready(function () {
    poke = getRandomPokemon().toUpperCase();
    for(i=0; i<poke.length; i++) {
        $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
    } 
    $(".character").css("width", 90/poke.length + "%");

    $("#replay").click(() => {
        $("#row").html("");
        poke = getRandomPokemon().toUpperCase();
        for(i=0; i<poke.length; i++) {
            $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
        } 
        $(".character").css("width", 90/poke.length + "%");
        $("#letters").text("Lettres :");
        $("#errors").text("Erreurs : 0/10");
    });

    $(".tile").click((event) => {
        var id = event.target.id;
        let code = ord(id);
        if (code >= 65 && code <= 90 || code === 95) {
            for(i=0; i<poke.length; i++) {
                if (poke[i] == String.fromCharCode(code)) {
                    $("#row").children()[i].innerHTML = "<p>"+ poke[i] +"</p>";
                    found += 1;
                }
            }
            let letters = $("#letters").text().split(":")[1];
            if (letters.search(String.fromCharCode(code)) === -1) {
                if (poke.search(String.fromCharCode(code)) === -1) {
                    errors += 1;
                    $("#errors").text("Erreurs : "+errors+"/10");
                }
                $("#letters").text($("#letters").text()+" "+String.fromCharCode(code));
            }
        }
        if (found === poke.length) {
            $(".character").css("background-color", "#0F0");
            errors = 0;
            found = 0;
            score += 1;
            $("#score").text("Score : "+score);
        }
        if (errors === 10) {
            $(".character").css("background-color", "#F00");
            for(i=0; i<poke.length; i++) {
                $("#row").children()[i].innerHTML = "<p>"+ poke[i] +"</p>";
            }
            errors = 0;
            found = 0;
        }
    });
});