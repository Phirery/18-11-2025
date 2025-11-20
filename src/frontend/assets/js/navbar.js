// components/navbar.js
fetch("../../components/navbar.html")
  .then(res => res.text())
  .then(data => {
    document.getElementById("navbar").innerHTML = data;

    const loginBtn = document.querySelector(".btn-login");
    const logoutBtn = document.querySelector(".btn-logout");
    const profileItem = document.querySelector('a[href="xemhoso.html"]');
    const dropdownMenu = loginBtn?.nextElementSibling;

    const isLoggedIn = localStorage.getItem("loggedIn") === "true";
    const username = localStorage.getItem("username");

    if (isLoggedIn && loginBtn) {
      // --- Đã đăng nhập ---
      loginBtn.innerHTML = `<i class="fas fa-user me-1"></i> ${username || "Tài khoản"}`;
      loginBtn.removeAttribute("href");

      // Hiện mũi tên dropdown
      loginBtn.classList.add("show-caret");
      loginBtn.setAttribute("data-bs-toggle", "dropdown");
      loginBtn.setAttribute("aria-expanded", "false");

      if (dropdownMenu) dropdownMenu.classList.remove("d-none");
      if (profileItem) profileItem.style.display = "block";
      if (logoutBtn) logoutBtn.style.display = "block";
    } else {
      // --- Chưa đăng nhập ---
      loginBtn.innerHTML = `<i class="fas fa-sign-in-alt me-1"></i> Đăng nhập`;
      loginBtn.href = "dangnhap.html";

      // Ẩn mũi tên dropdown
      loginBtn.classList.remove("show-caret");
      loginBtn.removeAttribute("data-bs-toggle");
      loginBtn.removeAttribute("aria-expanded");

      if (dropdownMenu) dropdownMenu.classList.add("d-none");
      if (profileItem) profileItem.style.display = "none";
      if (logoutBtn) logoutBtn.style.display = "none";
    }

    if (logoutBtn) {
      logoutBtn.addEventListener("click", (e) => {
        e.preventDefault();
        localStorage.removeItem("loggedIn");
        localStorage.removeItem("username");
        window.location.reload();
      });
    }
  })
  .catch(err => console.error("Lỗi khi load navbar:", err));
