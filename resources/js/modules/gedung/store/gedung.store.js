import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";

const defaultState = () => ({
    facilities: [],
    buildings: [],
    pagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10
    }
});

export const Store = {
    namespaced: true,
    state: defaultState(),
    mutations: {
        SET_GEDUNG_FACILITIES(state, data) {
            state.facilities = data;
        },
        SET_BUILDINGS(state, payload) {
            state.buildings = payload.content;
            state.pagination = {
                current_page: payload.current_page,
                total_pages: payload.total_pages,
                total_elements: payload.total_elements,
                total_elements_per_page: payload.total_elements_per_page
            };
        },
    },
    actions: {
        async [actions.GET_GEDUNG_DATA]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.GET_GEDUNG, { params });

                const data = response.data.result;
                commit("SET_BUILDINGS", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Gedung Data:", error);
                throw error;
            }
        },
        async [actions.GET_DETAIL_GEDUNG_DATA]({ commit }, id) {
            try {
                const response = await Api.get(`${apiUrl.GET_DETAIL_GEDUNG}/${id}`);
                return response.data.result;
            } catch (error) {
                console.error("Error Fetching Detail Gedung Data:", error);
                throw error;
            }
        },
        async [actions.GET_ROOM_FACILITIES]({ commit }, roomId) {
            try {
                const response = await Api.get(`${apiUrl.GET_ROOM_FACILITIES}/${roomId}/facilities`);
                return response.data;
            } catch (error) {
                console.error("Error Fetching Room Facilities Data:", error);
                throw error;
            }
        },
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
        async [actions.UPLOAD_IMAGE]({ commit }, file) {
            try {
                const formData = new FormData();
                formData.append("file", file);

                const response = await Api.post(apiUrl.UPLOAD_IMAGE, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });
                return response.data.result;
            } catch (error) {
                console.error("Error Uploading Image:", error);
                throw error;
            }
        },
        async [actions.SAVE_GEDUNG_DATA]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.SAVE_GEDUNG, payload);
                return response.data;
            } catch (error) {
                console.error("Error Saving Gedung Data:", error);
                throw error;
            }
        },
        async [actions.UPDATE_GEDUNG_DATA]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.UPDATE_GEDUNG}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Gedung Data:", error);
                throw error;
            }
        },
        async [actions.GET_BUILDINGS_ONLY]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.GET_BUILDINGS_ONLY, { params });
                return response.data.result;
            } catch (error) {
                console.error("Error Fetching Buildings Only:", error);
                throw error;
            }
        },
    },
};
