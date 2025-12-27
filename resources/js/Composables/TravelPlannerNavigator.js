import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";

export function TravelPlannerNavigator(system_index) {
    const systems = [import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL];
    const url = systems[system_index];
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
        return (window.location.href = `${url}/loginFromMain?token=${userToken}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
