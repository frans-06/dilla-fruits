
function triggerToastNotification(pesan) {
  const toastElement = document.getElementById("toast-notif");
  const msgElement = document.getElementById("toast-msg");

  if (!toastElement || !msgElement) return;

  msgElement.innerText = pesan;

  // Geser ke atas dan buat solid (Show)
  toastElement.classList.remove("translate-y-20", "opacity-0");
  toastElement.classList.add("translate-y-0", "opacity-100");

  // Sembunyikan otomatis kembali ke bawah setelah berdurasi 3 detik
  setTimeout(() => {
    toastElement.classList.remove("translate-y-0", "opacity-100");
    toastElement.classList.add("translate-y-20", "opacity-0");
  }, 3000);
}

/**
 * Sinkronisasi menghitung kuantitas unik item belanjaan di LocalStorage
 * untuk disuntikkan ke dalam angka badge merah penanda keranjang di Navbar atas
 */
function refreshNavbarCartBadge() {
  const cartData = JSON.parse(localStorage.getItem("cart_user")) || [];
  const badgeElement = document.getElementById("badge-cart");

  if (badgeElement) {
    if (cartData.length > 0) {
      badgeElement.innerText = cartData.length;
      badgeElement.classList.remove("hidden");
    } else {
      badgeElement.classList.add("hidden");
    }
  }
}
