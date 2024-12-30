document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("registerBtn").addEventListener("click", function(event) {
        event.preventDefault();
        var name = document.querySelector('input[name="nameI"]').value;
        var email = document.querySelector('input[name="emailI"]').value;
        var password = document.querySelector('input[name="passwordI"]').value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../php/register.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === "success") {
                            alert(response.message);
                           window.location.href = "../html/admin.php";
                           // Chemin mis à jour pour le répertoire "html"
                        } else {
                            alert(response.message);
                        }
                    } catch (e) {
                        console.error("Invalid JSON response", xhr.responseText);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                } else {
                    console.error("HTTP error", xhr.status, xhr.statusText);
                    alert("Une erreur s'est produite. Veuillez réessayer.");
                }
            }
        };
        xhr.send("&nameI=" + encodeURIComponent(name) + "&emailI=" + encodeURIComponent(email) + "&passwordI=" + encodeURIComponent(password));
    });

    document.getElementById("loginBtn").addEventListener("click", function(event) {
        event.preventDefault();
        var role = document.querySelector('input[name="role"]:checked').value;
        var email = document.querySelector('input[name="emailC"]').value;
        var password = document.querySelector('input[name="passwordC"]').value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../php/login.php", true);  // Chemin mis à jour pour le répertoire "php"
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    try {
                        console.log("Server response:", xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === "success") {
                            if(role == "user"){window.location.href = "../html/user.php";}
                            else if(role == "admin") window.location.href = "../html/admin.php";
                            else window.location.href = "../html/home.php";  // Chemin mis à jour pour le répertoire "html"
                        } else {
                            alert(response.message);
                        }
                    } catch (e) {
                        console.error("Invalid JSON response", xhr.responseText,);
                        console.error(e);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                } else {
                    console.error("HTTP error", xhr.status, xhr.statusText);
                    alert("Une erreur s'est produite. Veuillez réessayer.");
                }
            }
        };
        xhr.send("role=" + encodeURIComponent(role) + "&emailC=" + encodeURIComponent(email) + "&passwordC=" + encodeURIComponent(password));
    });
});
const signInButton = document.getElementById('signIn');
const signUpButton = document.getElementById('signUp');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
    container.classList.add('right-panel-active');
});

signInButton.addEventListener('click', () => {
    container.classList.remove('right-panel-active');
});
