<template>
    <div ref="tgLogin"></div>    
</template>
<script>
    export default {
        props:['botName'],
        methods: {
            onTelegramAuth(user) {
                axios.post('/auth/tg/verify',{auth_data: user})
            },
            async getTgBotName(){
                let tgBotName = await axios.post('/auth/tg/key')
                return tgBotName.data;
            },
            async tgInit(){
                window.onTelegramAuth=this.onTelegramAuth
                let script = document.createElement('script');
                script.setAttribute('src','https://telegram.org/js/telegram-widget.js?14')
                script.setAttribute('data-telegram-login',await this.getTgBotName())
                script.setAttribute('data-size','large')
                script.setAttribute('data-onauth','onTelegramAuth(user)')
                script.setAttribute('data-request-access','write')
                this.$refs.tgLogin.appendChild(script)
            }
        },
        mounted: async function(){
            await this.tgInit()
        }
    }
</script> 