function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("shown"));
    document.getElementById(formId).classList.add("shown");
}