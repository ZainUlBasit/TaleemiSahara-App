// Modal elements
const loginModal = document.getElementById("loginModal");
const registerModal = document.getElementById("registerModal");
const loginBtn = document.querySelector(".btn-login");
const registerBtn = document.querySelector(".btn-register");
const closeBtns = document.querySelectorAll(".close-modal");

// Function to prevent background scrolling
function preventScroll() {
  document.body.style.overflow = "hidden";
}

// Function to enable background scrolling
function enableScroll() {
  document.body.style.overflow = "auto";
}

// Open login modal
loginBtn.addEventListener("click", (e) => {
  e.preventDefault();
  loginModal.classList.add("modal-flex");
  preventScroll();
});

// Open register modal
registerBtn.addEventListener("click", (e) => {
  e.preventDefault();
  registerModal.classList.add("modal-flex");
  preventScroll();
});

// Close modals when clicking the close button
closeBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    loginModal.classList.remove("modal-flex");
    registerModal.classList.remove("modal-flex");
    enableScroll();
  });
});

// Close modals when clicking outside
window.addEventListener("click", (e) => {
  if (e.target === loginModal) {
    loginModal.classList.remove("modal-flex");
    enableScroll();
  }
  if (e.target === registerModal) {
    registerModal.classList.remove("modal-flex");
    enableScroll();
  }
});

// Handle form submissions
document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  try {
    const response = await fetch("login.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      throw new TypeError("Oops, we haven't got JSON!");
    }

    const data = await response.json();
    if (data.success) {
      enableScroll();
      window.location.href = data.redirect;
    } else {
      document.getElementById("loginError").textContent = data.error;
    }
  } catch (error) {
    console.error("Error:", error);
    document.getElementById("loginError").textContent =
      "An error occurred. Please try again.";
  }
});

document
  .getElementById("registerForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const response = await fetch("register.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        throw new TypeError("Oops, we haven't got JSON!");
      }

      const data = await response.json();
      if (data.success) {
        document.getElementById("registerSuccess").textContent = data.message;
        setTimeout(() => {
          registerModal.classList.remove("modal-flex");
          loginModal.classList.add("modal-flex");
        }, 2000);
      } else {
        document.getElementById("registerError").textContent = data.error;
      }
    } catch (error) {
      console.error("Error:", error);
      document.getElementById("registerError").textContent =
        "An error occurred. Please try again.";
    }
  });

// Switch between login and register modals
document.getElementById("showRegister").addEventListener("click", (e) => {
  e.preventDefault();
  loginModal.classList.remove("modal-flex");
  registerModal.classList.add("modal-flex");
});

document.getElementById("showLogin").addEventListener("click", (e) => {
  e.preventDefault();
  registerModal.classList.remove("modal-flex");
  loginModal.classList.add("modal-flex");
});
