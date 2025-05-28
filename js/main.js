jQuery(document).ready(function () {
  //startOwlSlider();
  setHamburgerOnClick();
  wrapIframe();
  replaceImgWithSvg();
  setAlignInATag();
  setOnSearch();
  setReviewSlider();
  setOnFaqItemClickedListener();
  setSearchTrigger();
  setFancybox();
  setOnQuickStartPosition();
  setEqualHeights("h3");
  setEqualHeights("p");
  setDynamicNavigationHeight();
  setDynamicNavigationPosition();
  setOnBtnAjaxFilter(jQuery);

  //woocomerce thumbnail sliders
  initializeSlider();
  initializeProductImageSlider();
  initializeProductImageThumbnailSlider();
  connectSliders();
});

jQuery(window).scroll(function () {
  setOnHeaderClass();
});

jQuery(window).resize(function () {
  setOnQuickStartPosition();
  setEqualHeights("h3");
  setEqualHeights("p");
  setDynamicNavigationHeight();
  setDynamicNavigationPosition();
});

// functions
function setDynamicNavigationHeight() {
  jQuery(".navigation .sub-menu").removeAttr("style");

  jQuery(".navigation > .menu > li >.sub-menu").each(function () {
    let height = jQuery(this).outerHeight();

    jQuery(this).parent().css("--submenu-height-in-px", `${height}px`);
  });
}
function setOnQuickStartPosition() {
  let quickStartContentHeight = jQuery(
    ".quick-start-item .content"
  ).outerHeight();

  jQuery(".quick-start-item").css({
    "--content-box-height-in-px": `-${quickStartContentHeight}px`,
  });
}
function setDynamicNavigationPosition() {
  let headerHeight =
    jQuery("header").outerHeight() +
    jQuery("header").offset().top -
    jQuery(window).scrollTop();

  jQuery(".navigation").css("--header-height-in-px", `${headerHeight}px`);
}
function setOnQuickStartPosition() {
  let quickStartContentHeight = jQuery(
    ".quick-start-item .content"
  ).outerHeight();

  jQuery(".quick-start-item").css({
    "--content-box-height-in-px": `-${quickStartContentHeight}px`,
  });
}
function setEqualHeights(nodeName) {
  let maxHeight = 0;
  let lastQuery = false;
  let savedQueries = [];
  let containerQuery = jQuery(".news-item");
  // reset first.
  containerQuery.find(nodeName).removeAttr("style").removeClass("is-resized");

  // we loop through all the titels.
  containerQuery.find(nodeName).each(function () {
    let query = jQuery(this);

    // we check if we have to reset.
    if (lastQuery) {
      let isAnotherLevel = lastQuery.offset().top != query.offset().top;
      if (isAnotherLevel) {
        // we can set the height
        for (var i = 0; i < savedQueries.length; i++) {
          savedQueries[i].addClass("is-resized").css({ height: maxHeight });
        }
        maxHeight = 0;
        savedQueries = [];
      }
    }

    // set the max height.
    maxHeight =
      query.outerHeight() > maxHeight ? query.outerHeight() : maxHeight;

    lastQuery = query;
    savedQueries.push(query);
  });

  containerQuery
    .find(nodeName)
    .not(".is-resized")
    .addClass("is-resized")
    .css({ height: maxHeight });
}
function setFancybox() {
  jQuery(".img-modal").each(function () {
    jQuery(this).attr("data-fancybox", "");
  });
}
function setSearchTrigger() {
  jQuery(".search-trigger").on("click", function (event) {
    event.preventDefault();

    jQuery(".search-bar form button").click();
  });
}
function setOnFaqItemClickedListener() {
  jQuery(".faq-item .header").on("click", function () {
    let containerQuery = jQuery(this).parents("li");

    // we toggle the p tag.
    containerQuery.find(".content").stop().slideToggle();
    containerQuery.toggleClass("is-active");
  });
}
function setReviewSlider() {
  jQuery(".review-slider").owlCarousel({
    items: 1,
    nav: true,
    dots: false,
    navText: [arrowIconContent, arrowIconContent],
  });

  jQuery(".partner-slider").owlCarousel({
    items: 1,
    nav: true,
    dots: false,
    margin: 50,
    responsive: {
      768: {
        nav: true,
        items: 2,
      },
      1199: {
        items: 4,
      },
    },
    // navText: [arrowIconContent, arrowIconContent]
  });
}
function wrapIframe() {
  jQuery(".page-content iframe").wrap("<div class='embed-container'></div>");
}
function setOnSearch() {
  jQuery(".launch-search, .btn-search-close").on("click", function (e) {
    e.preventDefault();

    jQuery(".search-screen").toggleClass("active");
  });
}
function setOnHeaderClass() {
  var togglePosition = 10;
  var currentPosition = jQuery(window).scrollTop();
  if (currentPosition > togglePosition) {
    jQuery("header").addClass("scrolled");
    jQuery("main").addClass("scrolled");
  } else {
    jQuery("header").removeClass("scrolled");
    jQuery("main").removeClass("scrolled");
  }
}
function startOwlSlider() {
  jQuery("section.slider").owlCarousel({
    items: 1,
    nav: false,
    dots: false,
  });
}

function replaceImgWithSvg() {
  jQuery("img.svg").each(function () {
    var $img = jQuery(this);
    var imgID = $img.attr("id");
    var imgClass = $img.attr("class");
    var imgURL = $img.attr("src");
    jQuery.get(
      imgURL,
      function (data) {
        var $svg = jQuery(data).find("svg");
        if (typeof imgID !== "undefined") {
          $svg = $svg.attr("id", imgID);
        }
        if (typeof imgClass !== "undefined") {
          $svg = $svg.attr("class", imgClass + " replaced-svg");
        }
        $svg = $svg.removeAttr("xmlns:a");
        if (
          !$svg.attr("viewBox") &&
          $svg.attr("height") &&
          $svg.attr("width")
        ) {
          $svg.attr(
            "viewBox",
            "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
          );
        }
        $img.replaceWith($svg);
      },
      "xml"
    );
  });
}

