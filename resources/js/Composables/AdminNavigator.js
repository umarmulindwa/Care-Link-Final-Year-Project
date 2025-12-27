import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { notify } from "@/mixins/notify";
import { showLoader } from "@/mixins/helpers.js";

//accident reporting
function AccidentReportingNavigator(goto) {
    const accidentUrl = import.meta.env.VITE_ACCIDENTREPORTING_URL;
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
        return (window.location.href = `${accidentUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}

// incident reporting
function toIncidentReporting(goto) {
    const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;
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
        return (window.location.href = `${incidentUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}

//diplomatic vehicle registration
function diplomaticVehicleRegistration(goto) {
    const vehicleReg = import.meta.env.VITE_DIPLOMATICVEHICLEREG_URL;
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
        return (window.location.href = `${vehicleReg}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}

// office Supplies
function officeSupplies(goto) {
    const officesupplies = import.meta.env.VITE_OFFICESUPPLIES_URL;
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
        return (window.location.href = `${officesupplies}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}

function goToAssetSubModule(system_index, redirect) {
    const systems = [
        import.meta.env.VITE_ADMIN_ASSET_TAGGING_URL,
        import.meta.env.VITE_ADMIN_ASSET_TRACKING_URL,
        import.meta.env.VITE_ADMIN_ASSET_EQUIPMENT_REQUEST_URL,
        import.meta.env.VITE_ADMIN_ASSET_DISPOSAL_URL,
        import.meta.env.VITE_ADMIN_ASSET_AMR_URL,
    ];

    const url = systems[system_index];

    Swal.fire({
        html: `<p style='fontsize:16px'>Loading ...</p>`,
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false,
    });

    let full_url = `${url}/loginFromMain?token=${usePage().props.userToken}`;

    if (redirect) {
        full_url += `&redirect=${redirect}`;
    }

    return (window.location.href = full_url);
}

const vehicleMaintenance = (goto) => {
    const maintenance = import.meta.env.VITE_ADMIN_VEHICLE_MAINTENANCE_URL;
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
        return (window.location.href = `${maintenance}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};
const vehicleFuelManagement = (goto) => {
    const fuelManagement = import.meta.env.VITE_ADMIN_VEHICLE_FUEL_URL;
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
        return (window.location.href = `${fuelManagement}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};
const vehicleUtilization = (goto) => {
    const utilization = import.meta.env.VITE_ADMIN_VEHICLE_UTILIZATION_URL;
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
        return (window.location.href = `${utilization}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};
const vehiclePrivateUse = (goto) => {
    const privateUse = import.meta.env.VITE_ADMIN_URL;
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
        return (window.location.href = `${privateUse}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};
const administrationSettings = (goto) => {
    const adminSettings = import.meta.env.VITE_ADMIN_URL;
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
        return (window.location.href = `${adminSettings}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};

const carQueueManagement = (goto) => {
    const carPark = import.meta.env.VITE_ADMIN_CARPARK_URL;
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
        return (window.location.href = `${carPark}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};

//airtime & Data
function AirtimeDataNavigator(goto) {
    const airtimeDataUrl = import.meta.env.VITE_AIRTIMEDATA_URL;
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
        return (window.location.href = `${airtimeDataUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
//Incident Reporting
function IncidentReportingNavigator(goto) {
    const incidetReportURL = import.meta.env.VITE_INCIDENTREPORTING_URL;
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
        return (window.location.href = `${incidetReportURL}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
//Guest House
function GuestHouseNavigator(goto) {
    const guestHouseUrl = import.meta.env.VITE_GUESTHOUSE_URL;
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
        return (window.location.href = `${guestHouseUrl}/loginFromSubModule?token=${userToken}&goto=${goto}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
}
//visitor Tracker
const visitortrackerNavigator = () => {
    const VISITORTRACKER_URL = import.meta.env.VITE_VISTORTRACKER_URL;
    const userToken = usePage().props.userToken;

    if (userToken != null) {
        Swal.fire({
            html: `<p style='fontsize:16px'>Loading ...</p>`,
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false,
        });
        return (window.location.href = `${VISITORTRACKER_URL}/loginFromMain?token=${userToken}`);
    } else {
        notify.toastErrorMessage("Invalid Session");
    }
};

export {
    AccidentReportingNavigator,
    diplomaticVehicleRegistration,
    officeSupplies,
    goToAssetSubModule,
    vehicleMaintenance,
    vehicleFuelManagement,
    vehicleUtilization,
    vehiclePrivateUse,
    administrationSettings,
    carQueueManagement,
    AirtimeDataNavigator,
    visitortrackerNavigator,
    IncidentReportingNavigator,
    GuestHouseNavigator,
};
