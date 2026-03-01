$(document).ready(() => {
    $("#newPassword, #confirmPassword").on("keyup", function () {
        var newPasswordValue = $("#newPassword").val();
        var confirmPasswordValue = $("#confirmPassword").val();

        if (newPasswordValue.length > 0 && confirmPasswordValue.length > 0) {
        if (confirmPasswordValue !== newPasswordValue) {
            $("#password-does-not-match-text").removeAttr("hidden");
            $("#signUpSubmit").attr("disabled", true);
        }
        if (confirmPasswordValue === newPasswordValue) {
            $("#signUpSubmit").removeAttr("disabled");
            $("#password-does-not-match-text").attr("hidden", true);
        }
        }
    });

    $(".togglePassword").click(function (e) {
        e.preventDefault();
        const target = $(e.target);
        let icon = target;
        if (target.prop("tagName").toLowerCase() === "span") {
            icon = $(target.children()[0]);
        }

        const input = $("#"+target.data("for"));

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            input.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    const form = $("#signUpForm");
    
    form.on("submit", (event) => {
        form.addClass('was-validated');
        event.preventDefault();

        const username = $("#newUsername").val();

        if (!form[0].checkValidity()) {
            event.stopPropagation();
        } else {
            $.post(signupurl, 
                { 
                    username: username,
                    password: $("#newPassword").val()
                }
            )
            .done(function () {
                successSwal.title = successSwal.title.replace("%username%", username);
                swal(successSwal);
                form.removeClass('was-validated');
                form[0].reset();
                localStorage["username"] = username;

                $("#signModal").modal("hide");

                window.location.reload();
            })
            .fail(function (response) {
                if (response.status >= 500) {
                    errorSwal.text = response.responseJSON.message;
                    form.removeClass('was-validated');
                    form[0].reset();
                    $("#signUpBtn").hide();
                    $("#signInBtn").hide();
                    $("#signModal").modal("hide");
                    //window.location.reload();
                } else {
                    errorSwal.text = errorTexts[response.responseJSON.message];
                }
                swal(errorSwal);
            });
        }
    });

    $("#signModal").on("hidden.bs.modal", function () {
        isModalOpen = false;
        form.removeClass('was-validated');
        form[0].reset();
    });

    $("#signInSubmit").click(() => {
        localStorage["username"] = $("#inputUsername").val();
    });
});