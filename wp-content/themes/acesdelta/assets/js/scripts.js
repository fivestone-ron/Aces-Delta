import $ from 'jquery'; // eslint-disable-line

(($j) => {
  // function accordion() {
  //   $j('.accordion-title').on('click', (e) => {
  //     e.preventDefault();
  //     const ctn = $j(this).parents('.container-btn');
  //     ctn.find('.accordion-title').toggleClass('expanded');
  //     ctn.find('.content').slideToggle(500, 'easeInOutExpo');
  //   });
  // }

  function isEmail(email) {
    const regex = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  function brochureFormStep() {
    $j('.brochure-form .wp-block-button a').click(() => {
      const email = $j('.form-step-1 input').val();

      if (isEmail(email)) {
        $j('.form-step-1').addClass('-hidden');
        $j('.form-step-2').removeClass('-hidden');
        $j('.form-step-2 .ginput_container_email input').val(email);
      } else {
        $j('.form-step-1').addClass('invalid');
      }
    });
  }

  // Check if a p tag has only a tag to add the arrow to the link.
  function checkIfOnlyAInP() {
    $j('.basic-content-section').each((index, el) => {
      $j(el).find('p').each((i, item) => {
        if ($j(item).children().length === 1) {
          if ($j(item).html().startsWith('<a')) {
            $j(item).children().addClass('red-link');
          }
        }
      });
    });
  }

  function toggleSearchForm() {
    $j('.js-search__icon').on('click', () => {
      const $searchForm = $j('.js-search__form');

      if (!$searchForm.hasClass('toggled')) {
        $searchForm.addClass('toggled');
        $j('.search-field').focus();
      } else {
        $searchForm.removeClass('toggled');
      }
    });
  }

  function openResourceBlockVideoModal() {
    $j('.js-video-link, .category-videos img').on('click', (e) => {
      $j('body').addClass('fixed');
      $j(e.currentTarget).closest('li, .search__post').find('.js-video-modal').addClass('open');

      setTimeout(() => {
        const $iframe = $j(e.currentTarget).closest('li, .search__post').find('iframe');
        let src = $iframe.attr('src').replace(/&autoplay=\d/, '');
        src += '&autoplay=1';
        $iframe.attr('src', src);
      }, 100);
    });
  }

  function openColumnVideoModal() {
    $j('.js-has-video a').on('click', (e) => {
      $j('body').addClass('fixed');
      $j('.js-site-header').css('z-index', 1);
      $j(e.currentTarget).closest('.js-has-video').find('.js-video-modal').addClass('open');

      setTimeout(() => {
        const $iframe = $j(e.currentTarget).closest('.js-has-video').find('iframe');
        let src = $iframe.attr('src').replace(/&autoplay=\d/, '');
        src += '&autoplay=1';
        $iframe.attr('src', src);
      }, 100);
    });
  }

  function equalHeight(item) {
    let maxHeight = 0;
    return $j(item).each((index, el) => {
      const boxHeight = $j(el).height();
      maxHeight = Math.max(maxHeight, boxHeight);
    }).height(maxHeight);
  }

  function matchTitleHeightSearchPage() {
    $j('.search__article-row').each((index, el) => {
      equalHeight($j(el).find('.entry-title'));

      $j(window).on('resize', () => {
        $j($j(el).find('.entry-title')).css('height', 'auto');
        equalHeight($j(el).find('.entry-title'));
      });
    });
  }

  function matchTitleHeightMasteringBlock() {
    equalHeight($j('.js-icons-block h3'));

    $j(window).on('resize', () => {
      $j($j('.js-icons-block h3')).css('height', 'auto');
      equalHeight($j('.js-icons-block h3'));
    });
  }

  function onSuccesSubmit() {
    $j(document).on('gform_confirmation_loaded', () => {
      $j('.contact-form .form-title').hide();
    });
  }

  $j(document).ready(() => {
    // accordion();
    checkIfOnlyAInP();
    brochureFormStep();
    toggleSearchForm();
    openResourceBlockVideoModal();
    openColumnVideoModal();
    matchTitleHeightSearchPage();
    matchTitleHeightMasteringBlock();
    onSuccesSubmit();
  });
})(jQuery);
