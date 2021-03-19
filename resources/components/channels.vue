<template>
  <div id="channels-container">

    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item" v-for="(value, name, index) in channels[0]">
        <div class="accordion-item">
          <h2 class="accordion-header" v-bind:id="'flush-heading'+index">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse'+index" aria-expanded="false" v-bind:aria-controls="'#flush-collapse'+index">
              <p style="color: white">{{name}} : {{value}}</p>
            </button>
          </h2>
          <div v-bind:id="'flush-collapse'+index" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading'+index" v-bind:data-bs-parent="'#accordionFlushExample'+index">
            <div class="accordion-body">
            <div v-if="name == 'email'">
          <div v-if="value == null">
            <p>Email is not connected you can connect it! </p>

            <div id="email-channel">
              <form @submit.prevent="submit" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                  <div class="d-flex justify-content-center">
                    <div ref="wait_span" class="align-items-center lds-facebook d-none "><div></div><div></div><div></div></div>
                  </div>
                  <div >
                    <h1>{{ verifyMessage }}</h1>
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
            <telegram></telegram>
          </div>
        </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const options = {
  moduleCache: {
    vue: Vue,
  },

  getFile(url) {
    return fetch(url).then(response => response.ok ? response.text() : Promise.reject(response));
  },

  addStyle(styleStr) {
    const style = document.createElement('style');
    style.textContent = styleStr;
    const ref = document.head.getElementsByTagName('style')[0] || null;
    document.head.insertBefore(style, ref);
  },

  log(type, ...args) {
    console.log(type, ...args);
  }
}
const { loadModule, version } = window["vue3-sfc-loader"];
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
        sessionStorage.setItem("secretKey", secretKey)
      }).catch((err) => {
        if(err.response){
          if(err.response.status === 404) {
            this.verifyMessage = "Not found"
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

      }
    }
    await axios({
      baseURL: 'http://127.0.0.1/', // optional
      method: 'post',
      url: '/api/channels',
      headers: {
        'Authorization': 'Bearer 3ec1928e9299157509445424d3884160e928d4e1697a0535b745196ba7a23441'
      }
    }).then((response) => {

      console.warn("HERE: ",response.data)
      this.email = response.data[0].email

      this.telegram = response.data[0].telegram_id
      let telegramName;
      let email;
      if(response.data[0].telegram_id != null){
        telegramName = response.data[0].username
      }
      if(response.data[0].auth_type != 'TG'){
        email = response.data[0].email
      }
      let channels = {'email': email, 'telegram': telegramName}
      this.channels.push(channels);
    }).catch((err) =>{
      console.log(err)
    });

  },
  updated: function (){
    if (window.location.hash) {
      var myCollapse = document.getElementById('flush-collapse0')
      new bootstrap.Collapse(myCollapse, {
        show: true
      })
      if (sessionStorage.getItem("secretKey") != window.location.hash.split("#")[1]){
        this.notify = this.verifyMessage
      }
    }
  },
  components: {
    'telegram': Vue.defineAsyncComponent(() => loadModule('resources/components/tgChannel.vue', options)),
  }
}
</script>
<style>

</style>
