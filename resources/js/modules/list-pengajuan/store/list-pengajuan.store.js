import axios from 'axios';

export const Store = {
  namespaced: true,
  state: {
    pengajuans: [
      {
        id: "1",
        no_pengajuan: "BLDG01-2026-RM101-001",
        tipe_pengajuan: "PEMBELAJARAN",
        ruangan: { room_name: "Lab Komputer 1" },
        user: { name: "Ahmad Dahlan" },
        status: { nama_status: "Menunggu Persetujuan Kaprodi" },
      },
      {
        id: "2",
        no_pengajuan: "BLDG01-2026-RM102-002",
        tipe_pengajuan: "EVENT",
        ruangan: { room_name: "Aula Serbaguna" },
        user: { name: "Siti Aminah" },
        status: { nama_status: "Completed" },
      },
      {
        id: "3",
        no_pengajuan: "BLDG02-2026-RM201-003",
        tipe_pengajuan: "PEMBELAJARAN",
        ruangan: { room_name: "Ruang Kelas 201" },
        user: { name: "Budi Santoso" },
        status: { nama_status: "Menunggu Persetujuan Kaprodi" },
      },
      {
        id: "4",
        no_pengajuan: "BLDG02-2026-RM205-004",
        tipe_pengajuan: "EVENT",
        ruangan: { room_name: "Meeting Room" },
        user: { name: "Dewi Lestari" },
        status: { nama_status: "Completed" },
      },
      {
        id: "5",
        no_pengajuan: "BLDG03-2026-RM301-005",
        tipe_pengajuan: "PEMBELAJARAN",
        ruangan: { room_name: "Laboratorium Fisika" },
        user: { name: "Eko Prasetyo" },
        status: { nama_status: "Koreksi" },
      },
      {
        id: "6",
        no_pengajuan: "BLDG03-2026-RM302-006",
        tipe_pengajuan: "EVENT",
        ruangan: { room_name: "Gedung Olahraga" },
        user: { name: "Farida Utami" },
        status: { nama_status: "Rejected" },
      },
    ],
    pagination: {
      current_page: 0,
      total_elements: 6,
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
    async getPengajuanData({ commit, state }, params) {
      try {
        // Mocking API delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Filtering logic for mock data
        let filtered = [...state.pengajuans];
        
        if (params.search) {
          filtered = filtered.filter(item => 
            item.no_pengajuan.toLowerCase().includes(params.search.toLowerCase())
          );
        }
        
        if (params.tipe) {
          const tipeList = params.tipe.split(',');
          filtered = filtered.filter(item => tipeList.includes(item.tipe_pengajuan));
        }

        // We don't commit back to state to keep the master dummy data intact,
        // unless we want to simulate actual state update for this "view".
        // For hardcoded dummy data in FE, we just let the state hold it and 
        // if we want to simulate filtering, we could return it or update state.
        
        // Let's update state to simulate a real fetch
        commit('SET_PENGAJUANS', filtered);
        commit('SET_PAGINATION', {
          current_page: params.page || 0,
          total_elements: filtered.length,
          total_elements_per_page: params.size || 10,
        });

      } catch (error) {
        console.error('Error fetching pengajuan data:', error);
        throw error;
      }
    },
  },
};
