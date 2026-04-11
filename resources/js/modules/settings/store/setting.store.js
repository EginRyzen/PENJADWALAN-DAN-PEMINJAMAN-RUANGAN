import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

const defaultState = () => ({
    kelasList: [],
    kelasPagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
    sksSetting: null,
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_KELAS(state, payload) {
            if (Array.isArray(payload)) {
                state.kelasList = payload;
            } else {
                state.kelasList = payload.content || [];
                state.kelasPagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
        SET_SKS_SETTING(state, payload) {
            state.sksSetting = payload;
        },
    },
    actions: {
        async [actions.GET_SKS_SETTING]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.SKS_SETTING, { params });
                const data = response.data.result;
                commit("SET_SKS_SETTING", data);
                return data;
            } catch (error) {
                console.error("Error Fetching SKS Setting:", error);
                throw error;
            }
        },
        async [actions.UPDATE_SKS_SETTING]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.SKS_SETTING}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating SKS Setting:", error);
                throw error;
            }
        },
        async [actions.GET_KELAS]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.KELAS, { params });
                const data = response.data.result;
                commit("SET_KELAS", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Kelas:", error);
                throw error;
            }
        },
        async [actions.CREATE_KELAS]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.KELAS, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Kelas:", error);
                throw error;
            }
        },
        async [actions.UPDATE_KELAS]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.KELAS}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Kelas:", error);
                throw error;
            }
        },
        async [actions.DELETE_KELAS]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.KELAS}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Kelas:", error);
                throw error;
            }
        },
    },
};
