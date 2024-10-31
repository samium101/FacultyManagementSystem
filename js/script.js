// js/script.js

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("addEventModal");
    const btn = document.getElementById("addEventBtn");
    const span = document.getElementsByClassName("close")[0];

    // Open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Close the modal when the user clicks on <span> (x)
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal when the user clicks anywhere outside of the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
