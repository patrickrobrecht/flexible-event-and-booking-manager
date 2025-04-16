document.addEventListener('DOMContentLoaded', () => {
    initCheckboxes();

    document.addEventListener("productPropertyAdded", () => {
        requestAnimationFrame(() => {
            initCheckboxes();
        });
    });
});

function initCheckboxes() {
    const checkboxes = document.getElementsByClassName('button-checkbox');
    for (let i = 0, len = checkboxes.length | 0; i < len; i = i + 1) {
        if (checkboxes[i].checked) {
            checkboxes[i].click();
        }
        checkboxes[i].onchange = handleCheckBoxChange;
    }
}

function handleCheckBoxChange(event) {
    const checkbox = event.target;
    const containerId = checkbox.getAttribute('data-target');
    const container = document.getElementById(containerId);
    if (container) {
        if (checkbox.checked) {
            container.classList.remove('disabled');
            container.removeAttribute('aria-disabled');
            container.removeAttribute('disabled');
        } else {
            container.classList.add('disabled');
            container.setAttribute('aria-disabled', 'true');
            container.setAttribute('disabled', 'true');
        }
    }
}
