import $ from 'jquery'; // eslint-disable-line

(($j) => {
  // Apply filter for the grid according the selected filter.
  function filterGrid(grid) {
    $j('.js-filter-item').on('click', (evt) => {
      const filterVal = $j(evt.currentTarget).attr('filter');

      $j('.js-grid__item').each((index, el) => {
        if (!$j(el).hasClass(filterVal)) {
          $j(el).hide();
        } else {
          $j(el).show();
        }
      });

      if (filterVal === 'all') {
        $j('.js-grid__item').show();
      }

      // Hide dropdown menu.
      $j('.js-dropdown__wrapper').removeClass('toggled');

      // Update layout only when Masonry is active
      if (window.innerWidth > 670) {
        grid.masonry('layout');
      }

      // Replace the selected value with the new one.
      const newValue = $j(evt.currentTarget).text();
      $j('.js-dropdown__selected span').text(newValue);
      $j('.js-filter-item').show();
      $j(evt.currentTarget).hide();
    });
  }

  // Init or destroy Masonry according the width of the screen.
  function initOrDestroyMasonry() {
    const masonryOptions = {
      itemSelector: '.js-grid__item',
      columnWidth: 300,
      percentPosition: true,
      gutter: 20,
      fitWidth: true,
    };

    const $grid = $j('.js-grid-wrapper').masonry(masonryOptions);
    let isActive = true;

    $j(window).on('resize', () => {
      if (window.innerWidth > 670) {
        if (!isActive) {
          $grid.masonry(masonryOptions);
          isActive = true;
        }
      } else if (isActive) {
        $grid.masonry('destroy');
        isActive = false;
      }
    }).trigger('resize');

    filterGrid($grid);
  }

  function openVideoModal() {
    $j('.js-video__thumbnail').on('click', (evt) => {
      $j('body').addClass('fixed');

      $j(evt.currentTarget).closest('.videos').find('.js-video-modal').addClass('open');

      setTimeout(() => {
        const $iframe = $j(evt.currentTarget).closest('.videos').find('iframe');
        let src = $iframe.attr('src').replace(/&autoplay=\d/, '');
        src += '&autoplay=1';
        $iframe.attr('src', src);
      }, 100);
    });
  }

  function closeVideoModal() {
    $j('.js-video-modal').on('click', (evt) => {
      $j('body').removeClass('fixed');

      if ($j('.js-has-video').length) {
        $j('.js-site-header').css('z-index', 9);
      }

      $j(evt.currentTarget).removeClass('open');

      setTimeout(() => {
        const $iframe = $j(evt.currentTarget).find('iframe');
        const src = $iframe.attr('src');
        const newSrc = src.replace('autoplay=1', 'autoplay=0');
        $iframe.attr('src', newSrc);
      }, 100);
    });
  }

  $j(document).ready(() => {
    initOrDestroyMasonry();
    openVideoModal();
    closeVideoModal();
  });
})(jQuery);
