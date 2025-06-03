<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            font-family: 'Spartan', sans-serif;
        }

        .vision-section {
            position: relative;
            background-color: black;
            width: 100%;
            padding: 4rem 0;
            overflow: hidden;
        }

        .vision-bg {
            width: 100%;
            height: 22rem;
            object-fit: cover;
            opacity: 0.2;
        }

        @media (min-width: 640px) {
            .vision-bg {
                height: 20rem;
            }
        }

        @media (min-width: 768px) {
            .vision-bg {
                height: 24rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-bg {
                height: 28rem;
            }
        }

        .vision-content {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .vision-title {
            font-size: 5rem !important;
            font-weight: bold;
            padding: 1rem;
        }

        @media (min-width: 480px) {
            .vision-title {
                font-size: 1.25rem;
            }
        }

        @media (min-width: 640px) {
            .vision-title {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 768px) {
            .vision-title {
                font-size: 2.25rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-title {
                font-size: 3rem;
            }
        }

        .vision-text {
            width: 80%;
            font-size: 0.75rem;
            font-weight: 300;
        }

        @media (min-width: 480px) {
            .vision-text {
                font-size: 0.875rem;
            }
        }

        @media (min-width: 640px) {
            .vision-text {
                font-size: 1rem;
            }
        }

        @media (min-width: 768px) {
            .vision-text {
                font-size: 1.1rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-text {
                font-size: 1.2rem;
            }
        }


        .frame-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            place-items: center;
            gap: 50px;
            padding: 50px;
            max-width: 1400px;
            margin: 0px auto;
        }

        @media (max-width: 1250px) {
            .frame-container {
                grid-template-columns: repeat(1, 1fr);
                width: 100%;
            }

            .y-video {
                width: 100%;
            }

            .ifreav {
                width: 100%;
            }
        }

        .y-video {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="vision-section">
        <img src="./images/About3.png" alt="Vision Background" class="vision-bg" />
        <div class="vision-content">
            <h1 class="vision-title">Taleemi Sahara Media Team</h1>
            <p class="vision-text">
                At Taleemi Sahara, our Media Team plays a vital role in spreading awareness about our mission to support underprivileged students through donations and scholarships. We are dedicated to highlighting the stories, impact, and ongoing efforts of our foundation through engaging visuals, inspiring campaigns, and authentic storytelling. Our goal is to connect hearts and build a strong community that believes in equal access to education for all. Through the power of media, we aim to inspire action, encourage donations, and give a voice to those who need it most.
            </p>

        </div>
    </div>

    <div class="frame-container">
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <div class="y-video">
            <div class="" style="font-size: 1.2rem; font-family: Quicksand; font-weight: 600;">How do your donations help students?</div>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/QHCy8fP1py8?si=xWNRPhiQbb5PDrt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>


</body>

</html>