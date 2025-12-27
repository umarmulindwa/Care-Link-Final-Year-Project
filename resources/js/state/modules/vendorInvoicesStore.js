import { RequestHelper } from "@/mixins/helpers";

const vendorInvoiceStore = {
    namespaced: true,
    state: () => ({
        dateRanges: null,
        submittedInvoices: [],
        invoiceCount: null,
        invoicesList: null,
        startDate: null,
        endDate: null,
        monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
        vendorLogs: [],
    }),

    mutations: {
        updateSubmissionMutation: (state, payload) => {
            const { invoices, dateRanges, startDate, endDate } = payload;
            state.submittedInvoices = invoices;
            state.dateRanges = dateRanges;
            state.startDate = startDate;
            state.endDate = endDate;
        },
        storeVendorLogs(state, payload) {
            state.vendorLogs = payload;
        },
    },

    actions: {
        updateSubmissionAction: ({ commit }, payload) => {
            commit("updateSubmissionMutation", payload);
        },
        getVendorLogs({ commit }, payload) {
            RequestHelper.getRequest(payload.url, payload.params, payload.token)
                .then(({ data }) => {
                    commit("storeVendorLogs", data.results);
                })
                .catch((err) => {
                    commit("storeVendorLogs", []);
                });
        },
    },

    getters: {
        dataset: (state) => {
            if (state.dateRanges == null) {
                return {};
            }
            let yAxis = [];
            let xAxisValue = [];

            if (state.dateRanges == null) {
                return { xAxisValue, yAxis };
            }

            xAxisValue = Object.keys(state.dateRanges).map((e, i) => {
                let item = e.split("-");
                var month = "";
                if (item.length > 1) {
                    month = state.monthNames[parseInt(item[1]) - 1];
                    let filter = state.submittedInvoices.filter((s) => s.year == item[0] && s.month == item[1]);
                    if (filter.length > 0) {
                        yAxis[i] = filter[0]["data"];
                    } else {
                        yAxis[i] = 0;
                    }
                }
                return `${item[0]}-${month}`;
            });
            return { xAxisValue, yAxis };
        },
    },
};

export default vendorInvoiceStore;
