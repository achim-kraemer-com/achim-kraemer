import '../styles/home.css';

// ------------------------------------------------------------------
// Rückkehr von Unterseiten: genau dort landen, wo man losgegangen ist.
// Beim Verlassen merken wir uns die Scrollposition; kommt man per Zurück
// (Browser oder Zurück-Knopf der Unterseite) wieder, wird sie gesetzt.
// ------------------------------------------------------------------
const SCROLL_KEY = 'home-scroll:' + window.location.pathname;
const navEntry = performance.getEntriesByType('navigation')[0];
const cameBack = navEntry && navEntry.type === 'back_forward';

window.addEventListener('pagehide', () => {
    sessionStorage.setItem(SCROLL_KEY, String(window.scrollY));
});

if (cameBack && sessionStorage.getItem(SCROLL_KEY) !== null) {
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    const y = parseInt(sessionStorage.getItem(SCROLL_KEY), 10) || 0;
    const restore = () => window.scrollTo({ top: y, behavior: 'instant' });
    restore();
    window.addEventListener('load', restore);
} else if (window.location.hash) {
    // Mit #anker von einer Unterseite: nach dem Laden (Bilder, Schriften) neu ausrichten
    window.addEventListener('load', () => {
        const target = document.getElementById(window.location.hash.slice(1));
        if (target) target.scrollIntoView({ behavior: 'instant' });
    });
}

// ------------------------------------------------------------------
// Abschnittsköpfe einmalig seitlich einblenden. Bei der Rückkehr nicht,
// damit nichts unsichtbar an der wiederhergestellten Position steht.
// ------------------------------------------------------------------
const reveals = document.querySelectorAll('.reveal');
if (!cameBack && 'IntersectionObserver' in window) {
    document.documentElement.classList.add('reveal-ready');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -10% 0px' });
    reveals.forEach(el => observer.observe(el));
}

// ------------------------------------------------------------------
// Aktuellen Bereich in der Navigation markieren
// ------------------------------------------------------------------
const navLinks = document.querySelectorAll('.main-nav [data-section]');
if (navLinks.length && 'IntersectionObserver' in window) {
    // Alle Abschnitte beobachten, damit die Markierung bei FAQ und Kontakt wieder verschwindet
    const sections = document.querySelectorAll('main > section[id]');
    const sectionObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            navLinks.forEach(link => link.classList.toggle('is-current', link.dataset.section === entry.target.id));
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(section => sectionObserver.observe(section));
}

// Kontaktformular per AJAX senden und mit Danke-Modal bestätigen
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contact-form');
    const dialog = document.getElementById('contact-thanks');
    if (!form || !dialog) return;

    const status = document.getElementById('contact-form-status');
    const button = form.querySelector('button[type="submit"]');
    const buttonLabel = button.textContent;
    const captchaInput = form.querySelector('input[name$="[captcha]"]');

    const showStatus = text => {
        status.textContent = text;
        status.hidden = !text;
    };

    const clearErrors = () => {
        showStatus('');
        form.querySelectorAll('.contact-field-error').forEach(el => el.remove());
        form.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
    };

    const showErrors = (message, errors) => {
        let general = message;
        Object.entries(errors || {}).forEach(([field, messages]) => {
            const input = form.querySelector(`[name$="[${field}]"]`);
            if (!input || input.type === 'hidden') {
                general = messages.join(' ');
                return;
            }
            input.setAttribute('aria-invalid', 'true');
            const error = document.createElement('p');
            error.className = 'contact-field-error';
            error.textContent = messages.join(' ');
            input.insertAdjacentElement('afterend', error);
        });
        showStatus(general);
    };

    // reCAPTCHA erst laden, sobald jemand das Formular benutzt
    let captchaLoading = null;
    const loadCaptcha = () => {
        const src = captchaInput && captchaInput.dataset.recaptchaSrc;
        if (!src) return Promise.resolve();
        if (!captchaLoading) {
            captchaLoading = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
        return captchaLoading;
    };
    form.addEventListener('focusin', () => loadCaptcha().catch(() => {}), { once: true });

    // reCAPTCHA-Token ist nur einmal gültig, daher vor jedem Versand neu holen
    const refreshCaptcha = async () => {
        await loadCaptcha();
        if (!captchaInput || typeof grecaptcha === 'undefined') return;
        await new Promise(resolve => grecaptcha.ready(resolve));
        captchaInput.value = await grecaptcha.execute(form.dataset.recaptchaKey, { action: form.dataset.recaptchaAction });
    };

    const setSending = sending => {
        button.disabled = sending;
        button.textContent = sending ? form.dataset.sendingLabel : buttonLabel;
    };

    form.addEventListener('submit', async event => {
        event.preventDefault();
        clearErrors();
        setSending(true);

        try {
            await refreshCaptcha();
            const response = await fetch(form.action || window.location.href, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            const result = await response.json();

            if (result.success) {
                form.reset();
                dialog.querySelector('.contact-thanks-title').textContent = result.title;
                dialog.showModal();
            } else {
                showErrors(result.message, result.errors);
            }
        } catch (error) {
            showStatus(form.dataset.failedLabel);
        } finally {
            setSending(false);
        }
    });

    // Klick auf den abgedunkelten Hintergrund schließt das Modal
    dialog.addEventListener('click', event => {
        if (event.target === dialog) dialog.close();
    });
});
