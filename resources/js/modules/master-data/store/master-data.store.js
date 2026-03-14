import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

const defaultState = () => ({
    programStudiList: [],
    pagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
    mataKuliahList: [],
    mkPagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_PROGRAM_STUDI(state, payload) {
            if (Array.isArray(payload)) {
                state.programStudiList = payload;
            } else {
                state.programStudiList = payload.content || [];
                state.pagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
        SET_MATA_KULIAH(state, payload) {
            if (Array.isArray(payload)) {
                state.mataKuliahList = payload;
            } else {
                state.mataKuliahList = payload.content || [];
                state.mkPagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
    },
    actions: {
        async [actions.GET_PROGRAM_STUDI]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.PROGRAM_STUDI, { params });
                const data = response.data.result;
                commit("SET_PROGRAM_STUDI", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Program Studi:", error);
                throw error;
            }
        },
        async [actions.CREATE_PROGRAM_STUDI]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.PROGRAM_STUDI, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Program Studi:", error);
                throw error;
            }
        },
        async [actions.UPDATE_PROGRAM_STUDI]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.PROGRAM_STUDI}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Program Studi:", error);
                throw error;
            }
        },
        async [actions.DELETE_PROGRAM_STUDI]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.PROGRAM_STUDI}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Program Studi:", error);
                throw error;
            }
        },
        // Mata Kuliah Actions
        async [actions.GET_MATA_KULIAH]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.MATA_KULIAH, { params });
                const data = response.data.result;
                commit("SET_MATA_KULIAH", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.CREATE_MATA_KULIAH]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.MATA_KULIAH, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.UPDATE_MATA_KULIAH]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.MATA_KULIAH}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.DELETE_MATA_KULIAH]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.MATA_KULIAH}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Mata Kuliah:", error);
                throw error;
            }
        },
    },
};
