<template id="verify-component">

    <div id="verify-container">
        <h1>Verify Page</h1>
        <div id="loader" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="alert alert-primary" role="alert" v-for="message in messages">
            {{message}}
        </div>
    </div>

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
        getters:{
            getUser(state){
                return state.user
            },
            getSessionKey(){
                return sessionStorage.getItem('sessionKey') ?? null
            }
        },
        mutations:{
            setUser(state, user){
                state.user = user
            }
        },
        methods: {
            async verify(){
                const key =	window.location.pathname.split("/").pop()

                await axios.get("/api/auth/email/verify/"+key).then((response) => {
                    this.messages.push('Please wait we try to verify you')
                    this.resp = response.data.sessionkey
                    this.login(this.resp)
                });

                await new Promise(resolve => setTimeout(resolve, 2000));
                console.log(this.resp)
            },

            async sendHeader(){
                await axios({
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
                axios.defaults.headers.common['Authorization'] = 'Bearer '+sessionStorage.getItem('sessionKey')
                console.log(axios.defaults.headers)
            }

        },
        mounted:async function(){
            await this.verify();
            await this.sendHeader();
           }

    }

</script>
