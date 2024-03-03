(function($) {
    "use strict";

    var dropdown = document.querySelectorAll('[data-dropdown]');
    if (dropdown) {
        dropdown.forEach(function(el) {
            window.addEventListener("click", function(e) {
                if (el.contains(e.target)) {
                    el.classList.toggle("active");
                    setTimeout(function() {
                        el.classList.toggle("animated");
                    }, 10);
                } else {
                    el.classList.remove("active");
                    el.classList.remove("animated");
                }
            });
        });
    }

    let themeBtn = document.querySelector(".btn-theme"),
        logoDark = document.querySelector(".logo-dark"),
        logoLight = document.querySelector(".logo-light");
    if (themeBtn) {
        themeBtn.onclick = () => {
            document.body.classList.toggle("dark");
            if (document.body.classList.contains("dark")) {
                document.cookie = "Theme=dark; expires=31 Dec 2080 12:00:00 GMT; path=/";
                logoDark.classList.add("d-none");
                logoLight.classList.remove("d-none");
            } else {
                document.cookie = "Theme=light; expires=31 Dec 2080 12:00:00 GMT; path=/";
                logoLight.classList.add("d-none");
                logoDark.classList.remove("d-none");
            }
        };
    }

    if (document.cookie.indexOf("Theme=dark") != -1) {
        document.body.classList.add("dark");
        logoDark.classList.add("d-none");
        logoLight.classList.remove("d-none");
    } else if (document.cookie.indexOf("Theme=light") != -1) {
        document.body.classList.remove("dark");
        logoLight.classList.add("d-none");
        logoDark.classList.remove("d-none");
    } else {
        if (config.themeMode == "auto") {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add("dark");
                logoDark.classList.add("d-none");
                logoLight.classList.remove("d-none");
            } else {
                document.body.classList.remove("dark");
                logoLight.classList.add("d-none");
                logoDark.classList.remove("d-none");
            }
        } else if (config.themeMode == "dark") {
            document.body.classList.add("dark");
            logoDark.classList.add("d-none");
            logoLight.classList.remove("d-none");
        } else {
            document.body.classList.remove("dark");
            logoLight.classList.add("d-none");
            logoDark.classList.remove("d-none");
        }
    }

})(jQuery);