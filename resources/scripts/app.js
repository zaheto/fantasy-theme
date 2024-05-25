import domReady from '@roots/sage/client/dom-ready';

// if you're using a bundler, first import:
import Headroom from "headroom.js";

//import Swiper from 'swiper';
import Swiper from 'swiper/bundle';

/**
 * Application entrypoint
 */
domReady(async () => {


  // grab an element
  var myElement = document.querySelector(".header");
  var options = {
    // vertical offset in px before element is first unpinned
    offset : 0,
    // or you can specify offset individually for up/down scroll
    offset: {
        up: 100,
        down: 250
    },
    // scroll tolerance in px before state changes
    tolerance : 0,
    // or you can specify tolerance individually for up/down scroll
    tolerance : {
        up : 5,
        down : 0
    },
  };
  // construct an instance of Headroom, passing the element
  var headroom = new Headroom(myElement, options);
  // initialise
  headroom.init();


});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
