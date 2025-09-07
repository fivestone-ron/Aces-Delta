import $ from 'jquery'; // eslint-disable-line

(($j) => {
  /**
   * navigation.js
   *
   * Handles toggling the navigation menu for small screens.
   * Michou
   */

  const $button = $j('.menu-toggle');
  const $menu = $j('.main-navigation .menu');
  let $windowWidth;

  function stickyMenu() {
    const $headerHeight = $j('.site-header').outerHeight();
    $j('.site-content').css('margin-top', $headerHeight);
  }

  // Add a smoothScroll for anchor nav links.
  function smoothScroll() {
    $j(document).on('click', 'a[href^="#"]', (event) => {
      event.preventDefault();

      $j('.menu-item a').removeClass('active');
      $j(event.currentTarget).addClass('active');

      if ($j(window).width() < 768) {
        $j('html, body').animate({
          scrollTop: $j($j.attr(event.currentTarget, 'href')).offset().top - 120,
        }, 500);
        $j('.main-navigation').removeClass('active');
      } else {
        $j('html, body').animate({
          scrollTop: $j($j.attr(event.currentTarget, 'href')).offset().top - 150,
        }, 500);
      }
    });
  }

  // Add a white background color to navigation when scrolling.
  function addBackgroundToNav() {
    const $siteHeader = $j('.js-site-header');
    $j(window).on('scroll touchmove', () => {
      if ($j(window).scrollTop() > 5) {
        $siteHeader.addClass('scrolled');
      } else {
        $siteHeader.removeClass('scrolled');
      }
    }).trigger('scroll');
  }

  // - - - Make the anchor nav sticky on scroll - - -
  function stickyAnchorNav() {
    const $dropdown = $j('.js-dropdown');
    const $siteHeader = $j('.js-site-header');
    const $heading = $j('.js-heading');
    const $entryContent = $j('.entry-content');

    if ($dropdown.length) {
      $j(window).on('scroll touchmove', () => {
        if ($j(window).scrollTop() > ($heading.height() + 60) - $siteHeader.height()) {
          $dropdown.addClass('fixed');
          $siteHeader.addClass('has-anchor-nav-fixed');
          $entryContent.addClass('fixed-nav');
        } else {
          $dropdown.removeClass('fixed');
          $siteHeader.removeClass('has-anchor-nav-fixed');
          $entryContent.removeClass('fixed-nav');
        }
      }).trigger('scroll');
    }
  }

  $j(document).ready(() => {
    smoothScroll();
    addBackgroundToNav();
    stickyAnchorNav();

    if (!$menu.hasClass('nav-menu')) {
      $menu.addClass('nav-menu');
    }

    $button.off().on('click', (e) => {
      e.stopPropagation();
      e.preventDefault();

      $j('.main-navigation').toggleClass('active');
      $j(e.currentTarget).toggleClass('active');
      return false;
    });

    $j(window).bind('resize', () => {
      $windowWidth = $j(window).width();

      if ($windowWidth <= 767) {
        // Clean to make sure there is only one more icon.
        $menu.find('.more').remove();

        $menu.children('.menu-item-has-children').each((index, el) => {
          $j(el).children('a').append('<span class="more"></span>').off()
            .on('click', (e) => {
              if ($j(e.target).hasClass('more')) {
                $j(e.currentTarget).parent('.menu-item-has-children').toggleClass('open');
                $j('.main-navigation').toggleClass('sub-open');
                e.stopPropagation();
                e.preventDefault();
                return false;
              }
              return true;
            });
        });
      } else {
        $menu.find('.menu-item-has-children').each((index, el) => {
          $j(el).children('.more').remove();
        });
        $j('.site-header.toggled, body.toggled').removeClass('toggled');
        $j('.main-navigation.active, .menu-toggle.active').removeClass('active');
      }
    }).trigger('resize');

    // Isn't fully loaded, so margin is wrong without timeout.
    setTimeout(() => {
      stickyMenu();
    }, 0);
  });
})(jQuery);
