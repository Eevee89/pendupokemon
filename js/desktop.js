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
let nom = "";
let pokedex = -1;
let types = [];
let found = 0;
let errors = 0;
let end = false;
let score = 0;

$(document).ready(function () {
    $("#hint").removeClass("hint");
    $("#type1").attr("src", "");
    $("#hints").hide();
    $("#type2").attr("src", "");
    $("#type2").hide();
    $("#answer").attr("src", "");
    let gens = [];
    for(i=1; i<=9; i++) {
        if ($("#"+i).hasClass("success")) {
            gens.push(i);
        }
    }
    poke = getRandomPokemon(gens);
    pokedex = poke["Pokedex"];
    nom = poke["Nom"].toUpperCase();
    types = [];
    types.push(poke["Type1"].toLowerCase());
    if ("Type2" in poke) {
        types.push(poke["Type2"].toLowerCase());
    }
    for(i=0; i<nom.length; i++) {
        $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
    } 
    $(".character").css("width", 90/nom.length + "%");
    found = 0;
    errors = 0;
    end = false;

    $("#replay").click(() => {
        $("#hint").removeClass("hint");
        $("#type1").attr("src", "");
        $("#hints").hide();
        $("#type2").attr("src", "");
        $("#type2").hide();
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
        nom = poke["Nom"].toUpperCase();
        types = [];
        types.push(poke["Type1"].toLowerCase());
        if ("Type2" in poke) {
            types.push(poke["Type2"].toLowerCase());
        }
        for(i=0; i<nom.length; i++) {
            $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
        } 
        $(".character").css("width", 90/nom.length + "%");
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

    $("#scoreList").click(() => {
        $("#scoreModal").css("display", "block");
        $.ajax({
            url: 'php/fetchScores.php',
            type: 'POST',
            success: function (data) {
                $('#tbody').html("");
                data = JSON.parse(data);
                data.forEach((row) => {
                    let name = row.name;
                    if (name == $("#session_user").text().split(' ')[1]) {
                        name += " (MOI)";
                    }
                    let tr = $("<tr></tr>");
                    tr.append(`<td>${name}</td><td>${row.score}</td>`);
                    $("#tbody").append(tr);
                });
            },
            error: function(error) {
            } 
        });
    });

    $("#hint").click(() => {
        $("#hint").addClass("hint");
        $("#type1").attr("src", "images/typesIcons/"+types[0]+".png");
        $("#hints").show();
        if (types.length > 1) {
            $("#type2").attr("src", "images/typesIcons/"+types[1]+".png");
            $("#type2").show();
        }
    });

    $("#signup").click(() => {
        $("#signupModal").css("display", "block");
    });

    $("#signin").click(() => {
        $("#signinModal").css("display", "block");
    });

    $(".connected").click(() => {
        $("#accountModal").css("display", "block");
    });

    $(".close").click(() => {
        $("#scoreModal").css("display", "none");
        $("#signupModal").css("display", "none");
        $("#signinModal").css("display", "none");
        $("#accountModal").css("display", "none");
    });
});


$(document).keydown(function(e) {
    let code = e.keyCode;
    let letters = $("#letters").text().split(":")[1];
    let isLetterOrSpace = (code >= 65 && code <= 90 || code === 32);
    let hasNotBeenTyped = letters.search(String.fromCharCode(code)) === -1;
    let isConnectModalClosed = $("#signupModal").css("display") === "none" && $("#signinModal").css("display") === "none";
    let isScoreModalClosed = $("#scoreModal").css("display") === "none";
    let isAccountModalClosed = $("#accountModal").css("display") === "none";
    if ( isLetterOrSpace && hasNotBeenTyped && !end && isConnectModalClosed && isScoreModalClosed && isAccountModalClosed) {
        if (nom.search(String.fromCharCode(code)) === -1) {
            errors += 1;
            $("#errors").text("Erreurs : "+errors+"/10");
        }
        else {
            for(i=0; i<nom.length; i++) {
                if (nom[i] == String.fromCharCode(code)) {
                    $("#row").children()[i].innerHTML = "<p>"+ nom[i] +"</p>";
                    found += 1;
                }
            }
        }
        $("#letters").text($("#letters").text()+" "+String.fromCharCode(code));
    }
    if (found >= nom.length) {
        $(".character").css("background-color", "#0F0");
        $("#hint").addClass("hint");
        $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");
        toAdd = 10-errors;
        if ($("#hint").hasClass("hint")) {
            toAdd -= 2;
        }
        if (toAdd < 0) {
            toAdd = 0;
        }
        score += toAdd;
        errors = 0;
        found = 0;
        end = true;
        $("#score").text("Score : "+score);
        let username = $("#session_user").text().split(' ')[1];
        $.ajax({
            url: "php/updateScores.php",
            type: "POST",
            data: { name: username, score: score }
        });
    }
    if (errors >= 10) {
        $(".character").css("background-color", "#F00");
        for(i=0; i<nom.length; i++) {
            $("#row").children()[i].innerHTML = "<p>"+ nom[i] +"</p>";
        }
        $("#hint").addClass("hint");
        $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");

        errors = 0;
        found = 0;
        score = 0;
        $("#score").text("Score : "+score);
        end = true;
    }
});