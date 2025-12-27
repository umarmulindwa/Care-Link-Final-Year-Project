import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";

export function IctNavigator(goto) {
    const ictUrl = import.meta.env.VITE_ICT_ACCESS_URL;
    const userToken = usePage().props.userToken;

    if (userToken != null) {
        // showLoader("Loading...");
        Swal.fire({
            html: `<p style='fontsize:16px'>Loading ...</p>`,
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false,
        });
        return (window.location.href = `${ictUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
