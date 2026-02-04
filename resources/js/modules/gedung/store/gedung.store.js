import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

const defaultState = () => ({
    facilities: [],
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_GEDUNG_FACILITIES(state, data) {
            state.facilities = data;
        },
    },
    actions: {
        async [actions.GET_GEDUNG_FACILITIES]({ commit }) {
            try {
                const response = await Api.get(apiUrl.GEDUNG_FACILITIES);
                const data = response.data.result;
                commit("SET_GEDUNG_FACILITIES", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Gedung Facilities:", error);
                throw error;
            }
        },
        async [actions.SAVE_GEDUNG_DATA]({ commit }, formData) {
            try {
                const response = await Api.post(apiUrl.SAVE_GEDUNG, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                return response.data;
            } catch (error) {
                console.error("Error Saving Gedung Data:", error);
                throw error;
            }
        },
    },
};