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
    const blocks = document.querySelectorAll('.block-container');
    blocks.forEach(block => {
        const line = block.querySelector('.block-line');
        const blockHeight = block.offsetHeight;
        line.style.height = `${blockHeight}px`;
    });
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const block = entry.target;
            if (entry.isIntersecting) {
                // Entferne die translate Klassen, um die Animation auszuführen
                block.classList.remove('opacity-0', 'translate-y-10');
                block.querySelector('.headline').classList.remove('translate-x-[-100%]');
                block.querySelector('.text').classList.remove('translate-x-20');
            } else {
                block.classList.add('opacity-0', 'translate-y-10');
                block.querySelector('.headline').classList.add('translate-x-[-100%]');
                block.querySelector('.text').classList.add('translate-x-20');
            }
        });
    }, { threshold: 0.1 }); // Sobald 20% des Blocks sichtbar sind

    blocks.forEach(block => observer.observe(block));
});

