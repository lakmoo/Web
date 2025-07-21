let img;
let body;

window.addEventListener("load", () => {
    body = document.querySelector("body");
    logo = document.getElementById("theme");

    logo.addEventListener("click", () => {
       
        if (body.classList.contains("nuit")) {
          logo.src = "img/Nuit.png";
          body.classList.remove("nuit");
        } 
        else {
          logo.src = "img/Jour.png";  
          body.classList.add("nuit");
             
        }
    });
});
