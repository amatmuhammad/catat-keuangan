(function () {
    'use strict';

    const marker = document.querySelector('[data-live-summary]');

    if (!marker) {
        return;
    }

    const endpoint = marker.dataset.liveUrl;
    const accountId = String(marker.dataset.accountId || '');
    const statusElements = Array.from(document.querySelectorAll('[data-live-status]'));
    const amountFormatter = new Intl.NumberFormat('id-ID');
    const intervalMs = 10000;

    let inFlight = false;
    let reloadScheduled = false;

    function setStatus(message, tone) {
        const tones = {
            success: 'bg-success-subtle text-success-emphasis',
            warning: 'bg-warning-subtle text-warning-emphasis',
            muted: 'bg-secondary-subtle text-secondary-emphasis'
        };

        statusElements.forEach(function (element) {
            element.textContent = message;
            element.className = 'badge ' + (tones[tone] || tones.muted);
        });
    }

    function reloadPage(message) {
        if (reloadScheduled) {
            return;
        }

        reloadScheduled = true;
        setStatus(message, 'warning');
        window.setTimeout(function () {
            window.location.reload();
        }, 800);
    }

    function applySummary(summary) {
        if (accountId && String(summary.account_id) !== accountId) {
            reloadPage('Akun berubah, memuat ulang...');
            return;
        }

        document.querySelectorAll('[data-live-field]').forEach(function (element) {
            const field = element.dataset.liveField;

            if (field === 'shared_balance' || field === 'personal_balance') {
                const formatted = amountFormatter.format(Number(summary[field] || 0));
                element.textContent = (element.dataset.livePrefix || '') + formatted;

                if (element.dataset.liveTitle !== 'false') {
                    element.title = 'Rp ' + formatted;
                }

                return;
            }

            if (Object.prototype.hasOwnProperty.call(summary, field)) {
                element.textContent = String(summary[field]);
            }
        });

        const localWalletIds = Array.from(document.querySelectorAll('[data-live-wallet-balance]'))
            .map(function (element) {
                return String(element.dataset.liveWalletBalance);
            })
            .sort();

        const serverWalletIds = summary.wallets
            .map(function (wallet) {
                return String(wallet.id);
            })
            .sort();

        if (localWalletIds.join('|') !== serverWalletIds.join('|')) {
            reloadPage('Daftar dompet berubah, memuat ulang...');
            return;
        }

        summary.wallets.forEach(function (wallet) {
            document.querySelectorAll('[data-live-wallet-balance="' + wallet.id + '"]').forEach(function (element) {
                element.textContent = 'Rp ' + amountFormatter.format(Number(wallet.balance || 0));
                element.title = element.textContent;
            });

            document.querySelectorAll('[data-live-wallet-count="' + wallet.id + '"]').forEach(function (element) {
                element.textContent = String(wallet.transactions_count);
            });
        });

        setStatus('Tersinkron ' + new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }), 'success');
    }

    async function sync() {
        if (inFlight || document.hidden) {
            return;
        }

        inFlight = true;

        try {
            const response = await fetch(endpoint, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('Live summary request failed');
            }

            applySummary(await response.json());
        } catch (error) {
            setStatus('Gagal sinkron, mencoba lagi...', 'warning');
        } finally {
            inFlight = false;
        }
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            sync();
        }
    });

    window.setInterval(sync, intervalMs);
    sync();
})();
