import { createStore } from "vuex";

import LoggedInUser from "./modules/LoggedInUser";
import LayoutState from "./modules/layout";
import vendorInvoiceStore from "./modules/vendorInvoicesStore";
import modalStore from "./modules/modalStore";
import beneficiaryStore from "./modules/beneficiaryStore";

const store = createStore({
    modules: {
        LoggedInUser,
        LayoutState,
        vendorInvoiceStore,
        modalStore,
        beneficiaryStore
    },

    // Enable strict mode in development to get a warning
    // when mutating state outside of a mutation.
    // https://vuex.vuejs.org/guide/strict.html
    strict: process.env.NODE_ENV !== "production",
});

export default store;
