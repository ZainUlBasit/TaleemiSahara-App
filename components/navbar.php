<style>
    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
        font-family: "Raleway", sans-serif;
    }

    ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        width: 100%;
        max-width: 1400px;
        padding: 10px 20px;
        margin: 0px auto;
    }

    .navbar-wrapper {
        width: 100%;
        position: fixed;
        z-index: 1000;
        /* background-color: white; */
        background-color: #96ADC5;
        border-bottom: 4px solid #465462;
    }

    .navbar a {
        text-decoration: none;
        color: inherit;
        font-weight: 600;
        transition: text-decoration 1s ease-in-out;
    }

    .navbar a:hover {
        text-decoration: underline;
    }

    /* From Uiverse.io by satyamchaudharydev */
    .button {
        position: relative;
        transition: all 0.3s ease-in-out;
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        padding-block: 0.5rem;
        padding-inline: 1.25rem;
        background-color: rgb(0 107 179);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffff;
        gap: 10px;
        font-weight: bold;
        border: 3px solid #ffffff4d;
        outline: none;
        overflow: hidden;
        font-size: 15px;
        cursor: pointer !important;
    }

    .icon {
        width: 24px;
        height: 24px;
        transition: all 0.3s ease-in-out;
    }

    .button:hover {
        transform: scale(1.05);
        border-color: #fff9;
    }

    .button:hover .icon {
        transform: translate(4px);
    }

    .button:hover::before {
        animation: shine 1.5s ease-out infinite;
    }

    .button::before {
        content: "";
        position: absolute;
        width: 100px;
        height: 100%;
        background-image: linear-gradient(120deg,
                rgba(255, 255, 255, 0) 30%,
                rgba(255, 255, 255, 0.8),
                rgba(255, 255, 255, 0) 70%);
        top: 0;
        left: -100px;
        opacity: 0.6;
    }

    @keyframes shine {
        0% {
            left: -100px;
        }

        60% {
            left: 100%;
        }

        to {
            left: 100%;
        }
    }

    .auth-btn {
        border: 3px solid #465462;
        background-color: white;
        font-size: 1rem;
        padding: 8px 20px;
        border-radius: 5px;
        font-family: "Quicksand";
        font-weight: 900;
        transition: all 1s ease-in-out;
        cursor: pointer;
        color: black;
    }

    .auth-btn:hover {
        background-color: #465462;
        color: white;
    }
</style>

<div class="navbar-wrapper">
    <div class="navbar">
        <img src="./images/logo.png" alt="not found!" style="width: 150px" />
        <ul>
            <li><a href="#home" class="raleway-400">Home</a></li>
            <li><a href="#our-mission" class="raleway-400">Our Mission</a></li>
            <li><a href="#our-impacts" class="raleway-400">Our Impacts</a></li>
            <li><a href="#our-team" class="raleway-400">Our Teams</a></li>
            <li><a href="./our-media.php" class="raleway-400">Our Media</a></li>
            <li><a href="#news" class="raleway-400">News</a></li>
            <!-- <li><a href="#" class="raleway-400">Blogs</a></li> -->
            <li>
                <!-- From Uiverse.io by satyamchaudharydev -->
                <!-- From Uiverse.io by lenfear23 -->
                <button class="auth-btn raleway-600">
                    Login
                </button>
            </li>
            <li>
                <!-- From Uiverse.io by satyamchaudharydev -->
                <!-- From Uiverse.io by lenfear23 -->
                <button class="auth-btn raleway-600">
                    Register
                </button>
            </li>
        </ul>
    </div>
</div>