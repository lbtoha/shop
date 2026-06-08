// iconPicker.js
import tippy from 'tippy.js';

document.addEventListener('DOMContentLoaded', async () => {
    const elements = document.querySelectorAll('.icon-picker');

    // Fetch icons from Phosphor Icons CSS
    const icons = await fetchIcons();

    elements.forEach((element) => {
        const input = element.querySelector('input');
        const iconPickerModal = element.querySelector('.icon-picker-modal');
        const iconList = iconPickerModal.querySelector('.icon-list');

        iconSet(icons, input, iconList);

        // Initialize Tippy.js
        const tippyInstance = tippy(input, {
            content: iconPickerModal,
            trigger: 'click',
            interactive: true,
            placement: 'bottom',
            arrow: false,
            maxWidth: '300px',
            offset: [0, -30],
            theme: 'light',
            appendTo: () => document.body,
            onShow(instance) {
                iconPickerModal.style.display = 'block';
            },
            onHide(instance) {
                iconPickerModal.style.display = 'none';
            },
        });

        // Search icons on input keyup
        input.addEventListener('keyup', (event) => {
            const searchQuery = event.target.value.toLowerCase();
            const filteredIcons = icons.filter((icon) => icon.toLowerCase().includes(searchQuery));
            iconList.innerHTML = '';
            iconSet(filteredIcons, input, iconList);
            tippyInstance.setContent(iconPickerModal);
        });
    });
});

function iconSet(icons, input, iconList) {
    iconList.innerHTML = '';

    icons.forEach((icon) => {
        const iconElement = document.createElement('i');
        iconElement.className = `ph ${icon}`;
        iconElement.style.fontSize = '24px';
        iconElement.style.cursor = 'pointer';
        iconElement.style.margin = '5px';

        iconElement.addEventListener('click', () => {
            input.value = icon;
            input._tippy.hide();
        });

        iconList.appendChild(iconElement);
    });
}

async function fetchIcons() {

    const icons = [];

    try {
        const response = await fetch('https://unpkg.com/@phosphor-icons/web/src/regular/style.css');

        const cssText = await response.text();

        const iconRegex = /\.ph-([a-z0-9-]+):before/g;
        let match;

        while ((match = iconRegex.exec(cssText)) !== null) {
            icons.push(`ph ph-${match[1]}`);
            icons.push(`ph-fill ph-${match[1]}`);
        }
    } catch (error) {

    }

    return icons;
}
