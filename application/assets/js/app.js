/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.css';

const hamburgerBtn = document.getElementById('burger-btn');
const mobileMenu = document.getElementById('mobile-menu');
const navigationLinks = document.querySelectorAll('.navigation-link');

if (hamburgerBtn !== null) {
// Öffne/Schließe das Menü
    hamburgerBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('opacity-0');
        mobileMenu.classList.toggle('opacity-100');
    });
}

if (navigationLinks !== null) {
// Menü schließen bei Klick auf Links
    navigationLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            mobileMenu.classList.remove('opacity-100');
            mobileMenu.classList.add('opacity-0');
        });
    });
}

// Zeiterfassung: laufende Timer sekundengenau anzeigen
const formatDuration = (totalSeconds) => {
    const total = Math.max(0, totalSeconds);
    const hours = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const seconds = total % 60;

    return [hours, minutes, seconds]
        .map((value) => String(value).padStart(2, '0'))
        .join(':');
};

document.querySelectorAll('[data-timer]').forEach((element) => {
    let elapsed = parseInt(element.dataset.seconds, 10) || 0;
    element.textContent = formatDuration(elapsed);

    if (element.dataset.running === '1') {
        setInterval(() => {
            elapsed += 1;
            element.textContent = formatDuration(elapsed);
        }, 1000);
    }
});
