import axios from "axios";

const Api = axios.create({
  baseURL: "/api", 
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
});

Api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Fungsi untuk membuat Modal Error Global menggunakan komponen Modal DaisyUI
function showGlobalErrorModal(message) {
  let modalContainer = document.getElementById('global-error-modal');
  if (!modalContainer) {
    modalContainer = document.createElement('dialog');
    modalContainer.id = 'global-error-modal';
    // Menyesuaikan posisi modal dari bawah untuk mobile dan tengah untuk desktop
    modalContainer.className = 'modal modal-bottom sm:modal-middle z-[99999]';
    document.body.appendChild(modalContainer);
  }

  // Isi modal menggunakan struktur DaisyUI dengan styling premium (White/Teal/Red)
  modalContainer.innerHTML = `
    <div class="modal-box bg-white border border-gray-100 shadow-2xl rounded-2xl p-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-red-50 rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="font-black text-lg text-gray-800">Pemberitahuan</h3>
      </div>
      
      <p class="py-2 text-gray-600 font-medium leading-relaxed">${message}</p>
      
      <div class="modal-action mt-6">
        <form method="dialog">
          <button class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all duration-200 shadow-lg shadow-gray-200">
            Tutup
          </button>
        </form>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop bg-gray-900/40 backdrop-blur-sm">
      <button>close</button>
    </form>
  `;

  // Tampilkan modal menggunakan fungsi HTML5 bawaan
  modalContainer.showModal();
}

// Menangkap semua response error secara global
Api.interceptors.response.use(
  (response) => response,
  (error) => {
    const message = error.response?.data?.message || "Terjadi kesalahan pada jaringan atau server.";
    showGlobalErrorModal(message);
    return Promise.reject(error);
  }
);

export default Api;