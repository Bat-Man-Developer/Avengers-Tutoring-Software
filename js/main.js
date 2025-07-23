// Navbar Function
const menuBtn = document.querySelector('.menu-btn');
const navLinks = document.querySelector('.nav-links');

menuBtn.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Navbar scroll effect
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
        navbar.style.top = "0";
        return;
    }

    if (currentScroll > lastScroll && !navLinks.classList.contains('active')) {
        navbar.style.top = "-80px";
    } else {
        navbar.style.top = "0";
    }

    lastScroll = currentScroll;
});