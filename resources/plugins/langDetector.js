// plugins/langDetector.js
const langDetector = {
  install: (app, options) => {

    function translate(){
      let currentURL = (window.location.href);
      let splitURL = currentURL.toString().split("/");
      let href = splitURL[3];
      if(href == 'en' || href == 'de') {
        this.lang = href
      }else{
        this.lang = navigator.language.split('-')[0]
      }
      return this.lang
    }
    app.config.globalProperties.$translate = translate;

    app.provide("langDetector", {translate});
  }
}
