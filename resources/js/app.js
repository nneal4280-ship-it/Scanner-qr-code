import './bootstrap';

import Alpine from 'alpinejs';
import QrScanner from 'qr-scanner';
import QrScannerWorkerPath from 'qr-scanner/qr-scanner-worker.min.js?url';
import QRCode from 'qrcode';

window.Alpine = Alpine;
QrScanner.WORKER_PATH = QrScannerWorkerPath;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const body = document.querySelector('.pa-body');
    const setTheme = (theme) => {
        const dark = theme === 'dark';
        body?.classList.toggle('pa-dark', dark);
        document.documentElement.classList.toggle('pa-dark', dark);
        localStorage.setItem('pointage-theme', dark ? 'dark' : 'light');
        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.setAttribute('aria-pressed', String(dark));
            button.setAttribute('aria-label', dark ? 'Activer le mode clair' : 'Activer le mode sombre');
        });
        document.querySelectorAll('[data-theme-dark]').forEach((button) => button.setAttribute('aria-pressed', String(dark)));
        document.querySelectorAll('[data-theme-light]').forEach((button) => button.setAttribute('aria-pressed', String(!dark)));
    };
    setTheme(localStorage.getItem('pointage-theme') === 'dark' ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => button.addEventListener('click', () => setTheme(body?.classList.contains('pa-dark') ? 'light' : 'dark')));
    document.querySelectorAll('[data-theme-dark]').forEach((button) => button.addEventListener('click', () => setTheme('dark')));
    document.querySelectorAll('[data-theme-light]').forEach((button) => button.addEventListener('click', () => setTheme('light')));

    document.querySelectorAll('[data-toast]').forEach((button) => button.addEventListener('click', () => showToast(button.dataset.toast)));
    document.querySelectorAll('input[type="password"]').forEach((input) => {
        if (input.closest('.pa-password-field')) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'pa-password-field';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'pa-password-toggle';
        toggle.textContent = 'Afficher';
        toggle.setAttribute('aria-label', 'Afficher le mot de passe');
        toggle.setAttribute('aria-pressed', 'false');
        toggle.addEventListener('click', () => {
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            toggle.textContent = visible ? 'Afficher' : 'Masquer';
            toggle.setAttribute('aria-label', (visible ? 'Afficher' : 'Masquer') + ' le mot de passe');
            toggle.setAttribute('aria-pressed', String(!visible));
        });
        wrapper.appendChild(toggle);
    });

    const loginForm = document.querySelector('form[action$="/login"]');
    const authCard = loginForm?.closest('.pa-auth-card');
    if (loginForm && authCard && !authCard.querySelector('[data-register-link]')) {
        const registerLink = document.createElement('a');
        registerLink.href = loginForm.action.replace(/\/login\/?$/, '/register');
        registerLink.dataset.registerLink = 'true';
        registerLink.className = 'pa-auth-switch';
        registerLink.textContent = 'Créer un compte';
        authCard.appendChild(registerLink);
    }

    const qrCanvas = document.querySelector('[data-qr-token]');
    if (qrCanvas) {
        QRCode.toCanvas(qrCanvas, qrCanvas.dataset.qrToken, { width: 280, margin: 2, errorCorrectionLevel: 'M' }).catch(() => {});
        document.querySelector('[data-qr-download]')?.addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'qr-presence-' + qrCanvas.dataset.qrDate.split('/').reverse().join('-') + '.png';
            link.href = qrCanvas.toDataURL('image/png');
            link.click();
        });
        document.querySelector('[data-qr-print]')?.addEventListener('click', () => window.print());
        document.querySelector('[data-qr-export-form]')?.addEventListener('submit', (event) => {
            event.currentTarget.querySelector('[data-qr-image]').value = qrCanvas.toDataURL('image/png');
        });
    }

    document.querySelectorAll('[data-list-search]').forEach((input) => input.addEventListener('input', () => {
        document.querySelectorAll(`${input.dataset.listSearch} [data-search-item]`).forEach((item) => {
            item.hidden = !item.textContent.toLowerCase().includes(input.value.toLowerCase());
        });
    }));

    const form = document.querySelector('[data-attendance-form]');
    const qrInput = document.querySelector('#qr_token_input');
    const qrHidden = document.querySelector('input[name="qr_token"]');
    const qrState = document.querySelector('#qr-state');
    if (qrInput && qrHidden) qrInput.addEventListener('input', () => { qrHidden.value = qrInput.value.trim(); qrState.textContent = qrHidden.value ? 'Scanné' : '—'; });

    if (form) {
        const typeInput = form.querySelector('input[name="type"]');
        const typeSelector = document.createElement('div');
        typeSelector.className = 'pa-attendance-types';
        typeSelector.setAttribute('role', 'group');
        typeSelector.setAttribute('aria-label', 'Type de pointage');
        [
            ['arrival', 'Entrée'],
            ['departure', 'Sortie'],
        ].forEach(([value, label]) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = value === 'arrival' ? 'is-active' : '';
            button.textContent = label;
            button.addEventListener('click', () => {
                if (typeInput) typeInput.value = value;
                typeSelector.querySelectorAll('button').forEach((item) => item.classList.remove('is-active'));
                button.classList.add('is-active');
            });
            typeSelector.appendChild(button);
        });
        form.insertBefore(typeSelector, form.firstElementChild);
    }

    document.querySelector('[data-locate]')?.addEventListener('click', () => {
        const state = document.querySelector('#location-state');
        if (!navigator.geolocation) { if (state) state.textContent = 'Indisponible'; return; }
        if (state) state.textContent = 'Recherche…';
        navigator.geolocation.getCurrentPosition((position) => {
            form?.querySelector('[name="latitude"]').setAttribute('value', position.coords.latitude);
            form?.querySelector('[name="longitude"]').setAttribute('value', position.coords.longitude);
            if (state) state.textContent = 'Position vérifiée';
        }, () => { if (state) state.textContent = 'Autorisation refusée'; }, { enableHighAccuracy: true, timeout: 7000, maximumAge: 30000 });
    });

    if (form) {
        const video = document.querySelector('#qr-video');
        const status = document.querySelector('#scan-status');
        const setScannedValue = (value) => {
            const token = value.trim();
            if (!token) return false;
            qrHidden.value = token;
            if (qrInput) qrInput.value = token;
            if (qrState) qrState.textContent = 'Scanné';
            if (status) status.textContent = 'QR code reconnu. Vous pouvez valider le pointage.';
            return true;
        };

        const scanner = new QrScanner(
            video,
            (result) => {
                const value = typeof result === 'string' ? result : result.data;
                if (setScannedValue(value)) scanner.stop();
            },
            {
                preferredCamera: 'environment',
                highlightScanRegion: true,
                highlightCodeOutline: true,
                returnDetailedScanResult: true,
            },
        );
        scanner.start().then(() => {
            if (status) status.textContent = 'Caméra active : cadrez le QR code du centre.';
        }).catch((error) => {
            if (status) {
                status.textContent = error?.name === 'NotAllowedError'
                    ? 'Autorisez l’accès à la caméra dans votre navigateur.'
                    : 'Caméra indisponible. Utilisez HTTPS ou localhost, puis réessayez.';
            }
        });
        return;

        if (!('BarcodeDetector' in window)) {
            if (status) status.textContent = 'Scanner non pris en charge. Utilisez Chrome récent ou saisissez le code.';
        } else if (!navigator.mediaDevices?.getUserMedia) {
            if (status) status.textContent = 'Caméra indisponible. Ouvrez le site via HTTPS ou localhost.';
        } else {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false }).then((stream) => {
                video.srcObject = stream;
                return video.play().then(() => {
                    if (status) status.textContent = 'Caméra active : cadrez le QR code du centre.';
                    const detector = new BarcodeDetector({ formats: ['qr_code'] });
                    const scan = async () => {
                        if (qrHidden.value) {
                            stream.getTracks().forEach((track) => track.stop());
                            return;
                        }
                        try {
                            const codes = await detector.detect(video);
                            if (codes[0]?.rawValue) setScannedValue(codes[0].rawValue);
                        } catch (_) {
                            // La caméra peut ne pas avoir encore fourni une image exploitable.
                        }
                        requestAnimationFrame(scan);
                    };
                    scan();
                });
            }).catch((error) => {
                if (status) {
                    status.textContent = error.name === 'NotAllowedError'
                        ? 'Autorisez l’accès à la caméra dans votre navigateur.'
                        : 'Caméra indisponible. Utilisez HTTPS ou localhost, puis réessayez.';
                }
            });
        }
    }
});

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'pa-toast'; toast.textContent = message; document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('is-visible'));
    setTimeout(() => { toast.classList.remove('is-visible'); setTimeout(() => toast.remove(), 200); }, 2600);
}
