$(document).ready(function () {
    if ($(window).width() < 780) {
        $("#desktopView").hide();
        $("#mobileView").show();
    } else {
        $("#mobileView").hide();
        $("#desktopView").show();
    }

    $("#scoreBtn").expandIcon("fa-solid fa-table-list", buttonsText.scoreBtn);
    $("#signUpBtn").expandIcon("fa-solid fa-user-plus", buttonsText.signUpBtn);
    $("#signInBtn").expandIcon("fa-regular fa-user", buttonsText.signInBtn);
    $("#disconnectBtn").errorExpandIcon("fa-solid fa-user", buttonsText.disconnectBtn);
    $("#replayBtn").expandIcon("fa-solid fa-rotate-left", "Rejouer");
    $("#hintBtn").expandIcon("fa-solid fa-magnifying-glass", "Indice");
    $('[data-toggle="tooltip"]').tooltip();
    $("#poketypes").empty();

    $("#scoreBtn").click(() => {
        isModalOpen = true;
        $("#scoreModal").modal("show");
    });

    $("#signUpBtn").click(() => {
        isModalOpen = true;
        $("#signup-tab").click();
        $("#signModal").modal("show");
    });

    $("#signInBtn").click(() => {
        isModalOpen = true;
        $("#signin-tab").click();
        $("#signModal").modal("show");
    });

    $("#signin-tab").click(() => {
        const signinForm = $("#signin");
        signinForm.empty();
        $.ajax({
            url: loginUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(html) {
                signinForm.html(html);
            },
            error: function(textStatus, errorThrown) {
                signinForm.html("Une erreur est survenue");
            }
        });
    })

    $("#disconnectBtn").click(() => {
        swal({
            title: "Voulez-vous vous déconnecter ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: logoutUrl,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function() {
                        window.location.reload();
                    },
                    error: function(textStatus, errorThrown) {
                        console.error('Error fetching login form:', textStatus, errorThrown);
                    }
                });
            }
        });
    });

    $(".gen").click((event) => {
        let target = $(event.target);
        if (target.prop("tagName").toLowerCase() == "span") {
            target = target.parent();
        }

        if (target.hasClass("success")) {
            if ($(".gen.failed").length === 8) {
                swal({
                    title: "Action impossible",
                    text: "Au moins une génération doit être active",
                    icon: "error",
                    button: "OK"
                });

                return;
            }

            target.removeClass("success").addClass("failed");
            activesGens = removeItem(activesGens, target.children().text());
        } else {
            target.removeClass("failed").addClass("success");
            activesGens.push(target.children().text());
        }

        $("#replayBtn").click();
    });

    $("#hintBtn").click(() => {
        if (!answerReveled) {
            $.ajax({
                url: urls.hint,
                type: 'GET',
                success: function(response) {
                    const type1 = response["type1"];
                    const type2 = response["type2"];

                    $("#poketypes")[type1]();
                    if (type2) {
                        $("#poketypes")[type2]();
                    }
                    $("#hintBtn").addClass("disabled");
                },
                error: function(err) {
                    swal({
                        title: "Une erreur est survenue",
                        icon: "warning",
                        text: err.responseJSON.message
                    });
                }
            });
        }
    });

    $("#answerImg").hide();

    $("#replayBtn").click(() => {
        $("#hintBtn").removeClass("disabled");
        answerReveled = false;
        $("#answerImg").attr("src", "");
        $("#answerImg").hide();
        $("#poketypes").empty();
        $("#lettersLabel").text($("#lettersLabel").text().split(" : ")[0] + " : ");
        $("#errorLabel").text($("#errorLabel").text().split(" : ")[0] + " : 0/10");

        const answer = $("#answer");
        answer.empty();
        $.ajax({
            url: urls.replay,
            type: 'GET',
            data: {
                "generations": activesGens.join(',')
            },
            success: function(data) {
                for(let i=0; i<data.letters; i++) {
                    answer.append($("<div></div>").addClass("letter").append($("<span>_</span>").addClass("title")));
                }
            },
            error: function(textStatus, errorThrown) {
                console.error('Error fetching login form:', textStatus, errorThrown);
            }
        });
    });
});

