// typing animation 

// var typed =new typed(".typing",{
//     strings:["Web Designer","Web Developer","Graphic Designer","Serch Engien Optimization"],
//     typeSpeed:100,
//     BackSpeed:60,
//     loop:true
// })


document.addEventListener("DOMContentLoaded", function () {
    var el = document.querySelector(".typing");
    if (!el || typeof Typed === "undefined") return;

    var raw = el.getAttribute("data-typed-strings");
    var strings = raw ? raw.split("|").map(function (s) { return s.trim(); }).filter(Boolean) : null;

    var typed = new Typed(".typing", {
        strings: strings && strings.length ? strings : [el.textContent || "Teacher"],
        typeSpeed: 100,
        backSpeed: 60,
        loop: true
    });
});
