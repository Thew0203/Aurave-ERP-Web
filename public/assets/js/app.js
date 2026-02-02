/* Aruave – Electronics & IT ERP – Application scripts */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
      if (window.bootstrap && window.bootstrap.Tooltip) {
        new window.bootstrap.Tooltip(el);
      }
    });

    // Navbar: add scrolled class on scroll for subtle shadow
    var navbar = document.querySelector('.navbar-aruave');
    if (navbar) {
      function onScroll() {
        if (window.scrollY > 20) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      }
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    // Smooth scroll for anchor links (e.g. #features)
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      var href = anchor.getAttribute('href');
      if (href && href.length > 1) {
        anchor.addEventListener('click', function (e) {
          var target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      }
    });

    // Optional: animate elements on scroll (add .animate-on-scroll to elements)
    var animateEls = document.querySelectorAll('.animate-on-scroll');
    if (animateEls.length && 'IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-slide-up');
            observer.unobserve(entry.target);
          }
        });
      }, { rootMargin: '0px 0px -40px 0px', threshold: 0 });
      animateEls.forEach(function (el) { observer.observe(el); });
    }
  });
})();
