<template>
    <div ref="tgLogin"></div>
</template>
<script>
  export default {
    data() {
        return {
            tgBotName: null
        }
    },
    methods: {
        async onTelegramAuth(user) {
            await axios.post('/auth/tg/verify',{auth_data: user}).then((response) =>{
              sessionStorage.setItem('sessionKey', response.data.sessionkey)
            })
        },
    async getKeys(){
      await axios.post("/keys").then(response => (
        this.tgBotName = response.data.tgBotName
      ));
    },
      async tgInit(){
          window.onTelegramAuth = this.onTelegramAuth
          let script = document.createElement('script');
          script.setAttribute('src','https://telegram.org/js/telegram-widget.js?14')
          script.setAttribute('data-telegram-login',this.tgBotName)
          script.setAttribute('data-size','medium')
          script.setAttribute('data-radius','5')
          script.setAttribute('async',true)
          script.setAttribute('data-onauth','onTelegramAuth(user)')
          script.setAttribute('data-request-access','write')
          this.$refs.tgLogin.appendChild(script)
      }
    },
    mounted: async function(){
        await this.getKeys()
        this.tgInit()
    }
  }
</script>
