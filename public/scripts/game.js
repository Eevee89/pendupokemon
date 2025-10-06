$(document).keydown(function(e) {
    let code = e.keyCode;
    let letters = $("#lettersLabel").text().split(":")[1];

    let isLetterOrSpace = (code >= 65 && code <= 90 || code === 32);
    let hasNotBeenTyped = letters.search(String.fromCharCode(code)) === -1;

    if (isLetterOrSpace && hasNotBeenTyped && !isModalOpen && !answerReveled) {
        $.ajax({
            url: "http://symfony-pokemon/guess/"+String.fromCharCode(code),
            type: "GET",
        })
        .done((data) => {
            const answer = $("#answer");
            const guess = data.guess;

            for(let i = 0; i < guess.length; i++) {
                const child = answer.children()[i];
                const span = $("<span></span>").addClass("title").text(guess[i]);

                if (guess.search("_") === -1) {
                    $(child).addClass("valid");
                    answerReveled = true;
                    $("#hintBtn").addClass("disabled");

                    $("#answerImg").attr("src", "https://www.pokebip.com/pokedex-images/300/"+data.pokedex+".png?v=ev-blueberry");
                    $("#answerImg").show();
                    $("#scoreLabel").text("Score : " + data.score);
                }

                child.innerHTML = span[0].outerHTML;
            }
        })
        .fail((err) => {
            const data = err.responseJSON;

            $("#errorLabel").text($("#errorLabel").text().split(" : ")[0] + " : " + data.errors + "/10");

            if (data.errors === 10) {
                answerReveled = true;
                const guess = data.answer;
                const answer = $("#answer");

                for(let i = 0; i < guess.length; i++) {
                    const child = answer.children()[i];
                    const span = $("<span></span>").addClass("title").text(guess[i]);

                    $(child).addClass("failed");
                    child.innerHTML = span[0].outerHTML;
                }

                $("#answerImg").attr("src", "https://www.pokebip.com/pokedex-images/300/"+data.pokedex+".png?v=ev-blueberry");
                $("#answerImg").show();
                $("#scoreLabel").text("Score : 0");
            }
        });

        $("#lettersLabel").text($("#lettersLabel").text()+" "+String.fromCharCode(code));
    }
});