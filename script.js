
document.addEventListener("DOMContentLoaded", function () {
    const banner = document.querySelector(".banner");
    let index = 0;
    const totalSlides = document.querySelectorAll(".banner img").length;

    function slideImages() {
        index = (index + 1) % totalSlides-1; // Loop back after the last image
        banner.style.transform = `translateX(-${index * 60}%)`;
    }

    // Auto slide every 10 seconds
    setInterval(slideImages, 10000);
});
