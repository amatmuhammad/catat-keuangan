(function () {
    'use strict';

    const DISMISS_KEY = 'cashflow-install-dismissed-at';
    const DISMISS_DAYS = 7;
    const standalone = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;

    let deferredPrompt = null;

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {
                // Registrasi service worker gagal (misalnya bukan HTTPS).
            });
        });
    }

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredPrompt = event;
        document.dispatchEvent(new CustomEvent('pwa:installable'));
    });

    window.addEventListener('appinstalled', function () {
        deferredPrompt = null;
        hideInstallBanner();

        const navItem = document.getElementById('installAppNavItem');
        if (navItem) {
            navItem.classList.add('d-none');
        }

        const installButton = document.getElementById('installAppBtn');
        if (installButton && !navItem) {
            installButton.classList.add('d-none');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        initInstallPrompt();
        initInstallButton();
        initPushNotifications();
    });

    function initInstallPrompt() {
        const banner = document.getElementById('pwaInstallBanner');

        if (!banner || standalone) {
            return;
        }

        const installButton = document.getElementById('pwaInstallBtn');
        const dismissButton = document.getElementById('pwaDismissBtn');
        const closeButton = document.getElementById('pwaCloseBtn');
        const iosHint = document.getElementById('iosInstallHint');
        const installText = document.getElementById('pwaInstallText');
        const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);

        function recentlyDismissed() {
            const dismissedAt = Number(window.localStorage.getItem(DISMISS_KEY) || 0);

            return dismissedAt > 0 && (Date.now() - dismissedAt) < (DISMISS_DAYS * 86400000);
        }

        function showBanner() {
            if (!recentlyDismissed()) {
                banner.classList.remove('d-none');
            }
        }

        function dismissBanner() {
            window.localStorage.setItem(DISMISS_KEY, String(Date.now()));
            banner.classList.add('d-none');
        }

        if (deferredPrompt) {
            showBanner();
        }

        document.addEventListener('pwa:installable', showBanner);

        if (isIos) {
            if (iosHint) {
                iosHint.classList.remove('d-none');
            }
            if (installText) {
                installText.classList.add('d-none');
            }
            if (installButton) {
                installButton.classList.add('d-none');
            }
            showBanner();
        }

        if (installButton) {
            installButton.addEventListener('click', async function () {
                await triggerInstall();
                banner.classList.add('d-none');
            });
        }

        if (dismissButton) {
            dismissButton.addEventListener('click', dismissBanner);
        }

        if (closeButton) {
            closeButton.addEventListener('click', dismissBanner);
        }
    }

    async function triggerInstall() {
        if (!deferredPrompt) {
            return false;
        }

        const prompt = deferredPrompt;
        deferredPrompt = null;
        prompt.prompt();

        try {
            await prompt.userChoice;
        } catch (error) {
            // Pemasangan dibatalkan pengguna.
        }

        document.dispatchEvent(new CustomEvent('pwa:installed-prompt'));
        return true;
    }

    function initInstallButton() {
        const navItem = document.getElementById('installAppNavItem');
        const modal = document.getElementById('installAppModal');
        const confirmButton = document.getElementById('installAppConfirmBtn');
        const autoSection = document.getElementById('installAutoSection');
        const manualSection = document.getElementById('installManualSection');
        const browserHint = document.getElementById('installBrowserHint');

        const installButton = document.getElementById('installAppBtn');

        // Sembunyikan tombol jika aplikasi sudah terpasang (atau tidak ada tombol).
        if (standalone || !installButton) {
            return;
        }

        if (navItem) {
            navItem.classList.remove('d-none');
        }

        const userAgent = window.navigator.userAgent;
        const isIos = /iphone|ipad|ipod/i.test(userAgent);
        const isChrome = /chrome|crios/i.test(userAgent) && !/edg|opr|samsungbrowser/i.test(userAgent);

        if (browserHint) {
            if (isIos) {
                browserHint.innerHTML = 'Ketuk ikon <span class="fw-semibold">Bagikan</span> di bawah, lalu pilih ' +
                    '<span class="fw-semibold">Tambah ke Layar Utama</span>.';
            } else if (isChrome) {
                browserHint.innerHTML = 'Ketuk ikon <span class="fw-semibold">⋮</span> (titik tiga) di pojok kanan atas ' +
                    'Chrome, lalu pilih <span class="fw-semibold">Pasang aplikasi</span>.';
            }
        }

        function refreshModalSections() {
            if (!autoSection || !manualSection) {
                return;
            }

            if (deferredPrompt) {
                autoSection.classList.remove('d-none');
                manualSection.classList.add('d-none');
            } else {
                autoSection.classList.add('d-none');
                manualSection.classList.remove('d-none');
            }
        }

        let modalBackdrop = null;

        function openInstallModal() {
            if (!modal) {
                return;
            }

            refreshModalSections();

            modalBackdrop = document.createElement('div');
            modalBackdrop.className = 'modal-backdrop show';
            modalBackdrop.id = 'installModalBackdrop';
            document.body.appendChild(modalBackdrop);

            modal.style.display = 'block';
            modal.classList.add('show');
            modal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');

            modalBackdrop.addEventListener('click', closeInstallModal);
        }

        function closeInstallModal() {
            if (!modal) {
                return;
            }

            modal.style.display = '';
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');

            if (modalBackdrop) {
                modalBackdrop.remove();
                modalBackdrop = null;
            }
        }

        installButton.addEventListener('click', openInstallModal);

        document.addEventListener('pwa:installable', refreshModalSections);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal && modal.classList.contains('show')) {
                closeInstallModal();
            }
        });

        if (modal) {
            modal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (button) {
                button.addEventListener('click', closeInstallModal);
            });
        }

        if (confirmButton) {
            confirmButton.addEventListener('click', async function () {
                const triggered = await triggerInstall();
                if (triggered) {
                    closeInstallModal();
                }
            });
        }
    }

    function initPushNotifications() {
        const enableButton = document.getElementById('enablePushBtn');
        const disableButton = document.getElementById('disablePushBtn');
        const status = document.getElementById('pushStatus');

        if (!enableButton || !status) {
            return;
        }

        const supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
        const vapidKey = document.querySelector('meta[name="webpush-public-key"]');

        function setStatus(message, classes) {
            status.textContent = message;
            status.className = 'notification-state ' + (classes || 'text-secondary');
        }

        function showEnabled() {
            setStatus('Aktif di perangkat ini', 'text-success');
            enableButton.classList.add('d-none');
            if (disableButton) {
                disableButton.classList.remove('d-none');
            }
        }

        function showDisabled(message) {
            setStatus(message || 'Belum aktif', 'text-secondary');
            enableButton.classList.remove('d-none');
            if (disableButton) {
                disableButton.classList.add('d-none');
            }
        }

        if (!supported) {
            setStatus('Browser ini belum mendukung notifikasi push', 'text-warning');
            enableButton.disabled = true;
            return;
        }

        if (!vapidKey || !vapidKey.content) {
            setStatus('Kunci VAPID belum dikonfigurasi', 'text-warning');
            enableButton.disabled = true;
            return;
        }

        if (Notification.permission === 'denied') {
            setStatus('Izin notifikasi diblokir di pengaturan browser', 'text-danger');
            enableButton.disabled = true;
        }

        navigator.serviceWorker.ready
            .then(function (registration) {
                return registration.pushManager.getSubscription();
            })
            .then(function (subscription) {
                if (subscription && Notification.permission === 'granted') {
                    showEnabled();
                    syncSubscription(subscription);
                    return;
                }

                if (Notification.permission !== 'denied') {
                    showDisabled();
                }
            })
            .catch(function () {
                // Abaikan kegagalan pengecekan awal.
            });

        enableButton.addEventListener('click', async function () {
            enableButton.disabled = true;
            setStatus('Meminta izin notifikasi...', 'text-secondary');

            try {
                const permission = Notification.permission === 'granted'
                    ? 'granted'
                    : await Notification.requestPermission();

                if (permission !== 'granted') {
                    setStatus('Izin notifikasi tidak diberikan', 'text-warning');
                    enableButton.disabled = false;
                    return;
                }

                const registration = await navigator.serviceWorker.ready;
                let subscription = await registration.pushManager.getSubscription();

                if (!subscription) {
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(vapidKey.content)
                    });
                }

                await syncSubscription(subscription, true);
                showEnabled();
            } catch (error) {
                setStatus('Gagal mengaktifkan notifikasi. Coba lagi.', 'text-danger');
                enableButton.disabled = false;
            }
        });

        if (disableButton) {
            disableButton.addEventListener('click', async function () {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    const subscription = await registration.pushManager.getSubscription();

                    if (subscription) {
                        await fetch('/push-subscriptions', {
                            method: 'DELETE',
                            headers: requestHeaders(),
                            body: JSON.stringify({ endpoint: subscription.endpoint })
                        });

                        await subscription.unsubscribe();
                    }

                    showDisabled('Dimatikan di perangkat ini');
                } catch (error) {
                    setStatus('Gagal mematikan notifikasi', 'text-danger');
                }
            });
        }

        async function syncSubscription(subscription, reportErrors) {
            const payload = Object.assign({}, subscription.toJSON(), {
                contentEncoding: 'aes128gcm'
            });

            const response = await fetch('/push-subscriptions', {
                method: 'POST',
                headers: requestHeaders(),
                body: JSON.stringify(payload)
            });

            if (!response.ok && reportErrors) {
                throw new Error('Subscription rejected');
            }
        }
    }

    function requestHeaders() {
        const token = document.querySelector('meta[name="csrf-token"]');

        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token ? token.content : ''
        };
    }

    function hideInstallBanner() {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) {
            banner.classList.add('d-none');
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let index = 0; index < rawData.length; index += 1) {
            outputArray[index] = rawData.charCodeAt(index);
        }

        return outputArray;
    }
})();
