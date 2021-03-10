<template id="verify-component">
    <template v-if="resp != null">
    <div id="verify-container">
        <div id="loader" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="alert alert-primary" role="alert" v-for="message in messages">
            {{message}}
        </div>
    </div>
    </template>
</template>
<script>

    export default {
        names: 'verify', data() {
            return {
                resp: null,
                messages: [],
            }
        },    state:{
            user:null
        },
        methods: {
            async verify(){
                const key =	window.location.pathname.split("/").pop()

                await axios.get("/auth/email/verify/"+key).then((response) => {
                    this.messages.push('Please wait we try to verify you')
                    this.resp = response.data.sessionkey
                    this.login(this.resp)
                });

                await new Promise(resolve => setTimeout(resolve, 2000));
                console.log(this.resp)
            },

            async sendHeader(){
                await axios({
                    baseURL: 'http://127.0.0.1/',
                    method: 'post',
                    url: '/profile',
                    data: null,
                    headers: {
                        Authorization: 'Bearer ' + this.resp
                    }
                }).then((response) => {
                    this.messages.pop()
                    document.getElementById("loader").setAttribute("class", "dont-sping")
                    this.messages.push('You are verified you can proceed to our profile')
                    console.warn(response)
                });
            },login(sessionKey){
                sessionStorage.setItem('sessionKey', sessionKey)
            }

        },
        mounted:async function() {
          if (window.location.href.indexOf("signup/") > -1) {
            let urlLength = (window.location.pathname.split("/signup/")[1]).length
            if ((window.location.pathname.split("/").length - 1 == 2) && urlLength == 13) {
              await this.verify();
              await this.sendHeader();
            }
          }
        }
    }

</script>
