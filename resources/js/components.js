document.addEventListener("DOMContentLoaded", function() {
    const selectAllButtons = document.getElementsByClassName('select-all-button');
    for (let i = 0, len = selectAllButtons.length | 0; i < len; i = i + 1) {
        selectAllButtons[i].onclick = selectAll;
    }
});

function selectAll(event) {
    let button = event.currentTarget;
    const selector = button.dataset.target;
    const action = button.dataset.action;
    if (selector) {
        if (action === "select") {
            document.querySelectorAll(selector).forEach(input => {
                input.setAttribute('checked', 'checked');
            });
        } else if (action === "unselect") {
            document.querySelectorAll(selector).forEach(input => {
                input.removeAttribute('checked');
            });
        }
    }
}
