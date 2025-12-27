
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";
import {showLoader} from "@/mixins/helpers.js";


export function LowValueNavigator() {
    const LOW_VALUE_URL = import.meta.env.VITE_LOW_VALUE_PROCUREMENT_URL;

    Swal.fire({
        html: `<p style='font-size:16px'>Loading ...</p>`,
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false,
    });

    return (window.location.href = `${LOW_VALUE_URL}/loginFromMain?token=${usePage().props.userToken}`);
}
