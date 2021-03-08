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
        },
        methods: {
            async verify(){
                const key =	window.location.pathname.split("/").pop()

                await axios.get("/api/auth/email/verify/"+key).then((response) => {
                    this.messages.push('Please wait we try to verify you')
                    this.resp = response.data.sessionkey
                    sessionStorage.setItem('sessionKey', this.resp)
                    axios.defaults.headers.common['Authorization'] = sessionStorage.getItem('sessionKey')

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
            }

        },mounted:async function(){
            await this.verify();
            await this.sendHeader();
        }

    }
</script>
