import '../styles/home.css';

const scrollNavbar = document.querySelector("#header-scroll-container");
const sticky = scrollNavbar.offsetTop;

window.addEventListener('scroll', function () {
    const scrolled = window.scrollY;
    const bgContainer = document.querySelector('.background-container');
    if (bgContainer) {
        bgContainer.style.transform = 'translateY(' + (scrolled * 0.3) + 'px)';
    }
    stickyNavbar();
});

function stickyNavbar() {
    if (window.scrollY > 50) {
        scrollNavbar.style.backgroundColor = "rgba(8, 11, 17, 0.85)";
        scrollNavbar.style.backdropFilter = "blur(12px)";
        scrollNavbar.style.borderBottom = "1px solid rgba(255, 255, 255, 0.08)";
        scrollNavbar.style.boxShadow = "0 10px 30px rgba(0, 0, 0, 0.3)";
    } else {
        scrollNavbar.style.backgroundColor = "";
        scrollNavbar.style.backdropFilter = "";
        scrollNavbar.style.borderBottom = "";
        scrollNavbar.style.boxShadow = "";
    }
}

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
                block.classList.remove('opacity-0', 'translate-y-10');
                if (headline) headline.classList.remove('translate-x-[-100%]');
                if (text) text.classList.remove('translate-x-20');
            } else {
                block.classList.add('opacity-0', 'translate-y-10');
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

