import { RequestHelper } from "@/mixins/helpers";
const beneficiaryStore = {
    namespaced: true,
    state: {
        beneficiaries: [],
        approvedParticipants: [],
        allParticipants: [],
    },
    mutations: {
        setBeneficiaries(state, beneficiaries) {
            state.beneficiaries = beneficiaries;
        },
        setApprovedParticipants(state, payload) {
            state.approvedParticipants = payload.approved;
            state.allParticipants = payload.all;
        },
    },
    actions: {
        async storeApprovedParticipants({commit},payload){
            console.log({payload});
            commit('setApprovedParticipants', payload)
        },
        async getReviewBeneficiaries({ commit }, payload) {
            const response = await RequestHelper.getRequest(
                "/api/beneficiaries/review"+payload.groupId,
                {
                    ...payload.params
                },
                payload.token
            );
            commit("setBeneficiaries", response.data.results);
        },

        async getReconciledBeneficiaries({ commit }, payload) {
            const response = await RequestHelper.getRequest(
                "/api/beneficiaries/reconciled"+payload.groupId,
                {
                    ...payload.params
                },
                payload.token
            );
            commit("setBeneficiaries", response.data.results);
        },

        async getSampleBeneficiaries({ commit }, payload) {
            const response = await RequestHelper.getRequest(
                "/api/beneficiaries/sample"+payload.groupId,
                {
                    ...payload.params
                },
                payload.token
            );
            commit("setBeneficiaries", response.data.results);
        },
    },
    getters: {
        getBeneficiaries: (state) => {
            return state.beneficiaries;
        },
        getSummary: (state) => {
            return {approved:state.approvedParticipants,all:state.allParticipants};
        },
    },
}

export default beneficiaryStore;
