const modalStore = {
    namespaced: true,
    state: () => ({
        showCancelInvoiceModal: false,
    }),
    mutations: {
        showCancelInvoiceModal(state) {
            state.showCancelInvoiceModal = true;
        },
        hideCancelInvoiceModal(state) {
            state.showCancelInvoiceModal = false;
        },
    },
    actions: {
        showCancelInvoiceModal(context) {
            context.commit("showCancelInvoiceModal");
        },
        hideCancelInvoiceModal(context) {
            context.commit("hideCancelInvoiceModal");
        },
    },
    getters: {
        showCancelInvoiceModal(state) {
            return state.showCancelInvoiceModal;
        },
    },
};

export default modalStore;
