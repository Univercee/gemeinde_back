// plugins/langDetector.js
const localizator = {
  install: (app, options) => {

    function getLang(){ 
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
    app.config.globalProperties.$translate = getLang;
    app.provide("langDetector", {getLang});

    i18n = VueI18n.createI18n({
      globalInjection: true,
      legacy: false,
      locale: getLang(),
      fallbackLocale: 'en',
      messages:{
        en:{
            'or':'or'
        },
        de:{
            'or':'oder'
        }
      }
    })
    app.use(i18n)
  }
}
