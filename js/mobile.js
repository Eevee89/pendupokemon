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
let score = 0;

$(document).ready(function () {
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

    $("#replay").click(() => {
        $("#row").html("");
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
        for(i=65; i<91; i++) {
            $("#"+String.fromCharCode(i)).removeClass("used");
        }
        $("#Space").removeClass("used");
        $("#tiles").show();
        $("#answer").hide();
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

    $(".tile").click((event) => {
        if (!event.target.classList.contains("used")) {
            for(i=65; i<91; i++) {
                $("#"+String.fromCharCode(i)).addClass("used");
            }
            $("#Space").addClass("used");
            var id = event.target.id;
            let code = id.charCodeAt(0);
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
            if (found === poke.length) {
                $(".character").css("background-color", "#0F0");
                found = 0;
                score += (10-errors);
                errors = 0;
                $("#score").text("Score : "+score);
                let username = $("#session_user").text().split(' ')[1];
                $.ajax({
                    url: "php/updateScores.php",
                    type: "POST",
                    data: { name: username, score: score }
                });
                $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");
                $("#tiles").hide();
                $("#answer").show();
            }
            if (errors === 10) {
                $(".character").css("background-color", "#F00");
                for(i=0; i<poke.length; i++) {
                    $("#row").children()[i].innerHTML = "<p>"+ poke[i] +"</p>";
                }
                score = 0;
                errors = 0;
                found = 0;
                $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+pokedex+".png?v=ev-blueberry");
                $("#tiles").hide();
                $("#answer").show();
            }
            letters = $("#letters").text().split(":")[1];
            for(i=65; i<91; i++) {
                if (letters.search(String.fromCharCode(i)) === -1) {
                    $("#"+String.fromCharCode(i)).removeClass("used");
                }
            }
            if (letters.search("_") === -1) {
                $("#Space").removeClass("used");
            }
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

    $(".tileP").click((event) => {
        $(event.target).parent().click();
    });

    for(i=65; i<91; i++) {
        $("#"+String.fromCharCode(i)).removeClass("used");
    }
    $("#Space").removeClass("used");
});