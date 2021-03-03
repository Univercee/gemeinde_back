<template>
    <div ref="tgLogin"></div>
</template>
<script>
    export default {
        props: ['tgBotName'],
        methods: {
            onTelegramAuth(user) {
                axios.post('/auth/tg/verify',{auth_data: user})
            },
            tgInit(){
                window.onTelegramAuth=this.onTelegramAuth
                let script = document.createElement('script');
                script.setAttribute('src','https://telegram.org/js/telegram-widget.js?14')
                script.setAttribute('data-telegram-login',this.$props.tgBotName)
                script.setAttribute('data-size','large')
                script.setAttribute('data-onauth','onTelegramAuth(user)')
                script.setAttribute('data-request-access','write')
                this.$refs.tgLogin.appendChild(script)
            }
        },
        mounted: async function(){
            this.tgInit() 
        }
    }
</script> 