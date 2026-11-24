// Schriften lokal ausliefern statt von Google (Datenschutz, Ladezeit)
import '@fontsource-variable/lexend/wght.css';
import '@fontsource-variable/newsreader/opsz.css';
import '../styles/app.css';

// Mobiles Menü
const burger = document.getElementById('burger-btn');
const mobileMenu = document.getElementById('mobile-menu');

const setMenu = open => {
    if (!burger || !mobileMenu) return;
    burger.setAttribute('aria-expanded', String(open));
    mobileMenu.hidden = !open;
    document.body.style.overflow = open ? 'hidden' : '';
};

if (burger && mobileMenu) {
    burger.addEventListener('click', () => setMenu(mobileMenu.hidden));
    mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenu(false)));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') setMenu(false);
    });
}

// Kopfzeile bekommt beim Scrollen eine Trennlinie
const header = document.getElementById('header-scroll-container');
if (header) {
    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', update, { passive: true });
    update();
}

// Zurück-Knopf auf Unterseiten: kommt man von dieser Website, geht es im Verlauf zurück.
// Der Browser stellt dabei die alte Scrollposition wieder her. Sonst greift der Link
// zum passenden Abschnitt der Startseite.
document.querySelectorAll('[data-back]').forEach(link => {
    link.addEventListener('click', event => {
        let sameSite = false;
        try {
            sameSite = document.referrer !== '' && new URL(document.referrer).origin === window.location.origin;
        } catch (e) { /* ungültiger Referrer */ }

        if (sameSite && window.history.length > 1) {
            event.preventDefault();
            window.history.back();
        }
    });
});
