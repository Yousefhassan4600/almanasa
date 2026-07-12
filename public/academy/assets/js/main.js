// بنغلف الكود كله جوا DOMContentLoaded عشان نضمن إن الـ HTML اتحمل تماماً أولاً
document.addEventListener("DOMContentLoaded", function () {
  // === 1. قائمة المواد (Desktop Dropdown) ===
  const button = document.getElementById("dropdownNvbarButton");
  const dropdown = document.getElementById("dropdownNavbar");

  if (button && dropdown) {
    button.addEventListener("click", function (e) {
      e.stopPropagation(); // عشان نمنع نقرة الزرار إنها تفتح وتقفل في نفس اللحظة بسبب حدث الـ window
      dropdown.classList.toggle("hidden");
    });

    // إغلاق القائمة عند النقر في أي مكان خارجها
    window.addEventListener("click", function (e) {
      if (!button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add("hidden");
      }
    });
  }

  // === 2. السايد بار للموبايل (Mobile Sidebar) ===
  const openBtn = document.getElementById("openSidebarBtn");
  const closeBtn = document.getElementById("closeSidebarBtn");
  const sidebar = document.getElementById("mobileSidebar");
  const overlay = document.getElementById("sidebarOverlay");

  // دالة الإغلاق الآمنة
  function closeSidebar() {
    if (sidebar) sidebar.classList.add("translate-x-full");
    if (overlay) {
      overlay.classList.remove("opacity-100");
      setTimeout(() => overlay.classList.add("hidden"), 300);
    }
  }

  // تشغيل السايد بار عند الضغط على زر الفتح (تأمين بـ if)
  if (openBtn && sidebar && overlay) {
    openBtn.addEventListener("click", () => {
      sidebar.classList.remove("translate-x-full");
      overlay.classList.remove("hidden");
      setTimeout(() => overlay.classList.add("opacity-100"), 10);
    });
  }
  console.log({
    openBtn,
    sidebar,
    overlay,
  });
  // تشغيل أزرار الإغلاق
  if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
  if (overlay) overlay.addEventListener("click", closeSidebar);

  // === 3. قائمة المواد داخل الموبايل (Mobile Dropdown) ===
  const mobileDropdownBtn = document.getElementById("mobileDropdownBtn");
  const mobileDropdownContent = document.getElementById(
    "mobileDropdownContent",
  );
  const mobileArrow = document.getElementById("mobileArrow");

  if (mobileDropdownBtn && mobileDropdownContent) {
    mobileDropdownBtn.addEventListener("click", () => {
      mobileDropdownContent.classList.toggle("hidden");
      if (mobileArrow) mobileArrow.classList.toggle("rotate-180");
    });
  }
});
