
function toggleAdminModal(modalId, action) {
  const targetModal = document.getElementById(modalId);
  if (!targetModal) return;

  if (action) {
    targetModal.classList.remove("hidden");
  } else {
    targetModal.classList.add("hidden");
  }
}

/**
 * Membuka modal edit dengan menyuntikkan data baris master buah
 * @param {Object} buahData - Data JSON buah tunggal
 */
function populateAndOpenEditModal(buahData) {
  document.getElementById("edit-id-buah").value = buahData.id_buah;
  document.getElementById("edit-nama-buah").value = buahData.nama_buah;
  document.getElementById("edit-harga").value = buahData.harga;
  document.getElementById("edit-stok").value = buahData.stok;
  document.getElementById("edit-deskripsi").value = buahData.deskripsi || "";
  document.getElementById("edit-status-tampil").value =
    buahData.status_tampil || "on";

  toggleAdminModal("modal-edit-buah", true);
}
