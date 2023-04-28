const burger = document.querySelector('.menu');
const nav = document.querySelector('.sidenav');

const navSlide = () => {
    burger.addEventListener('click', () => {
        nav.classList.toggle('nav-show');
        burger.classList.toggle('toggle');
    });//click then add

    document.addEventListener('click', (event) => {
        // check if the click event target is outside of the nav element
        if (!nav.contains(event.target) && !burger.contains(event.target)) {
          nav.classList.remove('nav-show');
          burger.classList.remove('toggle');
        }
      });
} 

document.querySelectorAll('.side-nav').forEach(n => n.addEventListener('click', () =>{
    burger.classList.remove('toggle');
    nav.classList.remove('nav-show');
}));  //Removing in the classlist whe clicking inside a nav-links



navSlide();