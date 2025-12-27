
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";


export function SupplyPlanNavigator(goto) {
    const supplyPlanUrl = import.meta.env.VITE_SUPPLY_PLANNING_URL;
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
          return (window.location.href = `${supplyPlanUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
      } else {
          notify.toastErrorMessage("Invalid Session");
      }

}
