<template>
    <div ref="tgLogin"></div>    
</template>
<script>
    export default {
        data(){
            return{
                tgBotName:null
            }
        },
        methods: {
            onTelegramAuth(user) {
                axios.post('/auth/tg/verify',{auth_data: user})
            },
			async getKeys(){
				await axios.post("/keys").then(responce => (
					this.tgBotName = responce.data.tgBotName
				));
			},
            async tgInit(){
                window.onTelegramAuth=this.onTelegramAuth
                let script = document.createElement('script');
                script.setAttribute('src','https://telegram.org/js/telegram-widget.js?14')
                script.setAttribute('data-telegram-login',this.tgBotName)
                script.setAttribute('data-size','large')
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