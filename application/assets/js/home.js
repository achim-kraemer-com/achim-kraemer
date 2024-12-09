import '../styles/home.css';

const navbar = document.querySelector("#header-container");
const scrollNavbar = document.querySelector("#header-scroll-container");
const sticky = navbar.offsetTop; // Position der Navigation speichern

window.addEventListener('scroll', function() {
    const scrolled = window.scrollY;
    document.querySelector('.background-container').style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
    stickyNavbar();
});

function stickyNavbar() {
    // Wenn die Seite so weit gescrollt ist, dass die Navigation den oberen Rand überschreitet
    if (window.scrollY > sticky) {
        navbar.style.display = "none"; // Füge die Klasse 'sticky' hinzu
        scrollNavbar.style.display = "block"; // Füge die Klasse 'sticky' hinzu
    } else {
        navbar.style.display = "block"; // Entferne die Klasse 'sticky'
        scrollNavbar.style.display = "none"; // Entferne die Klasse 'sticky'
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll(
        ".animate-fade-in-left, .animate-fade-in-right, .animate-vertical-line"
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("opacity-100");
                }
            });
        },
        {
            threshold: 0.1,
        }
    );

    elements.forEach((element) => {
        element.classList.add("opacity-0"); // Startzustand: unsichtbar
        observer.observe(element);
    });
});

