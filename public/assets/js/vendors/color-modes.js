// Dark / light mode
//
// Menyimpan preferensi di localStorage ("light", "dark", atau "auto"),
// menerapkan tema ke <html data-bs-theme="...">, dan memberi tahu komponen
// lain (mis. chart) lewat event "themechange".

(() => {
   "use strict";

   const STORAGE_KEY = "theme";
   const THEMES = ["light", "dark", "auto"];
   const media = window.matchMedia("(prefers-color-scheme: dark)");

   const getStoredTheme = () => {
      const stored = localStorage.getItem(STORAGE_KEY);
      return THEMES.includes(stored) ? stored : "auto";
   };

   const setStoredTheme = (theme) => {
      try {
         localStorage.setItem(STORAGE_KEY, theme);
      } catch (e) {
         // localStorage bisa diblokir (mis. private mode); abaikan saja.
      }
   };

   // "auto" akan diterjemahkan menjadi "light" / "dark" sesuai sistem.
   const resolveTheme = (theme) => {
      if (theme === "auto") {
         return media.matches ? "dark" : "light";
      }
      return theme === "dark" ? "dark" : "light";
   };

   const getPreferredTheme = () => getStoredTheme();

   const setTheme = (theme) => {
      const resolved = resolveTheme(theme);
      const root = document.documentElement;

      root.setAttribute("data-bs-theme", resolved);
      // Membantu browser menampilkan scrollbar & form control yang sesuai.
      root.style.colorScheme = resolved;

      // Samakan warna address bar / status bar di browser mobile.
      const themeColorMeta = document.querySelector('meta[name="theme-color"]');
      if (themeColorMeta) {
         themeColorMeta.setAttribute("content", resolved === "dark" ? "#141a21" : "#ffffff");
      }

      // Beri kesempatan chart / komponen lain untuk menyesuaikan warna.
      window.dispatchEvent(
         new CustomEvent("themechange", {
            detail: { theme, resolved, isDark: resolved === "dark" },
         })
      );
   };

   const updateDropdownIcon = (theme) => {
      const themeIconActive = document.querySelector(".theme-icon-active");
      if (!themeIconActive) return;

      const iconElement = document.querySelector(`[data-bs-theme-value="${theme}"] .theme-icon`);
      if (iconElement) {
         themeIconActive.innerHTML = iconElement.outerHTML;
      }
   };

   const showActiveTheme = (theme, focus = false) => {
      const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
      if (!btnToActive) return;

      document.querySelectorAll("[data-bs-theme-value]").forEach((element) => {
         element.classList.remove("active");
         element.setAttribute("aria-pressed", "false");
      });

      btnToActive.classList.add("active");
      btnToActive.setAttribute("aria-pressed", "true");

      updateDropdownIcon(theme);

      if (focus) {
         btnToActive.focus();
      }
   };

   // Terapkan secepat mungkin (sebelum CSS di-render) supaya tidak berkedip.
   setTheme(getPreferredTheme());

   // Ikuti perubahan tema sistem saat pengguna memilih "auto".
   const handleSystemThemeChange = () => {
      if (getStoredTheme() === "auto") {
         setTheme("auto");
      }
   };
   media.addEventListener("change", handleSystemThemeChange);

   // Sinkronkan antar-tab.
   window.addEventListener("storage", (event) => {
      if (event.key !== STORAGE_KEY) return;
      const theme = getStoredTheme();
      setTheme(theme);
      showActiveTheme(theme);
   });

   window.addEventListener("DOMContentLoaded", () => {
      showActiveTheme(getPreferredTheme());

      document.querySelectorAll("[data-bs-theme-value]").forEach((toggle) => {
         toggle.addEventListener("click", () => {
            const theme = toggle.getAttribute("data-bs-theme-value");
            if (!THEMES.includes(theme)) return;

            setStoredTheme(theme);
            setTheme(theme);
            showActiveTheme(theme, true);
         });
      });
   });
})();
