document.addEventListener("DOMContentLoaded", function () {
  // Ensure the form submission is handled correctly (if you are submitting via JS)
  const form = document.querySelector(".login-form");
  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission
    window.location.href = "../Pages/Home_page.php";
  });
});
