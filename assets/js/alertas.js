// alertas.js
import Swal from "https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm";

export function mostrarExito(titulo = 'Éxito', mensaje = 'Operación realizada correctamente') {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'success',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-btn-exito'
        }
    });
}

export function mostrarError(titulo = 'Error', mensaje = 'Algo salió mal') {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'error',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-btn-error'
        }
    });
}

export async function mostrarConfirmacion(
    titulo = '¿Está seguro?',
    mensaje = 'Esta acción no se puede deshacer'
) {
    return await Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-btn-advertencia',
            cancelButton: 'swal-btn-cancelar'
        }
    });
}
