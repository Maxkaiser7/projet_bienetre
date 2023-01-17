//burger menu
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

hamburger.addEventListener("click", () => {
    hamburger.classList.toggle('active');
    navLinks.classList.toggle('active');
})

//home slider
let counter = 1
const div = document.querySelector('.container-slide')

setInterval(() => {
    document.querySelector('.img.show').classList.remove('show')
    const img = document.querySelector(`.img-${counter}`)
    img.classList.add('show')
    counter++
    if (counter > div.children.length) {
        counter = 1
    }
},5000)

//like btn
const like_btn = document.querySelector('.like-btn');
like_btn.addEventListener('click', (e) => {
    like_btn.classList.toggle('dislike')
})

