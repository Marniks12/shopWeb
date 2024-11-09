let slideIndex = 0;
showSlide(slideIndex);

// Functie om een specifieke slide te tonen
function showSlide(index) {
    const slides = document.querySelectorAll(".promo-slide");
    slides.forEach((slide, i) => {
        slide.style.display = i === index ? "block" : "none";
    });
}

// Functie om naar de volgende/vorige slide te gaan
function changeSlide(n) {
    const slides = document.querySelectorAll(".promo-slide");
    slideIndex = (slideIndex + n + slides.length) % slides.length;
    showSlide(slideIndex);
}

// Automatisch door de slides heen gaan
setInterval(() => {
    changeSlide(1); // Ga elke 3 seconden naar de volgende slide
}, 3000);
