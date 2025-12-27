import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";

export function BscRequestsNavigator(goto) {
    const bscUrl = import.meta.env.VITE_BSC_REQUESTS_APP_URL;
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
        return (window.location.href = `${bscUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
