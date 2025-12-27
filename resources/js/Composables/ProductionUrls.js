import { reactive } from "vue";

const state = reactive({
    urls: {
        bsc: "https://bsc.v2.mlwdigitalops.org",
        leave: "https://leave.v2.mlwdigitalops.org",
        hr: "https://hr.v2.mlwdigitalops.org",
        accidentReporting: "https://vehiclemanager.accident.v2.mlwdigitalops.org",
        vistorTracker: "https://vehiclemanager.visitortracker.v2.mlwdigitalops.org",
        vehicleRegistration: "https://vehiclemanager.registration.v2.mlwdigitalops.org",
        supplyPlanning: "https://supply.planning.v2.mlwdigitalops.org",
        lowValueSupply: "https://supply.lowvalue.v2.mlwdigitalops.org",
        lowValueProcurement: "https://supply.lowvalue.v2.mlwdigitalops.org",
        tagging: "https://assetmanager.tagging.v2.mlwdigitalops.org",
        tracking: "https://assetmanager.tracking.v2.mlwdigitalops.org",
        equipmentRequest: "https://vehiclemanager.equipment.v2.mlwdigitalops.org",
        disposal: "https://assetmanager.disposal.v2.mlwdigitalops.org",
    },
});
export function productionUrls() {
    return state.urls;
}
