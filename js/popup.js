'use strict';

$(document).ready(function () {

  $('.popup-gallery').magnificPopup({
    delegate: 'a.popup',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    fixedContentPos: false,
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
      tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
      titleSrc: function titleSrc(item) {
        return item.el.attr('title');
      }
    }
  });

  $('.popup').click(function () {
    $(this).magnificPopup();
    $(this).blur();
  });

  $('.popup-gallery').find('a.download').click(function () {
    $(this).blur();
  });
});