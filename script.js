let darkmode = localStorage.getItem('darkmode')
const themeSwitch = document.getElementById('darkmode-toggle')
const ifTrue = document.querySelector('.ifTrue')
const burger = document.querySelector(".burger")
const menu = document.querySelector(".nav-menu")
const active = document.querySelector(".active2")

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


