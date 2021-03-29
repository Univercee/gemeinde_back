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
    props: {
      backendUrl: String
    },
    methods: {
        async onTelegramAuth(user) {
         if (this.backendUrl == '/auth/tg/channel'){
            await axios({
              method: 'post',
              url: this.backendUrl,
              data: {auth_data: user},
              headers: {
                Authorization: 'Bearer ' + sessionStorage.getItem('sessionKey')
              }
            }).then((response) => {
              console.warn(response)
              //getChannel.methods.getChannels()
              window.location.reload()
            }).catch((err) => {
              console.log("error", err)
            })
            return true
          }
          await axios.post('/auth/tg/verify', {auth_data: user}).then((response) => {
            sessionStorage.setItem('sessionKey', response.data.sessionkey)
            window.location.href = "./profile";
          })
        },
    async getKeys(){
      await axios.get("/keys").then(response => (
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
    },

  }
</script>
