let darkmode = localStorage.getItem('darkmode')
const themeSwitch = document.getElementById('darkmode-toggle')
const ifTrue = document.querySelector('.ifTrue')
const burger = document.querySelector(".burger")
const menu = document.querySelector(".nav-menu")
const active = document.querySelector(".active2")
let tab = document.querySelectorAll(".tab"); // Selectionner tabs et contenus    //
let div = document.querySelectorAll(".none");

const enableDarkmode = () => {
    document.body.classList.add('darkmode')
    localStorage.setItem('darkmode', 'active')
    ifTrue.innerHTML = 'NON'
}

const disableDarkmode = () => {
    document.body.classList.remove('darkmode')
    localStorage.setItem('darkmode', null)
    ifTrue.innerHTML = 'OUI'
}

if(darkmode === "active") enableDarkmode()

themeSwitch.addEventListener("click", () => {
    darkmode = localStorage.getItem('darkmode')
    darkmode !== "active" ? enableDarkmode() : disableDarkmode()
})

burger.addEventListener("click", () => {
    menu.classList.toggle("active2")
    burger.classList.toggle("active");
})

tab.forEach(function (element){
    // Pour chaque elemnt du tab, quand on click    //
    element.addEventListener("click", function() {
        
        tab.forEach(function (item) { // si on a pas clickeé sur ces tabs , on va les enlever    //
            item.classList.remove("tab-active");
            
            
        });

        div.forEach(function (item2) { // et pour content du text, enlever aussi    //
            item2.classList.remove("active");
            
            item2.classList.add("none");
        });

        this.classList.add("tab-active"); // que sur celle quelle on a clickquee apparaitre    //
        
       if (this.classList.contains("tab-menu1")) { // si on click sur tab1, alors text et image de ce contenu faire apparaitre    //
            document.querySelector(".text_container_title1").classList.add("active");
            document.querySelector(".text_container_description1").classList.add("active");
            
       }
            //  Meme chose pour les  autres tabs   //
       else if (this.classList.contains("tab-menu2")) {
            document.querySelector(".text_container_title2").classList.add("active");
            document.querySelector(".text_container_description2").classList.add("active");
            
       }

       else if (this.classList.contains("tab-menu3")) {
            document.querySelector(".text_container_title3").classList.add("active");
            document.querySelector(".text_container_description3").classList.add("active");
            
       }
       
    
    });
    
    //   apparaitre dans console, pour voir ce qui ce passe  //
    console.log(this);

});





