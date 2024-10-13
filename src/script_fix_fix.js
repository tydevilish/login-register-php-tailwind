const bgchange = document.getElementById("bgchange");
const themeIcon = document.getElementById("theme-icon");

// Function to toggle theme and update icon
function toggleTheme() {
    document.documentElement.classList.toggle('dark');

    // Update localStorage based on the current theme
    if (document.documentElement.classList.contains('dark')) {
        localStorage.setItem('theme', 'dark');
        bgchange.style.display = 'none';  // Hide sun icon
        themeIcon.style.display = 'block'; // Show moon icon
    } else {
        localStorage.setItem('theme', 'light');
        bgchange.style.display = 'block';  // Show sun icon
        themeIcon.style.display = 'none';   // Hide moon icon
    }
}

// Check the theme status from localStorage on page load
if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark');
    bgchange.style.display = 'none'; // Hide sun icon
    themeIcon.style.display = 'block'; // Show moon icon
} else {
    bgchange.style.display = 'block'; // Show sun icon
    themeIcon.style.display = 'none'; // Hide moon icon
}

// Add event listener for the button
bgchange.addEventListener("click", toggleTheme);
themeIcon.addEventListener("click", toggleTheme); // Also allow clicking on the moon icon


const menuToggle = document.getElementById('menu-toggle');
const navbarHamburger = document.getElementById('navbar-hamburger');

function closeAlert() {
    document.getElementById("alertBox").style.display = "none";
}

menuToggle.addEventListener('click', () => {
    navbarHamburger.classList.toggle('hidden');
});

window.addEventListener('click', function (event) {
    // Check if the click is outside the menu toggle or navbar hamburger
    if (!menuToggle.contains(event.target) && !navbarHamburger.contains(event.target)) {
        navbarHamburger.classList.add('hidden');
    }
});



console.log("js test 123456")

