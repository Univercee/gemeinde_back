function langDetect(){
  let href = window.location.href.split("#").pop()
  if(href == 'en' || href == 'de') {
    this.lang = href
  }else{
    this.lang = navigator.language.split('-')[0]
  }
  return this.lang
}

i18n = VueI18n.createI18n({
		globalInjection: true,
		legacy: false,
		locale: langDetect(),
		fallbackLocale: 'en'
	})


/* boilerplate for vue3-sfc-loader */
const options = {
  moduleCache: {
    vue: Vue,
  },

  getFile(url) {
    return fetch(url).then(response => response.ok ? response.text() : Promise.reject(response));
  },

  addStyle(styleStr) {
    const style = document.createElement('style');
    style.textContent = styleStr;
    const ref = document.head.getElementsByTagName('style')[0] || null;
    document.head.insertBefore(style, ref);
  },

  customBlockHandler(block, filename, options) {
		if ( block.type !== 'i18n' )
			return
		const messages = JSON.parse(block.content);
		for ( let locale in messages )
			i18n.global.mergeLocaleMessage(locale, messages[locale]);
	},
  
  log(type, ...args) {
    console.log(type, ...args);
  }
}

const { loadModule, version } = window["vue3-sfc-loader"];
/* end of boilerplate for vue3-sfc-loader */
