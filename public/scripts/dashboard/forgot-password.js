/* ==========================================================================
   Global variables
   ========================================================================== */

/*  ==========================================================================
    Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  startSendButton();
}

//Validate input fields

const emailInput = document.querySelectorAll("input");
input.addEventListener("blur", checkFields);

function checkFields(evt) {
  console.log(evt.target.name);
  if (evt.target.value.length == 0 || !evt.target.value) {
    document.querySelector(".error-message").textContent =
      "add a " + evt.target.name;
    evt.target.classList.add("error");
    return false;
  }
  if (evt.target.value.length < 2) {
    document.querySelector(".error-message").textContent =
      evt.target.name + " is too short";
    evt.target.classList.add("error");
    return false;
  }
  if (evt.target.name == "email") {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (re.test(String(evt.target.value).toLowerCase()) == false) {
      document.querySelector(".error-message").textContent =
        evt.target.name + " not valid";
      evt.target.classList.add("error");
      return false;
    }
  }
  document.querySelector(".error-message").textContent = "";
  evt.target.classList.remove("error");
  return true;
}

function startSendButton() {
  const sendButton = document.querySelector("form button");

  sendButton.addEventListener("click", sendRecoveryEmail);
}

function sendRecoveryEmail() {
  email = document.querySelector("form input").value;
  console.log(email);
  formData = new FormData();
  formData.append("email", email);

  axios
    .post("../api/send-recovery-email.php", formData)
    .then(function(response) {
      console.log(response.data);
      if (response.data.statusCode == 200) {
        console.log(response.data.message);
        document.querySelector(".confirmation-message").textContent =
          response.data.message;
      } else {
        // handle error
        console.log(response.data.message);
        document.querySelector(".confirmation-message").textContent =
          response.data.message;
      }
    })
    .catch(function(error) {
      console.log(error);
    });
}
