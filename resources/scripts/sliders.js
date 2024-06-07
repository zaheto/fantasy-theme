// sliders.js
import Swiper from 'swiper/bundle';
import 'swiper/swiper-bundle.css';

export default function initSliders() {


  jQuery(function($) {

    // Initialize the subcategories slider
    var subcategoriesSlider = new Swiper('.subcategories-slider', {
        loop: true,
        spaceBetween: 8,
        keyboard: {
            enabled: true,
        },
        navigation: {
            nextEl: '.slider-subcategories-next',
            prevEl: '.slider-subcategories-prev',
            hideOnClick: true,
        },
        breakpoints: {
            320: { // Mobile
                slidesPerView: 3,
                spaceBetween: 8,
            },
            768: { // Tablet
                slidesPerView: 4,
                spaceBetween: 8,
            },
            1024: { // Desktop
                slidesPerView: 7,
                spaceBetween: 8,
            },
        },
    });

    // Show/hide navigation buttons based on the number of slides
    var numberOfSlides = document.querySelectorAll('.subcategories-slider .swiper-slide').length;
    var maxSlidesPerView = 7;

    if (numberOfSlides <= maxSlidesPerView) {
        var nextButton = document.querySelector('.slider-subcategories-next');
        var prevButton = document.querySelector('.slider-subcategories-prev');

        if (nextButton) {
            nextButton.style.display = 'none';
        }
        if (prevButton) {
            prevButton.style.display = 'none';
        }
    } else {
        var nextButton = document.querySelector('.slider-subcategories-next');
        var prevButton = document.querySelector('.slider-subcategories-prev');

        if (nextButton) {
            nextButton.style.display = 'flex';
        }
        if (prevButton) {
            prevButton.style.display = 'flex';
        }
    }


    // Initialize the homepage slider
    var homepageSlider = new Swiper(".slider-home", {
      loop: true,
      slidesPerView: 1,
      keyboardControl: true,
      keyboard: true,
      pagination: {
        el: '.swiper-pagination',
      },
      navigation: {
        nextEl: '.slider-home-next',
        prevEl: '.slider-home-prev',
      },
    });

    // Check the number of slides
    var numberOfSlides = document.querySelectorAll('.slider-home .swiper-slide').length;

    // Hide navigation buttons if there's only one slide
    if (numberOfSlides <= 1) {
      var nextButton = document.querySelector('.slider-home-next');
      var prevButton = document.querySelector('.slider-home-prev');

      if (nextButton) {
        nextButton.style.display = 'none';
      }
      if (prevButton) {
        prevButton.style.display = 'none';
      }
    }

    var productList = new Swiper(".product-list-slider", {
      freeMode: true,
      watchSlidesProgress: true,
      touchRatio: 0.2,
      slideToClickedSlide: true,
      breakpoints: {
        768: {
          slidesPerView: 2,
          centeredSlides: true,
          spaceBetween: 4,
        },
        1280: {
          spaceBetween: 4,
          slidesPerView: 7,
        },
      },
    });

    var categorySlider = new Swiper(".category-list-builder", {
      autoHeight: true,
      keyboardControl: true,
      keyboard: true,
      slidesPerView: 2,
      spaceBetween: 12,
      navigation: {
        nextEl: '.category-list-next',
        prevEl: '.category-list-prev',
      },
      breakpoints: {
        760: {
          slidesPerView: 2,
          spaceBetween: 12,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 12,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 12,
        },
        1280: {
          spaceBetween: 16,
          slidesPerView: 5,
        },
        1440: {
          spaceBetween: 16,
          slidesPerView: 6,
        },
      },
    });

    var moreProductsSlider = new Swiper(".more-products-slider", {
      loop: false,
      autoHeight: true,
      keyboardControl: true,
      keyboard: true,
      slidesPerView: 2,
      spaceBetween: 12,
      breakpoints: {
        760: {
          slidesPerView: 2,
          spaceBetween: 12,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          }
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 12,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          }
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 12,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.more-products-next',
            prevEl: '.more-products-prev',
          }
        },
        1280: {
          spaceBetween: 16,
          slidesPerView: 5,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.more-products-next',
            prevEl: '.more-products-prev',
          }
        },
        1440: {
          spaceBetween: 16,
          slidesPerView: 5,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.more-products-next',
            prevEl: '.more-products-prev',
          }
        },
      },
    });

    var galleryThumbs = new Swiper(".product-thumbnails", {
      freeMode: true,
      slidesPerView: 5,
      spaceBetween: 4,
      watchSlidesProgress: true,
      touchRatio: 0.2,
      slideToClickedSlide: true,
      breakpoints: {
        760: {
          slidesPerView: 4,
          centeredSlides: true,
          spaceBetween: 4,
        },
        768: {
          slidesPerView: 5,
          centeredSlides: true,
          spaceBetween: 4,
        },
        1280: {
          spaceBetween: 10,
          slidesPerView: 7,
        },
      },
    });

    var landingGallery = new Swiper(".landing-gallery", {
      grabCursor: true,
      a11y: false,
      freeMode: true,
      speed: 11000,
      loop: true,
      slidesPerView: "auto",
      autoplay: {
        delay: 0.5,
        disableOnInteraction: false,
      },
      breakpoints: {
        760: {
          slidesPerView: 2
        },
        768: {
          slidesPerView: 3
        },
        1024: {
          slidesPerView: 4
        },
        1280: {
          slidesPerView: 5
        },
        1440: {
          slidesPerView: 5
        },
      },
    });

    var howToSlider = new Swiper(".how-to-list", {
      slidesPerView: 2,
      spaceBetween: 12,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true
      },
      breakpoints: {
        760: {
          slidesPerView: 2
        },
        768: {
          slidesPerView: 2
        },
        1024: {
          slidesPerView: 3
        },
        1280: {
          slidesPerView: 3
        },
        1440: {
          slidesPerView: 3
        },
      },
    });

    var featuresLanding = new Swiper(".features-landing", {
      loop: true,
      slidesPerView: 2,
      spaceBetween: 12,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      breakpoints: {
        760: {
          slidesPerView: 1,
          spaceBetween: 12
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 12
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 12
        },
        1280: {
          spaceBetween: 16,
          slidesPerView: 4
        },
        1440: {
          spaceBetween: 32,
          slidesPerView: 4
        },
      },
    });

    var galleryTop = new Swiper(".product-main-images", {
      spaceBetween: 10,
      loop: true,
      keyboardControl: true,
      keyboard: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      thumbs: {
        swiper: galleryThumbs,
      },
    });

    var swiperSlides = Array.from(galleryTop.slides);

    swiperSlides.forEach(function(slide) {
      openFullscreenSliderHandler(slide);
      closeFullscreenSliderHandler(slide);
    });

    function openFullscreenSliderHandler(slide) {
      var slideImage = slide.querySelector('img');
      slideImage.addEventListener('click', function() {
        var slideNumber = slide.dataset.swiperSlideIndex;
        openFullscreenSwiper(slideNumber);
      });
    }

    function openFullscreenSwiper(slideNumber) {
      galleryTop.el.classList.add('fullscreen');
      galleryTop.params.slidesPerView = 1;
      galleryTop.update();
      galleryTop.slideToLoop(parseInt(slideNumber, 10), 0);
    }

    function closeFullscreenSliderHandler(slide) {
      var slideNumber = slide.dataset.swiperSlideIndex;
      var backdrop = document.createElement('div');
      var closeButton = document.createElement('div');

      slide.appendChild(backdrop);
      slide.appendChild(closeButton);
      backdrop.classList.add('backdrop');
      closeButton.classList.add('close-button');
      closeButton.innerHTML = '×';

      backdrop.addEventListener('click', function() {
        closeFullscreenSwiper(slideNumber);
      });

      closeButton.addEventListener('click', function() {
        closeFullscreenSwiper(slideNumber);
      });
    }

    function closeFullscreenSwiper(slideNumber) {
      galleryTop.el.classList.remove('fullscreen');
      galleryTop.params.slidesPerView = 1;
      galleryTop.update();
      galleryTop.slideToLoop(parseInt(slideNumber, 10), 0);
    }





  });
}
