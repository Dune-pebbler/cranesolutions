jQuery(document).ready(function ($) {
  initFiltersFromUrl();

  function setLoading(loading) {
    if (loading) {
      $(".products-load-row").addClass("loading");
    } else {
      $(".products-load-row").removeClass("loading");
    }
  }
  
  function getFilterValues() {
    const subcategory = $('input[name="subcategory"]:checked').val();
    const category = $('input[name="category"]:checked').val();
    const brand = $('input[name="brand"]:checked').val();
    const capacity = $('input[name="capacity"]:checked').val();

    const finalSubcategory = !category || category === "" ? "" : subcategory;

    return {
      category: category,
      subcategory: finalSubcategory,
      brand: brand,
      capacity: capacity,
      search: $("#product-search").val(),
      orderby: $("#product-sort").val(),
      min_price: $("#min-price").val(),
      max_price: $("#max-price").val(),
    };
  }

  function createFilterPill(type, id, name) {
    return `
              <button type="button" class="filter-pill" data-filter-type="${type}" data-filter-id="${id}">
                  <span>${name}</span>
                  <span class="remove-icon">×</span>
              </button>
          `;
  }

  function updateFilterPills(values) {
    const $pillsContainer = $("#pills-container");
    const $categoryInput = $('input[name="category"]:checked');
    const $subcategoryInput = $('input[name="subcategory"]:checked');
    const $brandInput = $('input[name="brand"]:checked');
    const $capacityInput = $('input[name="capacity"]:checked');

    // Create a document fragment to batch DOM updates
    const pillsFragment = document.createDocumentFragment();
    
    // Add category pill if exists
    if (values.category) {
      const categoryName = $categoryInput.next("label").text();
      $(createFilterPill("category", values.category, categoryName)).appendTo(pillsFragment);
    }

    // Add subcategory pill if exists and category is selected
    if (values.category && values.subcategory) {
      const subcategoryName = $subcategoryInput.next("label").text();
      $(createFilterPill("subcategory", values.subcategory, subcategoryName)).appendTo(pillsFragment);
    }

    // Add brand pill if exists
    if (values.brand) {
      const brandName = $brandInput.next("label").text();
      $(createFilterPill("brand", values.brand, brandName)).appendTo(pillsFragment);
    }

    // Add capacity pill if exists
    if (values.capacity) {
      const capacityName = $capacityInput.next("label").text();
      $(createFilterPill("capacity", values.capacity, capacityName)).appendTo(pillsFragment);
    }
    
    // Clear existing pills and add new ones all at once
    $pillsContainer.empty().append(pillsFragment);
  }

  function updateProducts(updateUrl = true) {
    const formData = new FormData();
    const values = getFilterValues();
    formData.append("is_shop", true);

    Object.entries(values).forEach(([key, value]) => {
      if (value) {
        formData.append(key, value);
      }
    });

    formData.append("action", "filter_products");
    formData.append("nonce", filterAjax.nonce);

    $.ajax({
      url: filterAjax.ajax_url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          // Collect DOM updates for application in a single batch
          const domUpdates = () => {
            // Update products
            $(".product-archive .row").html(response.data.products);
            
            // Update result count
            $(".results").text(response.data.found_posts);
            
            // Update filter pills
            updateFilterPills(values);
            
            // Update URL if needed
            if (updateUrl) {
              updateUrlParameters(values);
            }
          };
          
          // Apply all DOM updates at once
          requestAnimationFrame(domUpdates);
        }
      },
      complete: function () {
        setLoading(false);
      },
    });
  }

  function updateUrlParameters(values = {}) {
    const url = new URL(window.location);
    const filterParams = [
      "category",
      "subcategory",
      "brand",
      "capacity",
      "search",
      "orderby",
      "min_price",
      "max_price",
    ];

    // Preserve non-filter parameters
    const paramsToPreserve = [];
    for (const [key, value] of url.searchParams.entries()) {
      if (!filterParams.includes(key)) {
        paramsToPreserve.push([key, value]);
      }
    }

    // Clear all search parameters
    url.search = "";

    // Restore non-filter parameters
    paramsToPreserve.forEach(([key, value]) => {
      url.searchParams.set(key, value);
    });

    // Only add filter parameters if values were provided and not empty
    if (Object.keys(values).length > 0) {
      Object.entries(values).forEach(([key, value]) => {
        if (key === "subcategory") {
          if (
            values.category &&
            values.category !== "" &&
            value &&
            value !== ""
          ) {
            url.searchParams.set(key, value);
          }
        } else if (value && value !== "") {
          url.searchParams.set(key, value);
        }
      });
    }

    window.history.pushState({}, "", url);
  }

  function initFiltersFromUrl() {
    const url = new URL(window.location);
    const params = url.searchParams;
    let shouldUpdateProducts = false;
    let hasCategory = params.has("category");
    let hasBrand = params.has("brand");
    let hasCapacity = params.has("capacity");
    let hasSubcategory = params.has("subcategory");

    // Batch DOM updates by collecting them
    let pillsHtml = '';
    let pageTitle = "Product filter";

    if (hasCategory) {
      const categoryId = params.get("category");
      $(`input[name="category"][value="${categoryId}"]`).prop("checked", true);

      const categoryName = $(`input[name="category"][value="${categoryId}"]`)
        .next("label")
        .text();
      
      pillsHtml += createFilterPill("category", categoryId, categoryName);
      pageTitle = categoryName;

      shouldUpdateProducts = true;
    }

    if (hasBrand) {
      const brandId = params.get("brand");
      $(`input[name="brand"][value="${brandId}"]`).prop("checked", true);

      const brandName = $(`input[name="brand"][value="${brandId}"]`)
        .next("label")
        .text();
      
      pillsHtml += createFilterPill("brand", brandId, brandName);

      shouldUpdateProducts = true;
    }

    if (hasCapacity) {
      const capacityId = params.get("capacity");
      $(`input[name="capacity"][value="${capacityId}"]`).prop("checked", true);

      const capacityName = $(`input[name="capacity"][value="${capacityId}"]`)
        .next("label")
        .text();
      
      pillsHtml += createFilterPill("capacity", capacityId, capacityName);

      shouldUpdateProducts = true;
    }

    // Process search, orderby, min_price, max_price params
    if (params.has("search")) {
      $("#product-search").val(params.get("search"));
      shouldUpdateProducts = true;
    }

    if (params.has("orderby")) {
      $("#product-sort").val(params.get("orderby"));
      shouldUpdateProducts = true;
    }

    if (params.has("min_price")) {
      $("#min-price").val(params.get("min_price"));
      shouldUpdateProducts = true;
    }

    if (params.has("max_price")) {
      $("#max-price").val(params.get("max_price"));
      shouldUpdateProducts = true;
    }

    // Apply all collected DOM updates at once
    $("#pills-container").html(pillsHtml);
    $("#page-title").text(pageTitle);

    // Load subcategories if needed and update products
    if (hasCategory) {
      const categoryId = params.get("category");

      // Load subcategories first, then handle the subcategory URL param
      loadSubcategories(categoryId, function (subcategoriesLoaded) {
        if (hasSubcategory && subcategoriesLoaded) {
          const subcategoryId = params.get("subcategory");
          const $subcatInput = $(`input[name="subcategory"][value="${subcategoryId}"]`);

          if ($subcatInput.length) {
            $subcatInput.prop("checked", true);
            
            // Add subcategory pill
            const subcatName = $subcatInput.next("label").text();
            const subcatPill = createFilterPill("subcategory", subcategoryId, subcatName);
            $("#pills-container").append(subcatPill);
          }
        }
        
        // Finally update products if needed
        if (shouldUpdateProducts) {
          updateProducts(false);
        }
      });
    } else if (shouldUpdateProducts) {
      updateProducts(false);
    }
  }

  function loadSubcategories(categoryId, callback) {
    const $subcategoryGroup = $(".subcategory-filter-group");
    const $subcategoryContainer = $("#subcategory-container");
    const $noSubcategoriesMessage = $(".no-subcategories-message");

    $subcategoryContainer
      .find(
        'input[name="subcategory"]:not(#subcat-all), label:not([for="subcat-all"])'
      )
      .not(".no-subcategories-message")
      .remove();

    if (!categoryId) {
      $('.filter-pill[data-filter-type="subcategory"]').remove();
      $(".no-subcategories-message").show();
      if (typeof callback === "function") callback(false);
      return;
    }

    $.ajax({
      url: filterAjax.ajax_url,
      type: "POST",
      data: {
        action: "get_subcategories",
        category_id: categoryId,
        nonce: filterAjax.nonce,
      },
      success: function (response) {
        if (response.success && response.data.subcategories.length > 0) {
          // Create a document fragment for batch insert
          const fragment = document.createDocumentFragment();
          
          response.data.subcategories.forEach(function (subcat) {
            const $subcatElement = $(
              `<input type="radio" name="subcategory" id="subcat-${subcat.term_id}" value="${subcat.term_id}">
               <label for="subcat-${subcat.term_id}">${subcat.name}</label>`
            );
            $(fragment).append($subcatElement);
          });
          
          // Hide message and show group in a batch
          $noSubcategoriesMessage.hide();
          $subcategoryContainer.append(fragment);
          $subcategoryGroup.show();

          // Attach event handlers
          $('input[name="subcategory"]')
            .off("change")
            .on("change", function () {
              const subcategoryId = $(this).val();
              const subcategoryName = $(this).next("label").text();

              // Update UI in a batch
              requestAnimationFrame(() => {
                $('.filter-pill[data-filter-type="subcategory"]').remove();
                
                if (subcategoryId) {
                  $("#pills-container").append(
                    createFilterPill("subcategory", subcategoryId, subcategoryName)
                  );
                }
                
                updateProducts();
              });
            });

          if (typeof callback === "function") callback(true);
        } else {
          // Show message and group in a batch
          requestAnimationFrame(() => {
            $noSubcategoriesMessage.show();
            $("#subcat-all").prop("checked", true);
            $subcategoryGroup.show();
          });

          if (typeof callback === "function") callback(false);
        }
      },
      error: function () {
        $subcategoryGroup.hide();
        if (typeof callback === "function") callback(false);
      },
    });
  }

  // Function to load capacities based on category
  function loadCapacitiesByCategory(categoryId) {
    return new Promise((resolve) => {
      $.ajax({
        url: filterAjax.ajax_url,
        type: "POST",
        data: {
          action: "get_filtered_capacities",
          category_id: categoryId || "",
          nonce: filterAjax.nonce,
        },
        success: function (response) {
          if (response.success) {
            updateCapacitiesInDOM(response.data.capacities);
          }
          resolve();
        },
        error: resolve,
      });
    });
  }

  // Function to update capacities in the DOM
  function updateCapacitiesInDOM(capacities) {
    const $capacityContainer = $(".filter-group .custom-radio").filter(
      function () {
        return $(this).find('input[name="capacity"]').length > 0;
      }
    );

    // Clear existing capacity options except the "all" option
    $capacityContainer
      .find('input[name="capacity"]:not([value=""]), label:not([for="capacity-all"])')
      .remove();

    // Use document fragment for batch DOM manipulation
    if (capacities && capacities.length > 0) {
      const fragment = document.createDocumentFragment();
      
      capacities.forEach(function (capacity) {
        $(
          `<input type="radio" name="capacity" id="capacity-${capacity.term_id}" value="${capacity.term_id}">
           <label for="capacity-${capacity.term_id}">${capacity.name}</label>`
        ).appendTo(fragment);
      });
      
      // Batch DOM update
      $capacityContainer.append(fragment).show();
    } else {
      $capacityContainer.hide();
    }
  }

  function attachBrandChangeHandler() {
    $(document)
      .off("change", 'input[name="brand"]')
      .on("change", 'input[name="brand"]', function () {
        setLoading(true);
        updateProducts();
      });
  }
  attachBrandChangeHandler();

  function attachCapacityChangeHandler() {
    $(document)
      .off("change", 'input[name="capacity"]')
      .on("change", 'input[name="capacity"]', function () {
        setLoading(true);
        updateProducts();
      });
  }
  attachCapacityChangeHandler();

  function loadSubcategoriesByCategory(categoryId) {
    return new Promise((resolve) => {
      loadSubcategories(categoryId, resolve);
    });
  }
  
  function loadBrandsByCategory(categoryId) {
    return new Promise((resolve) => {
      $.ajax({
        url: filterAjax.ajax_url,
        type: "POST",
        data: {
          action: "get_filtered_brands",
          category_id: categoryId || "",
          nonce: filterAjax.nonce,
        },
        success: function (response) {
          if (response.success) {
            updateBrandsInDOM(response.data.brands);
          }
          resolve();
        },
        error: resolve,
      });
    });
  }
  
  function updateBrandsInDOM(brands) {
    const $brandContainer = $(".filter-group .custom-radio").filter(
      function () {
        return $(this).find('input[name="brand"]').length > 0;
      }
    );

    // Clear existing brand options except the "all" option
    $brandContainer
      .find('input[name="brand"]:not([value=""]), label:not([for="brand-all"])')
      .remove();

    // Use document fragment for batch DOM manipulation
    if (brands && brands.length > 0) {
      const fragment = document.createDocumentFragment();
      
      brands.forEach(function (brand) {
        $(
          `<input type="radio" name="brand" id="brand-${brand.term_id}" value="${brand.term_id}">
           <label for="brand-${brand.term_id}">${brand.name}</label>`
        ).appendTo(fragment);
      });
      
      // Batch DOM update
      $brandContainer.append(fragment).show();
    } else {
      $brandContainer.hide();
    }
  }
  
  function updatePageTitle(categoryName) {
    if (categoryName && categoryName !== "") {
      $("#page-title").text(categoryName);
    } else {
      $("#page-title").text("Product filter");
    }
  }

  //events
  $('input[name="category"]').on("change", function () {
    const categoryId = $(this).val();
    const categoryName = $(this).next("label").text();

    setLoading(true);
    updatePageTitle(categoryName);

    if (categoryId) {
      $('input[name="subcategory"][value=""]').prop("checked", true);

      // Load all filters in parallel, then update UI in one batch
      Promise.all([
        loadSubcategoriesByCategory(categoryId),
        loadBrandsByCategory(categoryId),
        loadCapacitiesByCategory(categoryId)
      ]).then(() => {
        updateProducts();
      });
    } else {
      updateProducts();
    }
  });
  
  $("#pills-container").on("click", ".filter-pill", function () {
    const $pill = $(this);
    const filterType = $pill.data("filter-type");

    setLoading(true);

    // Batch UI updates
    const updateUI = () => {
      if (filterType === "category") {
        $('input[name="category"][value=""]').prop("checked", true);
        
        $('input[name="brand"]').prop("checked", false);
        $('input[name="brand"][value=""]').prop("checked", true);
        
        $('input[name="capacity"]').prop("checked", false);
        $('input[name="capacity"][value=""]').prop("checked", true);

        $('input[name="subcategory"]').prop("checked", false);
        $('input[name="subcategory"][value=""]').prop("checked", true);

        $("#page-title").text("Products");
        $pill.remove();

        // Load all filters in parallel
        Promise.all([
          loadBrandsByCategory(),
          loadCapacitiesByCategory(),
          new Promise(resolve => {
            loadSubcategories('', resolve);
          })
        ]).then(() => {
          updateProducts();
        });
        return;
      } else if (filterType === "subcategory") {
        const $subcatAll = $('#subcat-all');
        
        if ($subcatAll.length) {
          $subcatAll.prop("checked", true);
        } else {
          $('input[name="subcategory"]').prop("checked", false);
        }
      } else if (filterType === "brand") {
        $('input[name="brand"][value=""]').prop("checked", true);
      } else if (filterType === "capacity") {
        $('input[name="capacity"][value=""]').prop("checked", true);
      }

      $pill.remove();
      updateProducts();
    };

    // Use requestAnimationFrame for smoother UI updates
    requestAnimationFrame(updateUI);
  });
  
  $("#product-sort").on("change", updateProducts);
  
  let timeout;
  $("#product-search").on("input", function () {
    clearTimeout(timeout);
    timeout = setTimeout(updateProducts, 500);
  });
  
  window.addEventListener("popstate", function () {
    initFiltersFromUrl();
  });
});