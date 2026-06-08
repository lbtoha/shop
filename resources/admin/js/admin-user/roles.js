"use strict";
import $ from "jquery";
$(function() {
    // Initially hide all submenus
    $('.submenu').hide();

    // Toggle button click handler
    $('.toggle-btn').on('click', function(e) {
        e.preventDefault();
        const $parentDiv = $(this).closest('.parent-menu').next('.submenu');
        const $icon = $(this).find('svg');

        $parentDiv.slideToggle(300);
        $icon.toggleClass('rotate-180');
    });

    // Parent checkbox click handler
    $('.parent-checkbox').on('change', function() {
        const isChecked = $(this).prop('checked');
        const $parentDiv = $(this).closest('.parent-menu').next('.submenu');

        // Check/uncheck all child checkboxes
        $parentDiv.find('input[type="checkbox"]').prop('checked', isChecked);
    });

    // Child checkbox click handler
    $('.child-checkbox').on('change', function() {
        const parentId = $(this).data('parent');
        const $parentCheckbox = $('#' + parentId);
        const $siblings = $('input[data-parent="' + parentId + '"]');

        // Check if all siblings are checked
        const allChecked = $siblings.length === $siblings.filter(':checked').length;

        // Update parent checkbox accordingly
        $parentCheckbox.prop('checked', allChecked);
    });

    // Prevent level click from triggering toggle
    $('.parent-menu level').on('click', function(e) {
        e.stopPropagation();
    });
});
