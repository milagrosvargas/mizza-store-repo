// ==========================================================
// 🧩 TOASTS DE NOTIFICACIÓN GLOBAL (para auditoría / feedback)
// ==========================================================
document.addEventListener("DOMContentLoaded", () => {

    // Crear contenedor global si no existe
    let container = document.getElementById("toastContainer");
    if (!container) {
        container = document.createElement("div");
        container.id = "toastContainer";
        container.className = "toast-container position-fixed top-0 end-0 p-3";
        document.body.appendChild(container);
    }

    // Función reutilizable
    function mostrarToastBootstrap(mensaje, tipo = "success") {
        const toast = document.createElement("div");
        toast.className = `toast align-items-center text-bg-${tipo} border-0 show shadow`;
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, { delay: 10000 });
        bsToast.show();

        toast.addEventListener("hidden.bs.toast", () => toast.remove());
    }

    // ==========================================================
    // 🔔 Escuchar el evento "passwordUpdated"
    // ==========================================================
    document.addEventListener("passwordUpdated", (e) => {
        const detalle = e.detail;
        const msg = ` ${detalle.mensaje} (${detalle.fecha})`;
        mostrarToastBootstrap(msg, "success");
    });
});
