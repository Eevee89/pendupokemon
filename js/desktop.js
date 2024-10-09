function getRandomPokemon(gens) {
    let filtered = [];
    pokemons.forEach((poke) => {
        if (gens.includes(poke["Generation"])) {
            filtered.push(poke);
        }
    });
    let index = Math.floor(Math.random() * filtered.length);
    return filtered[index];
}

let poke = "";
let pokedex = -1;
let found = 0;
let errors = 0;
let end = false;
let score = 0;

$(document).ready(function () {
    $("#answer").attr("src", "");
    let gens = [];
    for(i=1; i<=9; i++) {
        if ($("#"+i).hasClass("success")) {
            gens.push(i);
        }
    }
    poke = getRandomPokemon(gens);
    pokedex = poke["Pokedex"];
    poke = poke["Nom"].toUpperCase();
    for(i=0; i<poke.length; i++) {
        $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
    } 
    $(".character").css("width", 90/poke.length + "%");
    found = 0;
    errors = 0;
    end = false;

    $("#replay").click(() => {
        $("#row").html("");
        $("#answer").attr("src", "");
        let gens = [];
        for(i=1; i<=9; i++) {
            if ($("#"+i).hasClass("success")) {
                gens.push(i);
            }
        }
        poke = getRandomPokemon(gens);
        pokedex = poke["Pokedex"];
        poke = poke["Nom"].toUpperCase();
        for(i=0; i<poke.length; i++) {
            $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
        } 
        $(".character").css("width", 90/poke.length + "%");
        $("#letters").text("Lettres :");
        $("#errors").text("Erreurs : 0/10");
        found = 0;
        errors = 0;
        end = false;
    });

    $(".gen").click(() => {
        if (event.target.classList.contains("success")) {
            event.target.classList.remove("success");
            event.target.classList.add("failed");
            $("#replay").click();
        }
        else {
            event.target.classList.remove("failed");
            event.target.classList.add("success");
            $("#replay").click();
        }
    });
});


$(document).keydown(function(e) {
    let code = e.keyCode;
    let letters = $("#letters").text().split(":")[1];
    if ((code >= 65 && code <= 90 || code === 32) && letters.search(String.fromCharCode(code)) === -1 && !end) {
        if (poke.search(String.fromCharCode(code)) === -1) {
            errors += 1;
            $("#errors").text("Erreurs : "+errors+"/10");
        }
        else {
            for(i=0; i<poke.length; i++) {
                if (poke[i] == String.fromCharCode(code)) {
                    $("#row").children()[i].innerHTML = "<p>"+ poke[i] +"</p>";
                    found += 1;
                }
            }
        }
        $("#letters").text($("#letters").text()+" "+String.fromCharCode(code));
    }
    if (found >= poke.length) {
        $(".character").css("background-color", "#0F0");
        $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");
        score += (10-errors);
        errors = 0;
        found = 0;
        end = true;
        $("#score").text("Score : "+score);
    }
    if (errors >= 10) {
        $(".character").css("background-color", "#F00");
        for(i=0; i<poke.length; i++) {
            $("#row").children()[i].innerHTML = "<p>"+ poke[i] +"</p>";
        }
        $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");
        errors = 0;
        found = 0;
        score = 0;
        $("#score").text("Score : "+score);
        end = true;
    }
});