/* ==========================================================================
   Global variables
   ========================================================================== */

/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  urlParam = getURLParam();
  id = "id" in urlParam ? urlParam["id"] : id;
  recoveryLink = "key" in urlParam ? urlParam["key"] : key;
  startResetButton();
}

// Validate input fields

const passwordInputs = document.querySelectorAll("input");
passwordInputs.forEach(input => {
  input.addEventListener("blur", checkFields);
});

function checkFields(evt) {
  //console.log(evt.target.name);
  if (evt.target.value.length == 0 || !evt.target.value) {
    document.querySelector(".error-message").textContent =
      "add a " + evt.target.name;
    evt.target.classList.add("error");
    return false;
  }
  if (evt.target.value.length < 6) {
    document.querySelector(".error-message").textContent =
      evt.target.name + " is too short";
    evt.target.classList.add("error");
    return false;
  }

  document.querySelector(".error-message").textContent = "";
  evt.target.classList.remove("error");
  return true;
}

function startResetButton() {
  const resetButton = document.querySelector("form button");

  resetButton.addEventListener("click", resetPassword);
}

function checkPasswordMatch(newPassword, confirmPassword) {
  if (newPassword != "" && confirmPassword != "") {
    if (newPassword != confirmPassword) {
      document.querySelector(".error-message").textContent =
        "Passwords do not match";
    }
  }
}

function resetPassword() {
  let newPassword = document.querySelector(".new-password").value;
  let confirmPassword = document.querySelector(".confirm-password").value;
  //console.log(newPassword, confirmPassword);
  checkPasswordMatch(newPassword, confirmPassword);
  formData = new FormData();

  formData.append("new-password", newPassword);
  formData.append("confirm-password", confirmPassword);
  formData.append("id", id);
  formData.append("recovery-link", recoveryLink);

  axios
    .post("../api/reset-password.php", formData)
    .then(function(response) {
      //console.log(response.data);
      if (response.data.statusCode == 200) {
        //console.log(response.data);
        window.location.href = "index.php";
      } else {
        // handle error
      }
    })
    .catch(function(error) {
      //console.log(error);
    });
}
