<?php
$images = [];
for ($i = 1; $i <= 10; $i++) {
    $images[] = "./images/{$i}.jpeg";
}
?>

<style>
    .custom-carousel {
        width: 100vw;
        position: relative;
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

<div id="home" class="custom-carousel">
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
            opacity: 0.6;
            z-index: 1;
          "></div>

    <div class="hero-content" style="position: absolute; z-index: 2; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <h1 style="color: white; font-size: 4.5rem; text-transform: capitalize; max-width: 950px; width: 100%; text-align: center;">One contribution can empower countless dreams</h1>
        <p style="color: white; font-size: 4ch; padding-top: 40px; text-align: center;">
            HELP EDUCATE - DONATE NOW
        </p>
    </div>

    <div class="carousel-container">
        <?php foreach ($images as $index => $image): ?>
            <div class="carousel-slide">
                <img src="<?php echo $image; ?>" alt="Slide <?php echo $index + 1; ?>" style="object-fit: center;" />
            </div>
        <?php endforeach; ?>
    </div>

    <div class="carousel-indicators">
        <?php foreach ($images as $index => $image): ?>
            <button class="carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></button>
        <?php endforeach; ?>
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