function setHamburgerOnClick() {
  let isTrigger = false;

  jQuery(document.body).on("click", function (event) {
    if (isTrigger) return;
    let targetQuery = jQuery(event.target);

    if (targetQuery.parents(".navigation").length == 1) return;
    if (targetQuery.hasClass("hamburger") || targetQuery.hasClass("navigation"))
      return;
    if (!jQuery(".hamburger").hasClass("is-active")) return;

    isTrigger = true;
    // jQuery('.hamburger').click();
    console.log(isTrigger, targetQuery);
  });

  jQuery(".hamburger").on("click", function () {
    if (jQuery(this).hasClass("is-active")) {
      jQuery(this).removeClass("is-active");
    } else {
      jQuery(this).addClass("is-active");
    }
    jQuery(".navigation").toggleClass("is-active");
    jQuery("html").toggleClass("is-active");

    isTrigger = false;
  });
}

function setAlignInATag() {
  jQuery("img[class*=align]").each(function (i, e) {
    jQuery(e).parents("a").addClass(jQuery(e).attr("class"));
  });
}

function setOnRecordView() {
  inView(".should-animate").on("enter", function (element) {
    jQuery(element)
      .addClass("animate__animated")
      .removeClass("remove__animate");
  });
}

function _fetch(options) {
  return jQuery.ajax({
    url: ajaxurl,
    dataType: "json",
    data: options,
    method: "POST",
  });
}

//not woocomerce
function setOnBtnAjaxFilter($) {
  $(".filter-btn").on("click", function () {
    $(".filters ul li").removeClass("is-active");
    $(this).closest("li").addClass("is-active");
    $("#project-container").addClass('fade-out');
    jQuery("#loader").show();

    var sector = $(this).data("filter");
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "filter_projects",
        sector
      },
      success: function (response) {
        jQuery("#loader").hide();
        $("#project-container").html(response);
        jQuery("#project-container").removeClass('fade-out');
        jQuery("#project-container").css("opcacity", 1);

      },
    });
  });
}

//plus minus buttons
jQuery(document).ready(function($) {
  // Handle plus/minus buttons
  jQuery('.quantity-control .quantity-button').on('click', function() {
    var $input = jQuery(this).siblings('.quantity-input');
    var currentValue = parseInt($input.val()) || 1;
    
    if (jQuery(this).hasClass('plus')) {
      $input.val(currentValue + 1);
      $input.attr('value', currentValue + 1);
    } else if (jQuery(this).hasClass('minus') && currentValue > 1) {
      $input.val(currentValue - 1);
      $input.attr('value', currentValue - 1);
    }
    // Update add-to-cart link URL with new quantity
    updateAddToCartUrl($input);
    // Trigger change event to notify any listeners
    $input.trigger('change');
  });
  
  // Also listen for manual changes to the input field
  jQuery('.quantity-input').on('change', function() {
    updateAddToCartUrl(jQuery(this));
  });
  
  // Function to update the add-to-cart URL
  function updateAddToCartUrl($input) {
    var quantity = parseInt($input.val()) || 1;
    var $row = $input.closest('tr');
    var $addToCartButton = $row.find('.add-to-cart-button');
    
    if ($addToCartButton.length) {
      var currentUrl = $addToCartButton.attr('href');
      
      // Update the quantity parameter in the URL
      var newUrl = currentUrl.replace(/quantity=\d+/, 'quantity=' + quantity);
      
      // If quantity parameter doesn't exist, add it
      if (newUrl === currentUrl) {
        var separator = newUrl.indexOf('?') !== -1 ? '&' : '?';
        newUrl += separator + 'quantity=' + quantity;
      }
      
      $addToCartButton.attr('href', newUrl);
    }
  }
  
  // Initialize all URLs with correct initial quantities
  jQuery('.quantity-input').each(function() {
    updateAddToCartUrl(jQuery(this));
  });
});


//woocomerce product image sliders 
function initializeSlider(){
  jQuery('.slider').owlCarousel({
    items: 1,
    autoplay: true,
    loop: true,
    autoplayTimeout: 7500,
    autoplayHoverPause: true,
  });
}
function initializeProductImageSlider(){
  jQuery('.product-image-slider').owlCarousel({
    items: 1,
    autoplay: false,
    loop: true,
    dots: true,  
    autoHeight: true,
  });
}
function initializeProductImageThumbnailSlider(){
  jQuery('.product-thumbnail-slider').owlCarousel({
    items: 5,
    autoplay: true,
    loop: false,
    margin: 10
  });
}
function connectSliders(){
  let mainSlider = jQuery('.product-image-slider');
  let thumbnailSlider = jQuery('.product-thumbnail-slider');
  
  mainSlider.on('translated.owl.carousel', function (event) {
    thumbnailSlider.find('.slide').removeClass("active");
    let c = mainSlider.find(".owl-item.active").index();
    thumbnailSlider.find('.slide').eq(c).addClass("active");
    let d = Math.ceil((c + 1) / (4)) - 1;
    thumbnailSlider.find(".owl-dots .owl-dot").eq(d).trigger('click');
  });
  
  thumbnailSlider.find('.slide').click(function () {
    let b = jQuery(this).parent().index();
    console.log(b);
    mainSlider.find(".owl-dots .owl-dot").eq(b).trigger('click');
    
    thumbnailSlider.find('slide').removeClass("active");
    
    jQuery(this).addClass("active");
  });
}