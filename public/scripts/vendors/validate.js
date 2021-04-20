/**
 *  Validation framework created by Santiago Donoso
 */

function fnbIsFormValid(oForm) {
  //console.log(oForm);
  fvDo(oForm.querySelectorAll("input[data-type]"), function(oElement) {
    oElement.classList.remove("error");
    document.querySelector(".error-message").textContent = " ";
    document.querySelector(".error-message-mobile").textContent = " ";
  });

  fvDo(oForm.querySelectorAll("input[data-type]"), function(oElement) {
    var sValue = oElement.value;
    var sDataType = oElement.getAttribute("data-type"); // $(oInput).attr('data-type')
    var iMin = oElement.getAttribute("data-min"); //$(oInput).attr('data-min')
    var iMax = oElement.getAttribute("data-max"); // $(oInput).attr('data-max')
    switch (sDataType) {
      case "string":
        if (sValue.length < iMin || sValue.length > iMax) {
          oElement.classList.add("error");
          document.querySelector(".error-message").textContent =
            "Some of the input fields are not valid";
          document.querySelector(".error-message-mobile").textContent =
            "Some of the input fields are not valid";
        }
        break;
      case "integer":
        if (
          !parseInt(sValue) ||
          parseInt(sValue) < parseInt(iMin) ||
          parseInt(sValue) > parseInt(iMax)
        ) {
          oElement.classList.add("error");
          document.querySelector(".error-message").textContent =
            "Some of the input fields are not valid";
          document.querySelector(".error-message-mobile").textContent =
            "Some of the input fields are not valid";
        }
        break;
      case "email":
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (re.test(String(sValue).toLowerCase()) == false) {
          oElement.classList.add("error");
        }
        break;
      default:
        //console.log("default");
        break;
    }
  });

  if (oForm.querySelectorAll("input.error").length) {
    document.querySelector(".error-message").textContent =
      "Some of the input fields are not valid";
    document.querySelector(".error-message-mobile").textContent =
      "Some of the input fields are not valid";
    return false;
  }
  return true;
}

function fileValidation(oInput) {
  var fileInput = document.getElementById(oInput);
  var filePath = fileInput.value;
  var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
  //fileInput.classList.remove("error");

  if (!fileInput.value) {
    fileInput.classList.add("error");

    return false;
  }

  if (fileInput.files[0] && fileInput.files) {
    var fileSize = fileInput.files[0].size / 1024 / 1024;
    if (!allowedExtensions.exec(filePath)) {
      fileInput.classList.add("error");
      document.querySelector(".error-message").textContent =
        "Image must be jpeg, jpg or png";
      document.querySelector(".error-message-mobile").textContent =
        "Image must be jpeg, jpg or png";
      fileInput.value = "";
      return false;
    } else if (fileSize > 1) {
      fileInput.classList.add("error");
      document.querySelector(".error-message").textContent =
        "Image cannot be over 1MB";
      document.querySelector(".error-message-mobile").textContent =
        "Image cannot be over 1MB";
      fileInput.value = "";
      return false;
    } else {
      fileInput.classList.remove("error");
      var reader = new FileReader();
      reader.onload = function(e) {
        document.querySelector(".image-preview").style.backgroundImage =
          "url(" + e.target.result + ")";
      };
      reader.readAsDataURL(fileInput.files[0]);
    }
  }
}

function fvValidateEvent(oBtn) {
  var eventForm = document.querySelector(".handle-event-container");
  var eventTextarea = eventForm.querySelector("textarea");

  if (eventTextarea.value.length == 0) {
    eventTextarea.classList.add("error");
  }

  if (eventTextarea.value.length < 2) {
    eventTextarea.classList.add("error");
  }

  if (eventTextarea.value.length > 1200) {
    eventTextarea.classList.add("error");
  }

  //console.log(eventTextarea);
  var bIsValid = fnbIsFormValid(eventForm);
  fileValidation("event-image");

  if (bIsValid == false) {
    return;
  }
}

function fvValidateImage(oInput) {
  fileValidation("event-image");
}

function fvValidateUser(oBtn) {
  //console.log('clicked')
  var employeeForm = document.querySelector(".handle-employee-container");
  var bIsValid = fnbIsFormValid(employeeForm);

  if (bIsValid == false) {
    return;
  }
}

function fvDo(aElements, fvCallback) {
  for (var i = 0; i < aElements.length; i++) {
    fvCallback(aElements[i]);
  }
}
