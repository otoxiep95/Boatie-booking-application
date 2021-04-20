/* ==========================================================================
   Global variables
   ========================================================================== */
const body = document.querySelector("body");

/*  ==========================================================================
      Initialize
    ========================================================================== */
document.addEventListener("DOMContentLoaded", init);
function init() {
  //do stuff after page has loaded
  let coll = document.getElementsByClassName("collapsible");
  let i;

  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
      this.classList.toggle("active");
      let content = this.nextElementSibling;
      let arrow = this.querySelector(".arrow");
      if (content.style.maxHeight) {
        content.style.maxHeight = null;
        arrow.style.transform = "rotate(135deg)";
        //content.style.paddingBottom = null;
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        arrow.style.transform = "rotate(-45deg)";
        //content.style.paddingBottom = "50px";
      }
    });
  }
}

/*  ==========================================================================
      Functions
     ========================================================================== */
