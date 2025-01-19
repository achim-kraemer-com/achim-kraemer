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

document.addEventListener("DOMContentLoaded", async function () {
    const response = await fetch("/api/keywords");
    const keywords = await response.json();

    const container = document.getElementById("keywords-container");
    const positions = [];
    let addNumber = 0;
    const inPixels = window.innerHeight * 0.5;
    const topInPixels = inPixels - 100;
    const bottomInPixels = inPixels + 100;
    const windowInnerWidth = window.innerWidth - 200;
    const windowInnerHeight = window.innerHeight - 100;

    function getRandomPosition() {
        let x, y;
        do {
            x = Math.random() * windowInnerWidth;
            y = Math.random() * windowInnerHeight;
            if (y < 70) {
                y += 70;
            }
            if (y < bottomInPixels && y > topInPixels && x < 1100) {
                addNumber = Math.random() * 100;
                y = bottomInPixels + addNumber;
                if (y < inPixels) {
                    y = topInPixels - addNumber;
                }
            }
        } while (positions.some(pos => Math.abs(pos.x - x) < 250 && Math.abs(pos.y - y) < 80));

        positions.push({ x, y });
        return { x, y };
    }

    keywords.forEach((keyword, index) => {
        setTimeout(() => {
            const { x, y } = getRandomPosition();
            const span = document.createElement("span");
            span.className = "keyword";
            span.innerText = keyword;
            span.style.left = `${x}px`;
            span.style.top = `${y}px`;
            container.appendChild(span);

            setTimeout(() => {
                span.style.opacity = 1;
            }, 100);
        }, index * 1000);
    });
});

