import $ from 'jquery'; // eslint-disable-line

(($j) => {
  // Hide the selected item from the dropdown menu.
  function hideItemFromList() {
    const selectedValueText = $j('.js-dropdown__selected a').text();
    $j('.js-section-overview__list-item').each((index, el) => {
      // Hide selected value from the list.
      if (selectedValueText === $j(el).text()) {
        $j(el).hide();
      }
    });
  }

  // Retrieve the selected value.
  function getSeletedValue() {
    const $selectedValue = $j('.js-dropdown__selected a');
    const $breadcrumbAnchor = $j('.js-breadcrumb__anchor');

    $j('.js-section-overview__list-item').on('click', (evt) => {
      // Hide selected value and show the previous one.
      $j('.js-section-overview__list-item').show();
      $j(evt.currentTarget).hide();

      // Replace the selected value with the new one.
      const newValue = $j(evt.currentTarget).text();
      const newValueAnchor = $j(evt.currentTarget).attr('href').split('#')[1];
      $selectedValue.text(newValue).attr('href', `#${newValueAnchor}`);
      $breadcrumbAnchor.show().text(newValue);

      // If overview is clicked, scroll to top.
      if (newValue === 'Overview') {
        $j('html, body').animate({
          scrollTop: $j('body').offset().top,
        }, 'slow');
      }

      // Hide dropdown menu.
      $j('.js-dropdown__wrapper').removeClass('toggled');
    });
  }

  // Show or hide dropdown menu.
  function toggleAnchorList() {
    $j('.js-dropdown__selected').on('click', (evt) => {
      evt.preventDefault();
      const $anchor = $j(evt.currentTarget).closest('.js-dropdown__wrapper');

      if (!$anchor.hasClass('toggled')) {
        $anchor.addClass('toggled');
      } else {
        $anchor.removeClass('toggled');
      }
    });
  }

  // Add active state to anchor links when scrolling into sections.
  function getTitleAnchor() {
    if ($j('.js-dropdown').length) {
      let anchor = '';

      $j(window).on('resize scroll', () => {
        const headerHeight = $j('.js-header-nav').height();
        const topicSectionHeight = $j('.js-dropdown').outerHeight();
        const totalHeight = headerHeight + topicSectionHeight + 200;

        // Find the id of the title
        $j('.wp-block-mkl-section-block h3, .wp-block-mkl-section-block h2').each((index, el) => {
          if ($j(el).attr('id') !== undefined) {
            if ($j(window).scrollTop() + totalHeight >= $j(el).offset().top) {
              anchor = $j(el).attr('id');
            }

            // Clear anchor value when scrolling at the top of the page.
            if (index === 0) {
              if ($j(window).scrollTop() + totalHeight < $j(el).offset().top) {
                anchor = '';
              }
            }
          }
        });

        if (anchor !== '') {
          $j('.js-breadcrumb__anchor').show().text(anchor.replace(/-/g, ' '));
        }

        if ($j('.js-heading').height() > $j(window).scrollTop()) {
          anchor = '';
          $j('.js-section-overview__list-item').show();
          $j('.js-dropdown__selected a').text('Overview').attr('href', '#');
          $j('.js-breadcrumb__anchor').hide().text('');
          hideItemFromList();
        }
      });
    }
  }

  $j(document).ready(() => {
    hideItemFromList();
    toggleAnchorList();
    getSeletedValue();
    getTitleAnchor();
  });
})(jQuery);
