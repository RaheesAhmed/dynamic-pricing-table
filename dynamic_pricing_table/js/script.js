jQuery(document).ready(function ($) {
  var table_counter = 0;
  var templates = dynamicPricingTableData.templates;

  function addPricingTable(tableData = null) {
    var table_id = table_counter;
    var tableHtml = `
            <div class="pricing-table" data-table-id="${table_id}">
                <div class="pricing-table-header">
                    <h3>Pricing Table ${table_id + 1}</h3>
                    <div class="pricing-table-actions">
                        <button type="button" class="collapse-table" title="Collapse/Expand"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
                        <button type="button" class="edit-table" title="Edit"><span class="dashicons dashicons-edit"></span></button>
                        <button type="button" class="delete-table" title="Delete"><span class="dashicons dashicons-trash"></span></button>
                    </div>
                </div>
                <div class="pricing-table-content">
                    <input type="text" name="dynamic_pricing_table_data[${table_id}][title]" placeholder="Table Title" value="${
      tableData ? tableData.title : ""
    }" />
                    <div class="pricing-tiers">
                        <!-- Pricing tiers will be dynamically added here -->
                    </div>
                    <button type="button" class="add-pricing-tier button">
                        <i class="dashicons dashicons-plus-alt"></i> Add Pricing Tier
                    </button>
                    <div class="styling-options">
                        <h4>Styling Options</h4>
                        <div class="styling-option">
                            <label>Background Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][bg_color]" value="${
      tableData ? tableData.bg_color : "#ffffff"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Text Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][text_color]" value="${
      tableData ? tableData.text_color : "#000000"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Border Radius:</label>
                            <input type="range" name="dynamic_pricing_table_data[${table_id}][border_radius]" min="0" max="50" value="${
      tableData ? tableData.border_radius : "0"
    }" />
                            <span class="border-radius-value">${
                              tableData ? tableData.border_radius : "0"
                            }px</span>
                        </div>
                        <div class="styling-option">
                            <label>Tier Background Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][tier_bg_color]" value="${
      tableData ? tableData.tier_bg_color : "#f8f9fa"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Tier Text Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][tier_text_color]" value="${
      tableData ? tableData.tier_text_color : "#212529"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Button Background Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][button_bg_color]" value="${
      tableData ? tableData.button_bg_color : "#007bff"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Button Text Color:</label>
                            <input type="text" class="color-picker" name="dynamic_pricing_table_data[${table_id}][button_text_color]" value="${
      tableData ? tableData.button_text_color : "#ffffff"
    }" />
                        </div>
                        <div class="styling-option">
                            <label>Display Style:</label>
                            <select name="dynamic_pricing_table_data[${table_id}][display_style]">
                                <option value="column" ${
                                  tableData &&
                                  tableData.display_style === "column"
                                    ? "selected"
                                    : ""
                                }>Column</option>
                                <option value="vertical" ${
                                  tableData &&
                                  tableData.display_style === "vertical"
                                    ? "selected"
                                    : ""
                                }>Vertical</option>
                            </select>
                        </div>
                    </div>
                    <div class="shortcode-display">
                        Shortcode: [dynamic_pricing_table id="${table_id}"]
                    </div>
                </div>
            </div>
        `;
    $("#pricing-tables-container").append(tableHtml);

    if (tableData && tableData.tiers) {
      tableData.tiers.forEach(function (tierData) {
        addPricingTier(table_id, tierData);
      });
    }

    initializeColorPickers();
    initializeRangeInputs();

    table_counter++;
  }

  function addPricingTier(table_id, tierData = null) {
    var tier_id = $(
      '.pricing-table[data-table-id="' + table_id + '"] .pricing-tier'
    ).length;
    var tierHtml = `
            <div class="pricing-tier" data-tier-id="${tier_id}">
                <input type="text" name="dynamic_pricing_table_data[${table_id}][tiers][${tier_id}][name]" placeholder="Tier Name" value="${
      tierData ? tierData.name : ""
    }" />
                <input type="text" name="dynamic_pricing_table_data[${table_id}][tiers][${tier_id}][price]" placeholder="Price" value="${
      tierData ? tierData.price : ""
    }" />
                <textarea name="dynamic_pricing_table_data[${table_id}][tiers][${tier_id}][features]" placeholder="Features (one per line)">${
      tierData ? tierData.features : ""
    }</textarea>
                <input type="text" name="dynamic_pricing_table_data[${table_id}][tiers][${tier_id}][button_text]" placeholder="Button Text" value="${
      tierData ? tierData.button_text : "Sign Up"
    }" />
                <button type="button" class="remove-pricing-tier button">
                    <i class="dashicons dashicons-trash"></i> Remove Tier
                </button>
            </div>
        `;
    $('.pricing-table[data-table-id="' + table_id + '"] .pricing-tiers').append(
      tierHtml
    );
  }

  function initializeColorPickers() {
    $(".color-picker").wpColorPicker();
  }

  function initializeRangeInputs() {
    $('input[type="range"]').on("input", function () {
      $(this)
        .next(".border-radius-value")
        .text($(this).val() + "px");
    });
  }

  $("#add-pricing-table").on("click", function () {
    addPricingTable();
  });

  $(document).on("click", ".add-pricing-tier", function () {
    var table_id = $(this).closest(".pricing-table").data("table-id");
    addPricingTier(table_id);
  });

  $(document).on("click", ".remove-pricing-tier", function () {
    $(this).closest(".pricing-tier").remove();
  });

  $(document).on("click", ".delete-table", function () {
    if (confirm("Are you sure you want to delete this pricing table?")) {
      $(this).closest(".pricing-table").remove();
    }
  });

  $(document).on("click", ".collapse-table", function () {
    var $table = $(this).closest(".pricing-table");
    $table.toggleClass("collapsed");
    $(this)
      .find(".dashicons")
      .toggleClass("dashicons-arrow-up-alt2 dashicons-arrow-down-alt2");
  });

  $("#create-from-template").on("click", function () {
    var templateName = $("#template-select").val();
    if (templateName) {
      var template = templates[templateName];
      addPricingTable(template);
    } else {
      alert("Please select a template first.");
    }
  });

  // Load existing data
  if (
    typeof dynamicPricingTableData !== "undefined" &&
    dynamicPricingTableData.existing_data
  ) {
    $.each(
      dynamicPricingTableData.existing_data,
      function (table_id, tableData) {
        addPricingTable(tableData);
      }
    );
  }
});
