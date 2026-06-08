"use strict";

import $ from "jquery";

export const closeModal = () => {
    const modals = $("[data-modal-close]");

    if (modals.length) {
        modals.trigger("click");
    }
};


export default class Modal {
    static zIndexCounter = 100; // Starting z-index (high enough to be above most elements)
    static openModals = new Set(); // Track open modal instances
    static overlay = null; // Shared overlay for all modals
    static currentTopZIndex = 100; // Track the current top z-index
    constructor(buttonEl, options = {}) {
        this.id = buttonEl.dataset.modalTarget;
        this.buttonEl = buttonEl;
        this.modal = document.getElementById(this.id);
        if (!this.modal) {
            console.warn(`Modal: Element with ID "${this.id}" not found.`);
            return;
        }
        this.options = options;
        this.isOpen = false;
        this.componentType = 'modal';
        this.zIndex = Modal.zIndexCounter; // Will be updated when opened
        this.overlayClose = this.modal.dataset.overlayClose !== 'false'; // Default true unless explicitly false
        this.init();
    }
    init() {
        if (!this.modal) return;
        this.createSharedOverlay();
        this.addEventListeners();
        this.moveToBody();
    }
    createSharedOverlay() {
        if (Modal.overlay) return;
        Modal.overlay = document.createElement('div');
        Modal.overlay.classList.add('modal-overlay');
        Modal.overlay.style.position = 'fixed';
        Modal.overlay.style.top = '0';
        Modal.overlay.style.left = '0';
        Modal.overlay.style.right = '0';
        Modal.overlay.style.bottom = '0';
        Modal.overlay.style.backgroundColor = 'black';
        Modal.overlay.style.opacity = '0.8';
        Modal.overlay.style.zIndex = '99'; // Base z-index below modals
        Modal.overlay.style.transition = 'opacity 300ms ease-in-out';
        Modal.overlay.style.display = 'none';
        document.body.appendChild(Modal.overlay);
    }
    addEventListeners() {
        const trigger = this.buttonEl;
        const closeBtns = this.modal.querySelectorAll(`[data-${this.componentType}-close="${this.id}"]`);
        const panel = this.modal.querySelector('.panel');
        if (!trigger || !closeBtns || !panel) {
            console.warn(`Modal: Missing essential elements for ID "${this.id}"`);
            return;
        }
        trigger.addEventListener('click', () => this.open());
        this.modal.style.display = 'none';
        // Close buttons inside the modal
        closeBtns.forEach((btn) => btn.addEventListener('click', () => this.close()));
        // Close the modal when clicking outside the panel (only if overlayClose is true)
        this.modal.addEventListener('click', (e) => {
            if (!panel.contains(e.target) && this.overlayClose) {
                this.close();
            }
        });
    }
    open() {
        if (!this.modal || this.isOpen) return;
        // Update z-index values
        Modal.currentTopZIndex += 2; // Increment by 2 to leave room for overlay
        this.zIndex = Modal.currentTopZIndex;
        this.isOpen = true;
        Modal.openModals.add(this);
        // Set z-index for modal and overlay
        this.modal.style.zIndex = this.zIndex;
        Modal.overlay.style.zIndex = this.zIndex - 1; // Keep overlay just below
        // Show overlay if not already visible
        Modal.overlay.style.display = 'block';
        // Apply opening animation
        this.modal.style.transition = 'opacity 300ms ease, transform 300ms ease';
        this.modal.style.opacity = '0';
        this.modal.style.transform = 'translateY(-10%)';
        this.modal.style.display = 'block';
        setTimeout(() => {
            this.modal.style.opacity = '1';
            this.modal.style.transform = 'translateY(0)';
        }, 10);
        document.body.style.overflow = 'hidden';
    }
    close() {
        if (!this.modal || !this.isOpen) return;
        this.isOpen = false;
        // Apply closing animation
        this.modal.style.opacity = '0';
        this.modal.style.transform = 'translateY(-10%)';
        setTimeout(() => {
            Modal.openModals.delete(this);
            this.modal.style.display = 'none';
            // If this was the top modal, find the new top modal
            if (Modal.openModals.size > 0) {
                const modalsArray = Array.from(Modal.openModals);
                const newTopModal = modalsArray[modalsArray.length - 1];
                Modal.currentTopZIndex = newTopModal.zIndex;
                // Update overlay to be just below the new top modal
                Modal.overlay.style.zIndex = newTopModal.zIndex - 1;
            } else {
                // No modals left open
                Modal.overlay.style.display = 'none';
                Modal.currentTopZIndex = Modal.zIndexCounter; // Reset to base
                document.body.style.overflow = '';
            }
        }, 300);
    }
    moveToBody() {
        document.body.prepend(this.modal);
    }
    /**
     * Static method to initialize all modals on the page
     * @param {string} [selector='[data-modal-target]'] - Selector for modal triggers
     * @returns {Array} Array of initialized Modal instances
     */
    static start(selector = '[data-modal-target]') {
        const triggers = document.querySelectorAll(selector);
        if(!triggers.length) {
            return;
        }
        return Array.from(triggers).map(trigger => new Modal(trigger));
    }
}

