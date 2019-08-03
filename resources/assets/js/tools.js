const accordions = document.getElementsByClassName('has-submenu')
const adminSlideButton = document.getElementById('admin-slideout-button')

function setSubmenuStyles(submenu, maxHeight, margins) {
  submenu.style.maxHeight = maxHeight
  submenu.style.marginTop = margins
  submenu.style.marginBottom = margins
}
if (typeof adminSlideButton !== 'undefined' && adminSlideButton !== null)
  adminSlideButton.onclick = function () {
    this.classList.toggle('is-active');
    document.getElementById('admin-side-menu').classList.toggle('is-active');
  }
if (typeof accordions !== 'undefined' && accordions !== null)
  for (var i = 0; i < accordions.length; i++) {
    if (accordions[i].classList.contains('is-active')) {
      const submenu = accordions[i].nextElementSibling
      setSubmenuStyles(submenu, submenu.scrollHeight + "px", "0.75em")
    }

    accordions[i].onclick = function () {
      this.classList.toggle('is-active')

      const submenu = this.nextElementSibling
      if (submenu.style.maxHeight) {
        // menu is open, we need to close it now
        setSubmenuStyles(submenu, null, null)
      } else {
        // meny is close, so we need to open it
        setSubmenuStyles(submenu, submenu.scrollHeight + "px", "0.75em")
      }
    }
  }
topMenuSize = function () {
  return document.querySelector('.navbar').scrollHeight;
}
screenSize = function () {
  var height = window.scrollHeight ||
    document.documentElement.clientHeight ||
    document.body.clientHeight;
  return height;
}

function siteSizeAdjust() {
  if(document.querySelector('.management-area')) document.querySelector('.management-area').style.marginTop = topMenuSize() + 'px';
  var el = document.querySelector('#admin-side-menu');
  if(el != null){
    // el.style.maxHeight = (screenSize() - topMenuSize()) + 'px';
    el.style.minHeight = '100%';
    el.style.marginTop = topMenuSize() + 'px';
    el.querySelector('.menu').style.top = topMenuSize() + 'px';
    el.querySelector('.menu').style.maxHeight = '100%';
    // el.querySelector('.menu').style.minHeight = screenSize() - topMenuSize() + 'px';
  }
}
window.onload = function () {
  document.addEventListener("DOMContentLoaded", function() {
    // window.test = function calculate(exp) {
    //   const opMap = {
    //     '/': (a, b) => { return parseFloat(a) + parseFloat(b) },
    //     '*': (a, b) => { return parseFloat(a) - parseFloat(b) },
    //   };
    //   const opList = Object.keys(opMap);

    //   let acc = 0;
    //   let next = '';
    //   let currOp = '*';

    //   for (let char of exp) {
    //     if (opList.includes(char)) {
    //       acc = opMap[currOp](acc, next);
    //       currOp = char;
    //       next = '';
    //     } else {
    //       next += char;
    //     }
    //   }

    //   return currOp === '*' ? acc + parseFloat(next) : acc - parseFloat(next);
    // }
    siteSizeAdjust();
    window.onresize = function () {
      siteSizeAdjust();
    };
    // var button = document.querySelectorAll('nav.tabs ul li');
    var button = document.querySelectorAll('.tab-content input');
    for (let j = 0; j < button.length; j++) {
      button[j].addEventListener("click",function(){
          document.querySelector('.tab-content').style.overflow = "visible";
      }, true);
      
    }
    button = document.querySelectorAll('nav.tabs ul li');
    for (let j = 0; j < button.length; j++) {
      button[j].addEventListener("click",function(){
        document.querySelector('.tab-content').style.overflow = "hidden";
      }, true);
    }
  });
}
