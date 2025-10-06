$(document).ready(() => {
    const table = $("#scores").DataTable({
        ajax: source,
        layout: {
            topStart: {
                pageLength: {
                    text: translations.layout.length
                }
            },
            topEnd: {
                search: {
                    text: translations.layout.search
                }
            },
            bottomStart: {
                info: {
                    text: translations.layout.showing
                }
            }
        },
        columns: [
            {
                data: "username"
            },
            {
                data: "score"
            }
        ],
        order: [[1, 'desc']],
        createdRow: function(row, data) {
            const username = localStorage["username"] ?? "";
            if (data["username"] === username) {
                $(row).addClass('me');
            }
        }
    });

    $("#scoreBtn").click(() => {
        table.ajax.reload();
    });

    $("#scoreModal").on("hidden.bs.modal", function () {
        isModalOpen = false;
    });
});