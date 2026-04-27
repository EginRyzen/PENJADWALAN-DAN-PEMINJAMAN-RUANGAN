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
    mahasiswaList: [],
    mhsPagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
    dosenList: [],
    dosenPagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
    periodeList: [],
    periodePagination: {
        current_page: 0,
        total_pages: 0,
        total_elements: 0,
        total_elements_per_page: 10,
    },
    kmList: [],
    kmPagination: {
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
        SET_MAHASISWA(state, payload) {
            if (Array.isArray(payload)) {
                state.mahasiswaList = payload;
            } else {
                state.mahasiswaList = payload.content || [];
                state.mhsPagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
        SET_DOSEN(state, payload) {
            if (Array.isArray(payload)) {
                state.dosenList = payload;
            } else {
                state.dosenList = payload.content || [];
                state.dosenPagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
        SET_PERIODE(state, payload) {
            if (!payload) return;
            if (Array.isArray(payload)) {
                state.periodeList = payload;
            } else {
                state.periodeList = payload.content || [];
                state.periodePagination = {
                    current_page: payload.current_page || 0,
                    total_pages: payload.total_pages || 0,
                    total_elements: payload.total_elements || 0,
                    total_elements_per_page: payload.total_elements_per_page || 10,
                };
            }
        },
        SET_KELAS_MATA_KULIAH(state, payload) {
            if (Array.isArray(payload)) {
                state.kmList = payload;
            } else {
                state.kmList = payload.content || [];
                state.kmPagination = {
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
        // Mahasiswa Actions
        async [actions.GET_MAHASISWA]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.MAHASISWA, { params });
                const data = response.data.result;
                commit("SET_MAHASISWA", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Mahasiswa:", error);
                throw error;
            }
        },
        async [actions.CREATE_MAHASISWA]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.MAHASISWA, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Mahasiswa:", error);
                throw error;
            }
        },
        async [actions.UPDATE_MAHASISWA]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.MAHASISWA}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Mahasiswa:", error);
                throw error;
            }
        },
        async [actions.DELETE_MAHASISWA]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.MAHASISWA}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Mahasiswa:", error);
                throw error;
            }
        },
        // Dosen Actions
        async [actions.GET_DOSEN]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.DOSEN, { params });
                const data = response.data.result;
                commit("SET_DOSEN", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Dosen:", error);
                throw error;
            }
        },
        async [actions.CREATE_DOSEN]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.DOSEN, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Dosen:", error);
                throw error;
            }
        },
        async [actions.UPDATE_DOSEN]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.DOSEN}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Dosen:", error);
                throw error;
            }
        },
        async [actions.DELETE_DOSEN]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.DOSEN}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Dosen:", error);
                throw error;
            }
        },
        // Periode Actions
        async [actions.GET_PERIODE]({ commit }, params) {
            try {
                // Debugging: Ensure apiUrl.PERIODE is defined
                const url = apiUrl.PERIODE || "/master-data/periodes";
                if (!apiUrl.PERIODE) console.warn("Warning: apiUrl.PERIODE is undefined, using fallback.");
                
                const response = await Api.get(url, { params });
                const data = response.data.result;
                commit("SET_PERIODE", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Periode:", error);
                throw error;
            }
        },
        async [actions.CREATE_PERIODE]({ commit }, payload) {
            try {
                const url = apiUrl.PERIODE || "/master-data/periodes";
                const response = await Api.post(url, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Periode:", error);
                throw error;
            }
        },
        async [actions.UPDATE_PERIODE]({ commit }, payload) {
            try {
                const url = apiUrl.PERIODE || "/master-data/periodes";
                const response = await Api.put(`${url}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Periode:", error);
                throw error;
            }
        },
        async [actions.DELETE_PERIODE]({ commit }, id) {
            try {
                const url = apiUrl.PERIODE || "/master-data/periodes";
                const response = await Api.delete(`${url}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Periode:", error);
                throw error;
            }
        },
        // Kelas Mata Kuliah Actions
        async [actions.GET_KELAS_MATA_KULIAH]({ commit }, params) {
            try {
                const response = await Api.get(apiUrl.KELAS_MATA_KULIAH, { params });
                const data = response.data.result;
                commit("SET_KELAS_MATA_KULIAH", data);
                return data;
            } catch (error) {
                console.error("Error Fetching Kelas Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.CREATE_KELAS_MATA_KULIAH]({ commit }, payload) {
            try {
                const response = await Api.post(apiUrl.KELAS_MATA_KULIAH, payload);
                return response.data;
            } catch (error) {
                console.error("Error Creating Kelas Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.UPDATE_KELAS_MATA_KULIAH]({ commit }, payload) {
            try {
                const response = await Api.put(`${apiUrl.KELAS_MATA_KULIAH}/${payload.id}`, payload);
                return response.data;
            } catch (error) {
                console.error("Error Updating Kelas Mata Kuliah:", error);
                throw error;
            }
        },
        async [actions.DELETE_KELAS_MATA_KULIAH]({ commit }, id) {
            try {
                const response = await Api.delete(`${apiUrl.KELAS_MATA_KULIAH}/${id}`);
                return response.data;
            } catch (error) {
                console.error("Error Deleting Kelas Mata Kuliah:", error);
                throw error;
            }
        },
    },
};
