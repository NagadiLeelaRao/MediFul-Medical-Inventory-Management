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

function loadStock() {
    fetch('fetch_stock.php')
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); 

            let tableBody = document.querySelector("#stockTable tbody");
            tableBody.innerHTML = ""; 

            data.forEach(row => {
                console.log("Row Data:", row); 

                let tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.id|| "N/A"}</td>
                    <td>${row.Name|| "N/A"}</td>
                    <td>${row.Manufacturer || "N/A"}</td>
                    <td>${row.Pack_Size || "N/A"}</td>
                    <td>${row.Price || "N/A"}</td>
                    <td>${row.qty || "N/A"}</td>
                    <td>${row.batchno || "N/A"}</td>
                    <td>${row.mfd_date || "N/A"}</td>
                    <td>${row.exp_date || "N/A"}</td>
                    <td>${row.Total || "N/A"}</td>
                `;
                tableBody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

function loadInventory() {
    console.log("Loading inventory..."); 

    fetch('fetch_data.php')
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); 

            let tableBody = document.querySelector("#dataTable tbody");
            tableBody.innerHTML = ""; 

            data.forEach(row => {
                console.log("Row Data:", row); 

                let tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.Id|| "N/A"}</td>
                    <td>${row.Name|| "N/A"}</td>
                    <td>${row.Price || "N/A"}</td>
                    <td>${(row.Is_Discontinued ? "No" : "Yes") || "N/A"}</td>
                    <td>${row.Manufacturer || "N/A"}</td>
                    <td>${row.Type || "N/A"}</td>
                    <td>${row.Pack_Size || "N/A"}</td>
                    <td>${row.Short_Composition1 || "N/A"}</td>
                    <td>${row.Short_Composition2 || "N/A"}</td>
                `;
                tableBody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}