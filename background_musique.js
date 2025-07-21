let audio = document.getElementById('Musique');
let bouton = document.getElementById('iconM');
window.addEventListener("load", () => {
    bouton.addEventListener("click", () => {
        if (audio.paused) {
            bouton.src = "img/Music_logo_off.png";
            audio.play();
        } 
        else {
            bouton.src = "img/Music_logo_on.png";
            audio.pause();
            
        }
    });
});
