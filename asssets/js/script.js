document.addEventListener("DOMContentLoaded", function () {
    // 1. Smooth Scroll untuk Link Navigasi Navbar & Tombol Hero
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            const targetId = this.getAttribute("href");
            
            if (targetId !== "#" && targetId.length > 1) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    
                    // Offset untuk fixed navbar (sekitar 80px)
                    const navbarHeight = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - navbarHeight;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });

                    // Tutup navbar mobile setelah link diklik
                    const navbarCollapse = document.getElementById("navbarNav");
                    if (navbarCollapse && navbarCollapse.classList.contains("show")) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                }
            }
        });
    });

    // 2. Active State Switcher pada Navbar saat Scroll
    const sections = document.querySelectorAll("section[id]");
    
    function highlightNavLinkOnScroll() {
        const scrollY = window.pageYOffset;

        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 100;
            const sectionId = current.getAttribute("id");
            const navLink = document.querySelector(`.navbar-nav a[href*="${sectionId}"]`);

            if (navLink) {
                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLink.classList.add("active");
                } else {
                    navLink.classList.remove("active");
                }
            }
        });
    }

    window.addEventListener("scroll", highlightNavLinkOnScroll);

    // 3. Auto Scroll ke Anchor #kontak jika Form Berhasil Dikirim (Setelah Submit)
    if (window.location.hash === "#kontak") {
        const kontakSection = document.getElementById("kontak");
        if (kontakSection) {
            setTimeout(() => {
                kontakSection.scrollIntoView({ behavior: "smooth" });
            }, 200);
        }
    }
});