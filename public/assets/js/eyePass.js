const togglePassword = document.querySelectorAll(".eye");
const activeClassName = "is-active";

togglePassword.forEach((item) => {
  item.addEventListener("click", handleTogglePass);
});

function handleTogglePass() {
  let inputType = "password";
  // const input = this.parentNode?.firstElementChild.nextElementSibling;
  const input = this.parentNode?.querySelector(".form-group input");
  console.log(this.parentNode);

  if (this.matches(".eye-close")) {
    inputType = "text";
    const eyeOpen = this.previousElementSibling;
    eyeOpen && eyeOpen.classList.add(activeClassName);
  } else {
    inputType = "password";
    this.classList.remove(activeClassName);
  }

  input && input.setAttribute("type", inputType);
}
