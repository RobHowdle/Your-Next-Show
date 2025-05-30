import Swal from "sweetalert2";
import $ from "jquery";

export function updateModuleStatus(moduleName, dashboardType, enabled, userId) {
    $.ajax({
        url: `/profile/${dashboardType}/settings/update`,
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            module: moduleName,
            enabled: enabled ? 1 : 0,
            userId: userId,
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    title: "Success!",
                    text: `${moduleName} module has been ${
                        enabled ? "enabled" : "disabled"
                    }.`,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        },
        error: function (error) {
            Swal.fire({
                title: "Error!",
                text: "Failed to update module settings.",
                icon: "error",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        },
    });
}
