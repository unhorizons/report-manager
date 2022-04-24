import Swal from 'sweetalert2';

export const confirmSwalMixin = Swal.mixin({
    text: 'Êtes-vous sûr de vouloir effectué cette action ?',
    showDenyButton: false,
    showCancelButton: true,
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-dim btn-secondary'
    },
    buttonsStyling: false
});

export const toastSwalMixin = Swal.mixin({
    toast: true,
    position: 'top-end',
    timer: 5000,
    showCloseButton: true,
    timerProgressBar: true,
    showConfirmButton: false,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

export const installServiceWorker = async (onConfirm) => {
    await Swal.fire({
            position: 'bottom',
            text: 'Une nouvelle version du site est disponible',
            confirmButtonText: 'Installer',
            cancelButtonText: 'Annuler',
            showCloseButton: true,
            showCancelButton: true,
            showDenyButton: false,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'w-max mr-2 bg-green-500 border-2 border-green-500 text-sm text-gray-800 font-semibold hover:bg-green-600 hover:border-green-600 focus:bg-green-700 focus:border-green-700 active:bg-green-800 active:text-white active:border-green-800 transition-all duration-100 lg:shadow-xl block py-3 px-6 rounded',
                cancelButton: 'w-max ml-2 bg-gray-300 border-2 border-gray-300 text-gray-600 font-semibold hover:bg-gray-600 hover:border-gray-600 focus:bg-gray-700 focus:border-gray-700 active:bg-gray-800 transition-all duration-100 lg:shadow-xl block py-3 px-6 rounded'
            }
        })
        .then((result) => {
            if (result.isConfirmed) {
                onConfirm();
            }
        })
}

export const confirmation = async (action, onConfirm, onCancel) => {
    await confirmSwalMixin
        .fire({
            confirmButtonText: action,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-dim btn-light',
            }
        })
        .then((result) => {
            if (result.isConfirmed) {
                onConfirm();
            }
        });
}

export const toast = async (type, message) => {
    await toastSwalMixin.fire({
        icon: type,
        html: message,
    });
}
