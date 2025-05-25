<?php
$images = [
    './images/bgimage.jpg',
    './images/bgimage.jpg',
    './images/bgimage.jpg',
];
?>

<style>
    .custom-carousel {
        width: 100vw;
        position: relative;
        max-width: 1500px;
        margin: 0 auto;
        height: 100vh;
        overflow: hidden;
    }

    .carousel-container {
        display: flex;
        transition: transform 0.5s ease-in-out;
        height: 100%;
    }

    .carousel-slide {
        min-width: 100%;
        height: 100%;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 1rem;
        cursor: pointer;
        border: none;
        font-size: 1.5rem;
        transition: background-color 0.3s;
    }

    .carousel-button:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    .carousel-prev {
        left: 10px;
    }

    .carousel-next {
        right: 10px;
    }

    .carousel-indicators {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
    }

    .carousel-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .carousel-indicator.active {
        background: white;
    }
</style>

<div class="custom-carousel">
    <div
        class="hero-overlay"
        style="
            background-color: black;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            z-index: 1;
          "></div>

    <div class="hero-content" style="position: absolute; z-index: 2; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <h1 style="color: white">Connecting Dreams to Reality</h1>
        <p style="color: white">
            Join our platform to connect students with donors and mentors,
            making quality education accessible to all.
        </p>
    </div>

    <div class="carousel-container">
        <?php foreach ($images as $index => $image): ?>
            <div class="carousel-slide">
                <img src="<?php echo $image; ?>" alt="Slide <?php echo $index + 1; ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <div class="carousel-indicators">
        <?php for ($i = 0; $i < count($images); $i++): ?>
            <div class="carousel-indicator <?php echo $i === 0 ? 'active' : ''; ?>"
                onclick="goToSlide(<?php echo $i; ?>)"></div>
        <?php endfor; ?>
    </div>
</div>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const container = document.querySelector('.carousel-container');

    function updateCarousel() {
        container.style.transform = `translateX(-${currentSlide * 100}%)`;
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateCarousel();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateCarousel();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateCarousel();
    }

    // Auto-advance slides every 5 seconds
    setInterval(nextSlide, 5000);
</script>