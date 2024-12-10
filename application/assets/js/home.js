import '../styles/home.css';

const scrollNavbar = document.querySelector("#header-scroll-container");
const sticky = scrollNavbar.offsetTop; // Position der Navigation speichern

window.addEventListener('scroll', function () {
    const scrolled = window.scrollY;
    document.querySelector('.background-container').style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
    stickyNavbar();
});

function stickyNavbar() {
    // Wenn die Seite so weit gescrollt ist, dass die Navigation den oberen Rand überschreitet
    if (window.scrollY > sticky) {
        scrollNavbar.style.backgroundColor = "#2d2d28ff"; // Füge die Klasse 'sticky' hinzu
    } else {
        scrollNavbar.style.backgroundColor = ""; // Entferne die Klasse 'sticky'
    }
}

document.addEventListener("DOMContentLoaded", function () {
    console.log('Hier bin ich');
    const burgerBtn = document.getElementById('burger-btn');
    const navMenu = document.getElementById('nav-menu');

    burgerBtn.addEventListener('click', function () {
        console.log('Burger Button geklickt!');
        navMenu.classList.toggle('hidden');
    });

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
