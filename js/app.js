(function (factory) {
    typeof define === 'function' && define.amd ? define('app', factory) :
    factory();
  }((function () { 'use strict';
  
    var menuBtnOpen = document.querySelectorAll(".js--menu-mobile--open");
    const menuCloseButton = document.querySelectorAll('.js--menu-mobile--close');
    const menu = document.getElementById("js--nav-menu");
    menuBtnOpen.forEach(element => {
      element.addEventListener("click", function () {
        menu.classList.add("mobile-open");
      });
    });
    menuCloseButton.forEach(element => {
      element.addEventListener('click', function () {
        menu.classList.remove("mobile-open");
      });
    });
    const menuFirstLevel = document.querySelectorAll(".js--menu--first-level");
    const subMenuBtnClose = document.querySelectorAll(".js--submenu--close");
    const sidenav = document.getElementsByClassName("tpl--sidenav")[0];
    menuFirstLevel.forEach(element => {
      element.addEventListener("click", function () {
        let menuOpen = document.querySelectorAll(".js--menu--first-level.open");
  
        if (this.classList.contains("open")) {
          this.classList.remove("open");
          sidenav.classList.remove("open");
        } else {
          if (menuOpen.length != 0) {
            menuOpen.forEach(element => {
              element.classList.remove("open");
            });
          } else {
            sidenav.classList.add("open");
          }
  
          this.classList.add("open");
        }
      });
    });
    subMenuBtnClose.forEach(button => {
      button.addEventListener('click', function () {
        let thisSubmenu = this.closest('li').getElementsByClassName('js--menu--first-level');
        thisSubmenu[0].classList.remove('open');
        sidenav.classList.remove('open');
      });
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  
  })));
  