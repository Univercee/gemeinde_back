// plugins/ahma.js
const ahma = {
  install: (app, options) => {
    function translate() {
      alert('lokey')
    }

    app.config.globalProperties.$translate = translate;

    app.provide("ahma", {translate});
  }
}
