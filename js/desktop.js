let end = false;

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
    $.ajax({
        url: "php/process.php",
        type: "POST",
        data: { SENDER: "NEWGAME", GENS: gens, REPLAY: 0 }
    })
    .done((data) => {
        for(i=0; i<data["guess"].length; i++) {
            $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
        } 
        $(".character").css("width", 90/data["guess"].length + "%");
    });

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

        $.ajax({
            url: "php/process.php",
            type: "POST",
            data: { SENDER: "NEWGAME", GENS: gens, REPLAY: 1 }
        })
        .done((data) => {
            for(i=0; i<data["guess"].length; i++) {
                $("#row").append($("<div'></div>").addClass("character").append("<p></p>").text("_"));
            } 
            $(".character").css("width", 90/data["guess"].length + "%");
        });

        $("#letters").text("Lettres :");
        $("#errors").text("Erreurs : 0/10");
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
        $.ajax({
            url: "php/process.php",
            type: "POST",
            data: { SENDER: "HINT" }
        })
        .done((data) => {
            $("#type1").attr("src", "images/typesIcons/"+data["hint"][0]+".png");
            $("#hints").show();
            if (data["hint"].length > 1) {
                $("#type2").attr("src", "images/typesIcons/"+data["hint"][1]+".png");
                $("#type2").show();
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
        $.ajax({
            url: "php/process.php",
            type: "POST",
            data: { SENDER: "KEYDOWN", "KEYDOWN": String.fromCharCode(code) }
        })
        .done((data) => {
            if (data["message"] == "Char not in name") {
                $("#errors").text("Erreurs : "+data["nbErrors"]+"/10");
            } else {
                for(i=0; i<data["guess"].length; i++) {
                    $("#row").children()[i].innerHTML = "<p>"+ data["guess"][i] +"</p>";
                }
            }

            if (data["found"]) {
                $(".character").css("background-color", "#0F0");
                $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+data["pokedex"]+".png?v=ev-blueberry");
                end = true;
                $("#hint").addClass("hint");
                $("#score").text("Score : "+data["score"]);
            }

            if (data["nbErrors"] >= 10) {
                $(".character").css("background-color", "#F00");
                for(i=0; i<data["answer"].length; i++) {
                    $("#row").children()[i].innerHTML = "<p>"+ data["answer"][i] +"</p>";
                }
                $("#hint").addClass("hint");
                $("#answer").attr("src", "https://www.pokebip.com/pokedex-images/300/"+data["pokedex"]+".png?v=ev-blueberry");
                end = true;
                $("#score").text("Score : 0");
            }
        });

        $("#letters").text($("#letters").text()+" "+String.fromCharCode(code));
    }
});