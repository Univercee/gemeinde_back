<template>
  <div id="channels-container">

    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item" v-for="(value, name, index) in channels[0]">
        <div class="accordion-item">
          <h2 class="accordion-header" v-bind:id="'flush-heading'+index">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse'+index" aria-expanded="false" v-bind:aria-controls="'#flush-collapse'+index">
                <div v-if="name == 'email'">
                  <div v-if="value == null">
                    <p>{{name}} Not connected </p>
                  </div>
                  <div v-else>
                    <p>Email :{{value}}</p>
                  </div>
                </div>
                <div v-if="name == 'telegram'">
                  <div v-if="value != null">
                    <p>Telegram: {{value}}</p>
                  </div>
                  <div v-else>
                    <p>{{name}} Not connected </p>
                  </div>
                </div>
              </div>
            </button>
          </h2>
          <div v-bind:id="'flush-collapse'+index" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading'+index" v-bind:data-bs-parent="'#accordionFlushExample'+index">
            <div class="accordion-body">
            <div v-if="name == 'email'">
          <div v-if="value == null">

            <div id="email-channel">
              <form @submit.prevent="submit" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                  <div class="d-flex justify-content-center">
                    <div ref="wait_span" class="align-items-center lds-facebook d-none "><div></div><div></div><div></div></div>
                  </div>
                  <div >
                    <h1>{{ notify }}</h1>
                  </div>
                  <div ref="emailComponents" class="">
                    <label for="email" v-visible="email" class="form-label form-label-sm"><small>E-mail address</small></label>
                    <input type="email" class="form-control form-control-sm" placeholder="E-mail address" name="email" id="email" aria-describedby="emailHelp" v-model="email" required>
                  </div>
                  <div class="invalid-feedback">
                    <p v-if="errors.length">
                    <ul>
                      <li v-for="error in errors">{{ error }}</li>
                    </ul>
                    </p>
                  </div>
                </div>
                <div ref="emailAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                  <strong>Email is sent!</strong> Please check your email.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <a ref="sessionComponents" href="/profile" role="button" class="btn form-control btn-success btn-sm d-none">Continue</a>
                <div ref="emailComponentButton" class="">
                  <button role="button" class="btn btn-primary btn-sm">Continue</button>
                </div>
                <div ref='recaptcha'></div>
              </form>
            </div>


          </div>
          <div v-else>
            <p>Email :{{value}}</p>
          </div>
        </div>
        <div v-if="name == 'telegram'">
          <div v-if="value != null">
            <p>Telegram: {{value}}</p>
          </div>
          <div v-else>
            <tgChannel backendUrl="/auth/tg/channel"></tgChannel>
          </div>
        </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import tgChannel from './tgLogin.vue'
export default {
  names: 'location',
  data() {
    return {
      file: '',
      session: null,
      notify: null,
      email: null,
      errors: [],
      telegram: null,
      channels: [],
      verifyMessage: '',
      token: null,
      userEmail: '',
      googleRecaptchaSiteKey: null,
    }
  },
  methods: {
    async submit() {
      document.getElementById("email").classList.remove("is-invalid")
      this.errors = [] //added to clean array each time btn is pressed to flush old errs
      var regex = new RegExp('(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\\])');
      if((regex.test(this.email)) == false){
        this.errors.push('email is bad')
        document.getElementById("email").classList.add("is-invalid")
        return false; //otherwise execution goes on to axios
      }
      this.$refs['emailAlert'].classList.remove("d-none")
      sessionStorage.setItem('email', this.email)
      await grecaptcha.execute(this.googleRecaptchaSiteKey, {action: 'submit'}).then(token => (
        axios({
          method: 'post',
          url: '/auth/emailchannel',
          data: {email: this.email, token: token},
          headers: {
            Authorization: 'Bearer ' + sessionStorage.getItem('sessionKey')
          }
        }).then((response) => {
          console.warn(response)
        }).catch((err) =>{
          console.log("error",err)
        })

      ))


    },
    async verify(secretKey){

      this.verifyMessage = ''
      await axios.get("/auth/email/verify/"+secretKey).then((response) => {
        sessionStorage.setItem('email',response.data.useremail)

        this.verifyMessage = 'You are verified'
        this.getChannels()
        sessionStorage.setItem("secretKey", secretKey)

      }).catch((err) => {
        if(err.response){
          if(err.response.status === 404) {
            this.verifyMessage = "Verification key is not found, please try again"
          }else if (err.response.status === 403){
            this.verifyMessage = 'Verification key is expired, please try again'

          }
          console.log(err.response)
        }
      });
      await axios({
        baseURL: 'http://127.0.0.1/', // optional
        method: 'post',
        url: '/api/emailbykey',
        data: null,
        headers: {
          Authorization: 'Bearer ' + sessionStorage.getItem('sessionKey')
        }
      }).then((response) => {
        this.userEmail = response.data.email
        console.warn(response)
      }).catch((err) =>{
        console.warn(err)
      })
      console.log(this.resp)
    },
    async getKeys(){
      await axios.post("/keys").then(responce => (
        this.googleRecaptchaSiteKey = responce.data.googleRecaptchaSiteKey
      ));
    },
    async getChannels(){
      await axios({
        baseURL: 'http://127.0.0.1/', // optional
        method: 'post',
        url: '/api/channels',
        headers: {
          'Authorization': 'Bearer 3179d8aa1b259f2f8066840c5d154e53abe359346cbdd8c2d4301014c5eeb6b9'
        }
      }).then((response) => {
        let telegramName;
        let email;
        if(response.data[0].telegram_id != null){
          telegramName = response.data[0].username
        }
        if(response.data[0].email != null){
          email = response.data[0].email
        }
        let channels = {'email': email, 'telegram': telegramName}
        this.channels.push(channels);
      }).catch((err) =>{
        console.log(err)
      });
    },
    aeert(){
      alert('loded')
    }
  },

  mounted: async function(){
    this.verifyMessage = ''
    await this.getKeys();
    const script = document.createElement('script');
    script.src = "https://www.google.com/recaptcha/api.js?render="+this.googleRecaptchaSiteKey
    document.body.insertBefore(script,document.getElementById('vuescript'));
    if (window.location.hash) {
       let secretKey = window.location.hash.split("#")[1];
      if (secretKey) {
        await this.verify(secretKey);
        this.session = sessionStorage.getItem("sessionKey")
        if (sessionStorage.getItem("secretKey") != window.location.hash.split("#")[1]){
          this.notify = this.verifyMessage
        }
      }
    }
    this.getChannels()

  },
  updated: function (){
    if (window.location.hash) {
      var myCollapse = document.getElementById('flush-collapse0')
      new bootstrap.Collapse(myCollapse, {
        show: true
      })

    }
  },
  components: {
    tgChannel
  }
}
</script>
<style>

</style>
