import '../styles/home.css';

const scrollNavbar = document.querySelector("#header-scroll-container");
const sticky = scrollNavbar.offsetTop;

// Anker-Sprünge sollen exakt unter dem fixen Header enden (Höhe variiert je nach Breite)
const setHeaderHeight = () => {
    document.documentElement.style.setProperty('--header-height', scrollNavbar.offsetHeight + 'px');
};
setHeaderHeight();
window.addEventListener('resize', setHeaderHeight);

// Kommt man mit #anker von einer Unterseite, springt der Browser vor dem fertigen Layout
// (Bilder, Schriften, Linienhöhen) und landet daneben. Nach dem Laden neu ausrichten.
window.addEventListener('load', () => {
    const target = location.hash && document.getElementById(location.hash.slice(1));
    if (target) target.scrollIntoView({ behavior: 'instant' });
});

window.addEventListener('scroll', function () {
    const scrolled = window.scrollY;
    const bgContainer = document.querySelector('.background-container');
    if (bgContainer) {
        bgContainer.style.transform = 'translateY(' + (scrolled * 0.3) + 'px)';
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const blocks = document.querySelectorAll('.block-container');
    blocks.forEach(block => {
        const line = block.querySelector('.block-line');
        if (line) {
            const blockHeight = block.offsetHeight;
            line.style.height = `${blockHeight}px`;
        }
    });
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const block = entry.target;
            const headline = block.querySelector('.headline');
            const text = block.querySelector('.text');
            
            if (entry.isIntersecting) {
                block.classList.remove('opacity-0');
                if (headline) headline.classList.remove('translate-x-[-100%]');
                if (text) text.classList.remove('translate-x-20');
            } else {
                block.classList.add('opacity-0');
                if (headline) headline.classList.add('translate-x-[-100%]');
                if (text) text.classList.add('translate-x-20');
            }
        });
    }, { threshold: 0.05 });

    blocks.forEach(block => observer.observe(block));
});

// document.addEventListener("DOMContentLoaded", async function () {
//     const response = await fetch("/api/keywords");
//     const keywords = await response.json();
//
//     const container = document.getElementById("keywords-container");
//     const positions = [];
//     let addNumber = 0;
//     const inPixels = window.innerHeight * 0.5;
//     const topInPixels = inPixels - 100;
//     const bottomInPixels = inPixels + 100;
//     const windowInnerWidth = window.innerWidth - 200;
//     const windowInnerHeight = window.innerHeight - 100;
//
//     function getRandomPosition() {
//         let x, y;
//         do {
//             x = Math.random() * windowInnerWidth;
//             y = Math.random() * windowInnerHeight;
//             if (y < 70) {
//                 y += 70;
//             }
//             if (y < bottomInPixels && y > topInPixels && x < 1100) {
//                 addNumber = Math.random() * 100;
//                 y = bottomInPixels + addNumber;
//                 if (y < inPixels) {
//                     y = topInPixels - addNumber;
//                 }
//             }
//         } while (positions.some(pos => Math.abs(pos.x - x) < 250 && Math.abs(pos.y - y) < 80));
//
//         positions.push({ x, y });
//         return { x, y };
//     }
//
//     keywords.forEach((keyword, index) => {
//         setTimeout(() => {
//             const { x, y } = getRandomPosition();
//             const span = document.createElement("span");
//             span.className = "keyword";
//             span.innerText = keyword;
//             span.style.left = `${x}px`;
//             span.style.top = `${y}px`;
//             container.appendChild(span);
//
//             setTimeout(() => {
//                 span.style.opacity = 1;
//             }, 100);
//         }, index * 1000);
//     });
// });


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.testimonial-slider').forEach(slider => {
        const slides = slider.querySelectorAll('.testimonial-slide');
        const dots = slider.querySelectorAll('.testimonial-dot');
        let current = 0;
        let timer = null;

        const show = index => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));
            dots.forEach((dot, i) => dot.classList.toggle('is-active', i === current));
        };
        const stop = () => { clearInterval(timer); timer = null; };
        const start = () => {
            if (!timer) timer = setInterval(() => show(current + 1), 8000);
        };

        dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); stop(); start(); }));
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        slider.addEventListener('focusin', stop);
        slider.addEventListener('focusout', start);
        start();
    });
});

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

    // reCAPTCHA-Token ist nur einmal gültig, daher vor jedem Versand neu holen
    const refreshCaptcha = async () => {
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
