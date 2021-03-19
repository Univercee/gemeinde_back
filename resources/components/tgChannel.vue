<template>
  <div ref="tgChannel"></div>
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
      await axios({
        method: 'post',
        url: '/auth/tg/channel',
        data: {auth_data: user},
        headers: {
          Authorization: 'Bearer ' + sessionStorage.getItem('sessionKey')
        }
      }).then((response) => {
        console.warn(response)
      }).catch((err) =>{
        console.log("error",err)
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
      this.$refs.tgChannel.appendChild(script)
    }
  },
  mounted: async function(){
    await this.getKeys()
    this.tgInit()
  }
}
</script>
