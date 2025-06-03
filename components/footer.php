<style>
    body {
        margin: 0;
        font-family: 'Spartan', sans-serif;
    }

    .footer {
        background-color: #96ADC5;
        color: black;
        padding: 40px 20px;
    }

    .footer-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        max-width: 1400px;
        margin: auto;
    }

    .footer-section {
        flex: 1 1 300px;
        padding: 20px;
    }



    #logo-section .logo {
        height: 100px;
    }

    .description {
        margin-top: 10px;
        font-size: 0.95rem;
        color: black;
        max-width: 400px;
        text-align: justify;
    }

    .footer-links,
    .contact-info {
        list-style: none;
        padding: 0;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-start;
    }


    .footer-links a,
    .contact-info a {
        color: black !important;
        text-decoration: none;
    }

    .footer-links a:hover,
    .contact-info a:hover {
        text-decoration: underline;
        color: #fff;
    }

    .footer-bottom {
        border-top: 1px solid #555;
        margin-top: 30px;
        padding-top: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
    }

    .footer-bottom p {
        margin: 5px 0;
        font-size: 0.85rem;
    }

    .social-links {
        margin-top: 10px;
    }

    .social-links a {
        margin-right: 10px;
    }

    .social-links img {
        width: 24px;
        height: 24px;
    }

    /* Responsive */
    @media (min-width: 768px) {
        .footer-bottom {
            flex-direction: row;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section" id="logo-section">
            <a href="/">
                <img src="./images/logo.png" alt="Logo" class="logo" />
            </a>
            <p class="description">
                At Taleemi Sahara, we are dedicated to empowering underprivileged students through education. Our mission is to provide scholarships, financial aid, and academic support to those who need it most. With the support of our donors and partners, we aim to create equal opportunities for every child to learn, grow, and succeed—regardless of their financial background.
            </p>

        </div>

        <div class="footer-section" id="links-section">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="#home" class="raleway-400">Home</a></li>
                <li><a href="#our-mission" class="raleway-400">Our Mission</a></li>
                <li><a href="#" class="raleway-400">Our Impacts</a></li>
                <li><a href="#" class="raleway-400">Our Teams</a></li>
                <li><a href="#" class="raleway-400">News</a></li>
            </ul>
        </div>

        <div class="footer-section contact-section" id="">
            <h3>Reach Us</h3>
            <ul class="contact-info">
                <li><a href="https://wa.me/+923319042434">📞 +92 333 1234567</a></li>
                <li><a href="mailto:info@appspot.com.pk">✉️ info@taleemisahara.com.pk</a></li>
                <li>
                    <a href="https://maps.app.goo.gl/7xGgVB4VNwAwAD6F8">
                        📍 Women University Swabi
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2025 Taleemi Sahara All rights reserved.</p>
        <div class="social-links">
            <a href="#"><img src="facebook-icon.png" alt="Facebook" /></a>
            <a href="#"><img src="linkedin-icon.png" alt="LinkedIn" /></a>
        </div>
    </div>
</footer>