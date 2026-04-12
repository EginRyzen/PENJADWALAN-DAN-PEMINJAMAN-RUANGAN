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
    operasionalScheduleList: [],
    hariLiburList: [],
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
        SET_OPERASIONAL_SCHEDULE(state, payload) {
            state.operasionalScheduleList = payload;
        },
        SET_HARI_LIBUR(state, payload) {
            if (Array.isArray(payload)) {
                state.hariLiburList = payload;
            } else {
                state.hariLiburList = payload.content || [];
            }
        },
    },
    actions: {
        async [actions.GET_OPERASIONAL_SCHEDULE]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.OPERASIONAL_SCHEDULE, { params });
                const data = response.data.result;
                commit("SET_OPERASIONAL_SCHEDULE", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Operasional Schedule:", error);
                throw error;
            }
        },
        async [actions.UPDATE_OPERASIONAL_SCHEDULE]({ commit }, payload) {
            try {
                const url = payload.schedules 
                    ? `${apiUrl.OPERASIONAL_SCHEDULE}/bulk-update` 
                    : `${apiUrl.OPERASIONAL_SCHEDULE}/${payload.id}`;
                
                const response = await Api.post(url, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Operasional Schedule:", error);
                throw error;
            }
        },
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
        async [actions.GET_HARI_LIBUR]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.HARI_LIBUR, { params });
                const data = response.data.result;
                commit("SET_HARI_LIBUR", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Hari Libur:", error);
                throw error;
            }
        },
        async [actions.CREATE_HARI_LIBUR]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.HARI_LIBUR, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Hari Libur:", error);
                throw error;
            }
        },
        async [actions.UPDATE_HARI_LIBUR]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.HARI_LIBUR}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Hari Libur:", error);
                throw error;
            }
        },
        async [actions.DELETE_HARI_LIBUR]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.HARI_LIBUR}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Hari Libur:", error);
                throw error;
            }
        },
    },
};
