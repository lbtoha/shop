import "nestable/jquery.nestable"
function parseList(list) {
    const items = [];
    list.querySelectorAll(':scope > .dd-item').forEach(item => {
        const id = item.getAttribute('data-id');
        const text = item.querySelector('.dd-handle').textContent.trim();
        const childList = item.querySelector(':scope > .dd-list');

        const itemData = { id, text };

        // Recursively parse child list if it exists
        if (childList) {
            itemData.children = parseList(childList);
        }

        items.push(itemData);
    });
    return items;
}


function updateOutput(e) {
    const nestable = document.querySelector('#nestable .dd-list');

    // Parse the list into JSON
    const jsonData = parseList(nestable);
    $('#nestable-output').val(JSON.stringify(jsonData))

}
// activate Nestable for list 1
$('#nestable').nestable({
    group: 1
}).on('change', updateOutput);



