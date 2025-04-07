document.addEventListener("DOMContentLoaded", function () {
    const abilityCheckboxes = document.querySelectorAll('input[name="abilities[]"]');

    checkAllDependencies();
    abilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", checkAllDependencies);
    });

    function checkAllDependencies() {
        abilityCheckboxes.forEach(checkbox => {
            checkbox.disabled = hasUncheckedDependencies(checkbox);
            if (checkbox.disabled) {
                checkbox.checked = false;
            }
        });
    }

    function hasUncheckedDependencies(checkbox) {
        const dependentOfCheckboxId = checkbox.getAttribute("data-depends-on-id");
        if (!dependentOfCheckboxId) {
            return false;
        }

        const dependentOfCheckbox = document.getElementById(dependentOfCheckboxId);
        return !dependentOfCheckbox.checked || hasUncheckedDependencies(dependentOfCheckbox);
    }
});
