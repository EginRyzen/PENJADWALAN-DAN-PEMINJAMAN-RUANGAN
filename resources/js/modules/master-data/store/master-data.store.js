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
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_PROGRAM_STUDI(state, payload) {
            state.programStudiList = payload.content;
            state.pagination = {
                current_page: payload.current_page,
                total_pages: payload.total_pages,
                total_elements: payload.total_elements,
                total_elements_per_page: payload.total_elements_per_page,
            };
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
    },
};
