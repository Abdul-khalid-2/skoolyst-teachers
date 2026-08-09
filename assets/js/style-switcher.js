// toggle Style switcher
// (guarded with null checks: this panel's markup isn't present on every
// page, e.g. the public portfolio view, so we avoid throwing on load)
const styleSwitcherToggle = document.querySelector(".style-switcher-toggler");
const styleSwitcherPanel = document.querySelector(".style-switcher");
if (styleSwitcherToggle && styleSwitcherPanel) {
    styleSwitcherToggle.addEventListener("click", () => {
        styleSwitcherPanel.classList.toggle("open");
    });
    //hide style switcher on scroll
    window.addEventListener("scroll", () => {
        if (styleSwitcherPanel.classList.contains("open")) {
            styleSwitcherPanel.classList.remove("open");
        }
    });
}

// Them color
const alternateStyle = document.querySelectorAll(".alternate-style");
function setActiveStyle(color){
    alternateStyle.forEach((style) =>{
        if(color === style.getAttribute('title'))
        {
            style.removeAttribute("disabled");
        }
        else{
            style.setAttribute("disabled" ,"true");
        }
    })
}

// them light and dark mode
const dayNight = document.querySelector(".day-night");
if (dayNight) {
    dayNight.addEventListener("click", () => {
        dayNight.querySelector("i").classList.toggle("fa-sun");
        dayNight.querySelector("i").classList.toggle("fa-moon");
        document.body.classList.toggle("dark");
    });
    window.addEventListener("load", () => {
        if (document.body.classList.contains("dark")) {
            dayNight.querySelector("i").classList.add("fa-sun");
        } else {
            dayNight.querySelector("i").classList.add("fa-moon");
        }
    });
}














