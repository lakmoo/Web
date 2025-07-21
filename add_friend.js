let ico;
let tmp;
let clicked = false;

window.addEventListener("load", () => {
    ico = document.getElementById("friend");
    tmp = ico.src;
    //Juste pour changer la couleur du ico(ça fait pro)
    ico.addEventListener("mouseover", () => {
        if (!clicked) {
            ico.src = "img/no_friendB.png";
        }
    });

    ico.addEventListener("mouseout", () => {
        if (!clicked) {
            ico.src = tmp;
        }
    });

    ico.addEventListener("click", () => {
        ico.src = "img/friend_maybe.png";
        clicked = true; 
    });
});
