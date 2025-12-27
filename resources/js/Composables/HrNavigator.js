import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";


export function HrNavigator(redirect) {
    const hrURL = import.meta.env.VITE_HR_URL;

    Swal.fire({
        html: `<p style='fontsize:16px'>Loading ...</p>`,
        didOpen: () => {
        Swal.showLoading();
        },
        allowOutsideClick: false,
    });

    let full_url = `${hrURL}/loginFromMain?token=${usePage().props.userToken}`;

    if(redirect){
        full_url += `&redirect=${redirect}`
    }

    return window.location.href = full_url;

}
