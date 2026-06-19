
function formatRupiah(angka) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  })
    .format(angka)
    .replace("IDR", "Rp");
}

/**
 * Sanitasi string inputan sederhana via client-side
 * @param {string} text
 */
function cleanInputText(text) {
  return text.replace(/[/\\*^$&+##]/g, "");
}
