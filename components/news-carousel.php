<?php
require_once 'config/database.php';

// Fetch active news items from the database
try {
    $result = $conn->query("SELECT * FROM news WHERE status = 'active' ORDER BY date DESC LIMIT 10");
    $news_items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (mysqli_sql_exception $e) {
    // If the table doesn't exist or there's an error, use an empty array
    $news_items = [];
}
?>

<style>
    .news-section {
        padding: 4rem 0;
        background-color: aliceblue;
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
        max-width: 300px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
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
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
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

    /* News Modal Styles */
    .news-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 2000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .news-modal-content {
        background-color: white;
        border-radius: 15px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: newsModalSlideIn 0.3s ease-out;
    }

    @keyframes newsModalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .news-modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 2rem;
        cursor: pointer;
        color: #666;
        transition: color 0.3s;
        z-index: 2001;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-modal-close:hover {
        color: #333;
        background: white;
    }

    .news-modal-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }

    .news-modal-body {
        padding: 30px;
    }

    .news-modal-date {
        color: #666;
        font-size: 1rem;
        margin-bottom: 15px;
        font-style: italic;
    }

    .news-modal-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
        line-height: 1.3;
    }

    .news-modal-description {
        color: #555;
        font-size: 1.1rem;
        line-height: 1.7;
        text-align: justify;
    }

    .read-more-btn {
        background-color: #465462;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        margin-top: 15px;
        transition: background-color 0.3s;
    }

    .read-more-btn:hover {
        background-color: #96ADC5;
    }

    @media (max-width: 768px) {
        .news-card {
            min-width: calc(50% - 20px);
        }

        .news-modal-content {
            width: 95%;
            margin: 10px;
        }

        .news-modal-body {
            padding: 20px;
        }

        .news-modal-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .news-card {
            min-width: calc(100% - 20px);
        }
    }
</style>

<section id="news" class=" news-section">
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem; padding-top: 2rem; padding-bottom: 1rem; background-color: aliceblue; margin-top: 0rem; border-top: 2px solid black;white-space: nowrap;">
        <div style="font-size: 6rem; font-weight: bold; font-family: 'Quicksand', serif; padding: 0 2rem; text-align: center; max-width: 450px; font-size: 3rem;">
            Latest
            <span
                style="font-family: 'Dancing Script', cursive; font-size: 7rem; max-width: 450px; font-size: 5rem; color: #96ADC5; text-shadow: 1px 1px 2px rgba(0, 0, 0, 1)">
                News
            </span>
            !
        </div>
    </div>
    <div class="news-carousel">
        <div class="news-container">
            <?php if (!empty($news_items)): ?>
                <?php foreach ($news_items as $news): ?>
                    <div class="news-card" onclick="openNewsModal(<?php echo htmlspecialchars(json_encode($news)); ?>)">
                        <img src="<?php echo htmlspecialchars($news['image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-image">
                        <div class="news-content">
                            <div class="news-date"><?php echo date('M j, Y', strtotime($news['date'])); ?></div>
                            <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p class="news-description"><?php echo htmlspecialchars($news['description']); ?></p>
                            <button class="read-more-btn">Read More</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%; color: #666;">No recent news to display.</p>
            <?php endif; ?>
        </div>
        <button class="news-button news-prev" onclick="prevNews()">❮</button>
        <button class="news-button news-next" onclick="nextNews()">❯</button>
    </div>
</section>

<!-- News Modal -->
<div class="news-modal-overlay" id="newsModal">
    <div class="news-modal-content">
        <span class="news-modal-close" onclick="closeNewsModal()">&times;</span>
        <img id="modal-news-image" src="" alt="" class="news-modal-image">
        <div class="news-modal-body">
            <div id="modal-news-date" class="news-modal-date"></div>
            <h2 id="modal-news-title" class="news-modal-title"></h2>
            <p id="modal-news-description" class="news-modal-description"></p>
        </div>
    </div>
</div>

<script>
    let currentNewsIndex = 0;
    const newsContainer = document.querySelector('.news-container');
    if (newsContainer) {
        const newsCards = newsContainer.querySelectorAll('.news-card');
        const cardsPerView = window.innerWidth <= 480 ? 1 : window.innerWidth <= 768 ? 2 : 3;

        function updateNewsCarousel() {
            if (newsCards.length > cardsPerView) {
                const offset = currentNewsIndex * (100 / cardsPerView);
                newsContainer.style.transform = `translateX(-${offset}%)`;
            }
        }

        function nextNews() {
            if (newsCards.length > cardsPerView) {
                const maxIndex = newsCards.length - cardsPerView;
                currentNewsIndex = Math.min(currentNewsIndex + 1, maxIndex);
                updateNewsCarousel();
            }
        }

        function prevNews() {
            if (newsCards.length > 0) {
                currentNewsIndex = Math.max(currentNewsIndex - 1, 0);
                updateNewsCarousel();
            }
        }

        // Update cards per view on window resize
        window.addEventListener('resize', () => {
            const newCardsPerView = window.innerWidth <= 480 ? 1 : window.innerWidth <= 768 ? 2 : 3;
            if (newCardsPerView !== cardsPerView) {
                // This is a simple version. A more robust solution might recalculate cardsPerView and reset index.
                location.reload();
            }
        });

        if (newsCards.length <= cardsPerView) {
            document.querySelector('.news-prev').style.display = 'none';
            document.querySelector('.news-next').style.display = 'none';
        }
    }

    // News Modal Functions
    function openNewsModal(newsData) {
        const modal = document.getElementById('newsModal');
        const modalImage = document.getElementById('modal-news-image');
        const modalDate = document.getElementById('modal-news-date');
        const modalTitle = document.getElementById('modal-news-title');
        const modalDescription = document.getElementById('modal-news-description');

        // Set modal content
        modalImage.src = newsData.image;
        modalImage.alt = newsData.title;
        modalDate.textContent = new Date(newsData.date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        modalTitle.textContent = newsData.title;
        modalDescription.textContent = newsData.description;

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeNewsModal() {
        const modal = document.getElementById('newsModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('newsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNewsModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNewsModal();
        }
    });
</script>