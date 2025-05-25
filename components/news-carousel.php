<?php
$news_items = [
    [
        'title' => 'New Scholarship Program Launched',
        'description' => 'We are excited to announce our new scholarship program for deserving students.',
        'date' => 'March 15, 2024',
        'image' => './images/bgimage.jpg'
    ],
    [
        'title' => 'Mentorship Success Stories',
        'description' => 'Read about how our mentorship program has helped students achieve their goals.',
        'date' => 'March 10, 2024',
        'image' => './images/bgimage.jpg'
    ],
    [
        'title' => 'Community Impact Report',
        'description' => 'See how our community has made a difference in education this year.',
        'date' => 'March 5, 2024',
        'image' => './images/bgimage.jpg'
    ],
    [
        'title' => 'Community Impact Report',
        'description' => 'See how our community has made a difference in education this year.',
        'date' => 'March 5, 2024',
        'image' => './images/bgimage.jpg'
    ],
    [
        'title' => 'Community Impact Report',
        'description' => 'See how our community has made a difference in education this year.',
        'date' => 'March 5, 2024',
        'image' => './images/bgimage.jpg'
    ]
];
?>

<style>
    .news-section {
        padding: 4rem 0;
        background-color: #f8f9fa;
    }

    .news-section h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #333;
    }

    .news-carousel {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        overflow: hidden;
    }

    .news-container {
        display: flex;
        transition: transform 0.5s ease-in-out;
        gap: 20px;
        padding: 20px;
    }

    .news-card {
        min-width: calc(33.333% - 20px);
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .news-content {
        padding: 20px;
    }

    .news-date {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .news-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .news-description {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .news-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(90, 74, 227, 0.8);
        color: white;
        padding: 1rem;
        cursor: pointer;
        border: none;
        font-size: 1.5rem;
        transition: background-color 0.3s;
        z-index: 2;
    }

    .news-button:hover {
        background: rgba(90, 74, 227, 1);
    }

    .news-prev {
        left: 10px;
    }

    .news-next {
        right: 10px;
    }

    @media (max-width: 768px) {
        .news-card {
            min-width: calc(50% - 20px);
        }
    }

    @media (max-width: 480px) {
        .news-card {
            min-width: calc(100% - 20px);
        }
    }
</style>

<section class="news-section">
    <h2>Latest News</h2>
    <div class="news-carousel">
        <div class="news-container">
            <?php foreach ($news_items as $news): ?>
                <div class="news-card">
                    <img src="<?php echo $news['image']; ?>" alt="<?php echo $news['title']; ?>" class="news-image">
                    <div class="news-content">
                        <div class="news-date"><?php echo $news['date']; ?></div>
                        <h3 class="news-title"><?php echo $news['title']; ?></h3>
                        <p class="news-description"><?php echo $news['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="news-button news-prev" onclick="prevNews()">❮</button>
        <button class="news-button news-next" onclick="nextNews()">❯</button>
    </div>
</section>

<script>
    let currentNewsIndex = 0;
    const newsCards = document.querySelectorAll('.news-card');
    const newsContainer = document.querySelector('.news-container');
    const cardsPerView = window.innerWidth <= 480 ? 1 : window.innerWidth <= 768 ? 2 : 3;

    function updateNewsCarousel() {
        const offset = currentNewsIndex * (100 / cardsPerView);
        newsContainer.style.transform = `translateX(-${offset}%)`;
    }

    function nextNews() {
        const maxIndex = newsCards.length - cardsPerView;
        currentNewsIndex = Math.min(currentNewsIndex + 1, maxIndex);
        updateNewsCarousel();
    }

    function prevNews() {
        currentNewsIndex = Math.max(currentNewsIndex - 1, 0);
        updateNewsCarousel();
    }

    // Update cards per view on window resize
    window.addEventListener('resize', () => {
        const newCardsPerView = window.innerWidth <= 480 ? 1 : window.innerWidth <= 768 ? 2 : 3;
        if (newCardsPerView !== cardsPerView) {
            updateNewsCarousel();
        }
    });
</script>