// JavaScript External - Script cho slideshow
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("slide");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  if (slides[slideIndex-1]) {
    slides[slideIndex-1].style.display = "block";
  }
  if (dots[slideIndex-1]) {
    dots[slideIndex-1].className += " active";
  }
}

// Tự động chuyển slide (tùy chọn)
setInterval(function(){
    plusSlides(1);
}, 5000); // Chuyển slide sau mỗi 5 giây