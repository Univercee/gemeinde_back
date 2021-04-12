i18n = VueI18n.createI18n({
    globalInjection: true,
    legacy: false,
    locale: 'en',
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