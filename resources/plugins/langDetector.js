// plugins/langDetector.js
const langDetector = {
  install: (app, options) => {

    function translate(){
      let href = window.location.href.split("/").pop()
      if(href == 'en' || href == 'de') {
        this.lang = href
      }else {
        this.lang = navigator.language.split('-')[0]
      }
      return this.lang
    }
    app.config.globalProperties.$translate = translate;

    app.provide("langDetector", {translate});
  }
}
