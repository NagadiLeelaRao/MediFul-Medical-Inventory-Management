document.addEventListener("DOMContentLoaded", function() {
    fetch("header.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("header-container").innerHTML = data;
        })
        .catch(error => console.error("Error loading header:", error));
});
// checkout button on welcome page 
function checkout() {
    window.location.href = "CheckoutPage.html"; // Redirects to checkout.html
}

function manageInventory(){
    window.location.href = "ManageInventory.html";
} 

function inventoryAddItems(){
    window.location.href = "inventoryAddItems.html";
} 

function viewInventory(){
    window.location.href = "ViewInventory.html";
}