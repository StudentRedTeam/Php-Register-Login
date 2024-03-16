document.addEventListener("DOMContentLoaded", function() {
    // Function to handle button click
    function handleButtonClick(event) {
        event.preventDefault();
        alert("This is a placeholder. Add your login/register logic here.");
    }

    // Attach the function to each button
    var loginButton = document.getElementById("loginButton");
    var registerButton = document.getElementById("registerButton");

    if (loginButton) {
        loginButton.addEventListener("click", handleButtonClick);
    }

    if (registerButton) {
        registerButton.addEventListener("click", handleButtonClick);
    }
});
