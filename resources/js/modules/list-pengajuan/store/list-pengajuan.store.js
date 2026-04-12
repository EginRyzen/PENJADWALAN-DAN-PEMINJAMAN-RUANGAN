import apiUrl from "@/core/plugins/constants/apiUrl";
import actions from "@/core/plugins/constants/actions";
import Api from "@/core/services/Api";
import axios from 'axios';

export const Store = {
  namespaced: true,
  state: {
    pengajuans: [],
    pagination: {
      current_page: 0,
      total_elements: 0,
      total_elements_per_page: 10,
    },
  },
  mutations: {
    SET_PENGAJUANS(state, data) {
      state.pengajuans = data;
    },
    SET_PAGINATION(state, data) {
      state.pagination = {
        current_page: data.current_page,
        total_elements: data.total_elements,
        total_elements_per_page: data.total_elements_per_page,
      };
    },
  },
  actions: {
    async [actions.GET_LIST_PENGAJUAN]({ commit, state }, payload = {}) {
      try {
        const { isAppend, ...params } = payload;
        
        const response = await Api.get(apiUrl.SUBMIT_PENGAJUAN, { params });
        const { result, pagination } = response.data;

        if (isAppend) {
          commit('SET_PENGAJUANS', [...state.pengajuans, ...result]);
        } else {
          commit('SET_PENGAJUANS', result);
        }
        
        commit('SET_PAGINATION', pagination);
        return result;
      } catch (error) {
        console.error('Error fetching pengajuan data:', error);
        throw error;
      }
    },

    async [actions.SUBMIT_PENGAJUAN]({ commit }, payload) {
      try {
        const formData = new FormData();
        
        // Append all form fields to FormData
        formData.append('tipe_pengajuan', payload.tipe_pengajuan);
        formData.append('tanggal_start', payload.tanggal_start);
        formData.append('tanggal_end', payload.tanggal_end);
        formData.append('jam_mulai', payload.jam_mulai);
        formData.append('jam_selesai', payload.jam_selesai);
        formData.append('alasan', payload.alasan);
        
        // Append all_room_ids as array elements
        payload.all_room_ids.forEach((id, index) => {
          formData.append(`all_room_ids[${index}]`, id);
        });

        // Add items structure if backend needs it (Gedung mapping)
        payload.items.forEach((item, index) => {
          formData.append(`items[${index}][building_id]`, item.building_id);
        });

        // Append file if exists
        if (payload.file_raw) {
          formData.append('file_raw', payload.file_raw);
        }

        const response = await Api.post(apiUrl.SUBMIT_PENGAJUAN, payload);

        return response.data;
      } catch (error) {
        console.error('Error submitting pengajuan:', error);
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
        console.error("Error Uploading Document:", error);
        throw error;
      }
    },
  },
};

