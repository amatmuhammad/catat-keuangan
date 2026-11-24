<script>
    (() => {
        "use strict";

        // Tampilkan / sembunyikan password
        document.querySelectorAll("[data-toggle-password]").forEach((button) => {
            button.addEventListener("click", () => {
                const input = document.getElementById(button.dataset.togglePassword);
                if (!input) {
                    return;
                }

                const show = input.type === "password";
                input.type = show ? "text" : "password";
                button.setAttribute("aria-label", show ? "Sembunyikan password" : "Tampilkan password");
                button.setAttribute("aria-pressed", String(show));

                const icon = button.querySelector("i");
                if (icon) {
                    icon.className = show ? "ti ti-eye-off" : "ti ti-eye";
                }
            });
        });

        // Indikator kekuatan password (khusus halaman register)
        const strengthRoot = document.querySelector("[data-password-strength]");
        if (strengthRoot) {
            const input = document.getElementById("password");
            const bar = strengthRoot.querySelector("[data-strength-bar]");
            const text = strengthRoot.querySelector("[data-strength-text]");
            const defaultText = text ? text.textContent.trim() : "";

            const levels = [
                { label: defaultText, color: "var(--ds-danger, #dc2626)", width: "0%" },
                { label: "Lemah — tambahkan huruf besar, angka, atau simbol.", color: "#dc2626", width: "25%" },
                { label: "Cukup — tambahkan variasi karakter agar lebih kuat.", color: "#d97706", width: "50%" },
                { label: "Kuat — password sudah sulit ditebak.", color: "#16a34a", width: "75%" },
                { label: "Sangat kuat — kombinasi karakter sudah ideal.", color: "#15803d", width: "100%" },
            ];

            const scorePassword = (value) => {
                if (!value) {
                    return 0;
                }

                let score = 0;
                if (value.length >= 8) {
                    score += 1;
                }
                if (value.length >= 12) {
                    score += 1;
                }
                if (/[a-z]/.test(value) && /[A-Z]/.test(value)) {
                    score += 1;
                }
                if (/\d/.test(value)) {
                    score += 1;
                }
                if (/[^A-Za-z0-9]/.test(value)) {
                    score += 1;
                }

                return Math.min(4, Math.max(1, score));
            };

            const updateStrength = () => {
                const level = levels[scorePassword(input.value)];

                if (bar) {
                    bar.style.width = level.width;
                    bar.style.backgroundColor = level.color;
                }
                if (text) {
                    text.textContent = level.label;
                }
            };

            input.addEventListener("input", updateStrength);
            input.addEventListener("blur", updateStrength);
            updateStrength();
        }

        // Cegah submit ganda & tampilkan status loading
        document.querySelectorAll("form[data-auth-form]").forEach((form) => {
            let submitting = false;

            form.addEventListener("submit", () => {
                if (submitting) {
                    return;
                }

                const button = form.querySelector("[data-submit-button]");
                if (!button) {
                    return;
                }

                submitting = true;
                button.disabled = true;
                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>' +
                    "<span>" + (button.dataset.loadingText || "Memproses...") + "</span>";
            });
        });

        // Tombol ganti tema terang / gelap
        const themeToggle = document.querySelector("[data-auth-theme-toggle]");
        if (themeToggle) {
            const root = document.documentElement;
            const icon = themeToggle.querySelector("i");

            const syncThemeToggle = () => {
                const isDark = root.getAttribute("data-bs-theme") === "dark";
                if (icon) {
                    icon.className = isDark ? "ti ti-sun" : "ti ti-moon-stars";
                }
                themeToggle.setAttribute("aria-label", isDark ? "Aktifkan mode terang" : "Aktifkan mode gelap");
                themeToggle.setAttribute("aria-pressed", String(isDark));
            };

            themeToggle.addEventListener("click", () => {
                const next = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";

                root.setAttribute("data-bs-theme", next);
                root.style.colorScheme = next;

                try {
                    localStorage.setItem("theme", next);
                } catch (error) {
                    // localStorage bisa diblokir (mis. private mode); abaikan saja.
                }

                const themeColorMeta = document.querySelector('meta[name="theme-color"]');
                if (themeColorMeta) {
                    themeColorMeta.setAttribute("content", next === "dark" ? "#141a21" : "#ffffff");
                }

                window.dispatchEvent(
                    new CustomEvent("themechange", {
                        detail: { theme: next, resolved: next, isDark: next === "dark" },
                    })
                );

                syncThemeToggle();
            });

            syncThemeToggle();
        }
    })();
</script>
