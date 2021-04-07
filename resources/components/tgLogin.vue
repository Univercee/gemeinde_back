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
    props:{
      isChannel:{
        type:Boolean,
        default:false
      }
    },
    methods: {
      async onTelegramAuth(user) {
        await axios.post('/auth/tg/verify', {auth_data: user}).then((response) => {
          sessionStorage.setItem('sessionKey', response.data.sessionkey)
          window.location.href = "./profile";
        })
      },
      async onTelegramChannelAuth(user) {
        await axios.post('/profile/channels/tg/verify', {auth_data: user}).then((response) => {
            this.$emit('verifyTgChannel')
        })
      },
      async getKeys(){
        await axios.get("/keys").then(response => (
          this.tgBotName = response.data.tgBotName
        ));
      },
      async tgInit(){
        window.onTelegramAuth = this.$props.isChannel ? this.onTelegramChannelAuth : this.onTelegramAuth

        let script = document.createElement('script');
        script.setAttribute('src','https://telegram.org/js/telegram-widget.js?14')
        script.setAttribute('data-telegram-login',this.tgBotName)
        script.setAttribute('data-size','large')
        script.setAttribute('data-radius','5')
        script.setAttribute('async',true)
        script.setAttribute('data-onauth','onTelegramAuth(user)')
        script.setAttribute('data-request-access','write')
        script.setAttribute('data-userpic','true')
        script.setAttribute('data-lang','de')
        this.$refs.tgLogin.appendChild(script)
      }
    },
    mounted: async function(){
        await this.getKeys()
        this.tgInit()
    },

  }
</script>