$.fn.expandIcon = function(icon, text) {
    const spanIcon = $('<span/>').addClass('icon');
    const iconElt = $('<i/>').addClass("fa " + icon);
    spanIcon.append(iconElt);

    const spanText = $('<span/>').addClass('title').html(text);

    $(this).addClass("expandicon").append(spanIcon).append(spanText);
}

$.fn.successExpandIcon = function(icon, text) {
    $(this).expandIcon(icon, text);
    $(this).css("--pseudo-bg", "linear-gradient(45deg, #5F3, #5F7)");
}

$.fn.errorExpandIcon = function(icon, text) {
    $(this).expandIcon(icon, text);
    $(this).css("--pseudo-bg", "linear-gradient(45deg, #F35, #F75)");
}

function removeItem(arr, value) {
    const index = arr.indexOf(value);
    if (index > -1) {
        arr.splice(index, 1);
    }
    return arr;
}

//#region PokeTiles

$.fn.pokeTypeTile = function (type, filename, color1, color2) {
    const div = $("<div/>").addClass("poketype");
    const span = $('<span/>').addClass('title').html(type);
    div.append(span);
    div.css("--pseudo-bg", `linear-gradient(45deg, ${color1}, ${color2})`);
    div.css("--icon-url", `url(../images/icons/${filename}.png)`);
    
    $(this).append(div);
}

$.fn.appendBugType = function() {
    $(this).pokeTypeTile("Insecte", "bug_type", "#91a119", "#cfdc6f");
}

$.fn.appendDarkType = function() {
    $(this).pokeTypeTile("Ténèbres", "dark_type", "#957975", "#50413f");
}

$.fn.appendDragonType = function() {
    $(this).pokeTypeTile("Dragon", "dragon_type", "#5060e1", "#818bd7");
}

$.fn.appendElectrikType = function() {
    $(this).pokeTypeTile("Electrik", "electrik_type", "#fac000", "#fbecb7");
}

$.fn.appendFairyType = function() {
    $(this).pokeTypeTile("Fée", "fairy_type", "#ef70ef", "#eda7ed");
}

$.fn.appendFightingType = function() {
    $(this).pokeTypeTile("Combat", "fighting_type", "#bf766a", "#ff8000");
}

$.fn.appendFireType = function() {
    $(this).pokeTypeTile("Feu", "fire_type", "#945349", "#e62829");
}

$.fn.appendFlyingType = function() {
    $(this).pokeTypeTile("Vol", "flying_type", "#b7d4ef", "#81b9ef");
}

$.fn.appendGhostType = function() {
    $(this).pokeTypeTile("Spectre", "ghost_type", "#4a1f4a", "#704170");
}

$.fn.appendGrassType = function() {
    $(this).pokeTypeTile("Plante", "grass_type", "#3FA129", "#5ec946");
}

$.fn.appendGroundType = function() {
    $(this).pokeTypeTile("Sol", "ground_type", "#957975", "#915121");
}

$.fn.appendIceType = function() {
    $(this).pokeTypeTile("Glace", "ice_type", "#9dd9e8", "#3fd8ff");
}

$.fn.appendNormalType = function() {
    $(this).pokeTypeTile("Normal", "normal_type", "#aaaaaa", "#cccccc");
}

$.fn.appendPoisonType = function() {
    $(this).pokeTypeTile("Poison", "poison_type", "#704170", "#9141cb");
}

$.fn.appendPsychicType = function() {
    $(this).pokeTypeTile("Psy", "psychic_type", "#ef4179", "#ea97b2");
}

$.fn.appendRockType = function() {
    $(this).pokeTypeTile("Roche", "rock_type", "#957975", "#afa981");
}

$.fn.appendSteelType = function() {
    $(this).pokeTypeTile("Acier", "steel_type", "#53749e", "#60a1b8");
}

$.fn.appendWaterType = function() {
    $(this).pokeTypeTile("Eau", "water_type", "#53749e", "#2980ef");
}

//#endregion