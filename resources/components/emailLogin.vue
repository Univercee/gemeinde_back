<template>
	<div>
		<form @submit.prevent="submit" method="post" class="needs-validation" novalidate>

      <div v-if="state=='wait'">
        <div class="d-flex justify-content-center mb-3">
          <div class="align-items-center lds-facebook"><div></div><div></div><div></div></div>
        </div>
			</div>

      <div v-if="state=='answer'">

          <p class="backendMsg">{{ backendMessage }}</p>
        <img src="/resources/assets/images/greentick.png" class="email-img">
        <div>
          <a href="/profile" role="button" class="btn form-control btn-primary btn-sm">Continue</a>
        </div>
      </div>

      <div v-if="state=='input'">
        <div class="mb-3">
          <div>
            <label for="email" class="form-label form-label-sm" :class="{'d-none':!email}"><small>E-mail address</small></label>
            <input type="email" class="form-control form-control-sm" :class="{'is-invalid':errors}" placeholder="E-mail address" aria-describedby="emailHelp" v-model="email" required>
          </div>
          <div class="invalid-feedback" :class="{'d-block':errors}">
              <ul>
                <li :key="error" v-for="error in errors">{{ error }}</li>
              </ul>
          </div>
        </div>
        <div>
            <button role="button" class="btn btn-primary btn-sm">Continue</button>
        </div>
      </div>

      <div v-if="state=='sent'">
        <p class="text-center">{{backendMessage}}</p>
        <img src="/resources/assets/images/envelope.png" class="email-img">
<!--        <div class="alert alert-success alert-dismissible fade show" role="alert">-->
<!--            <strong>Email is sent!</strong> Please check your email.-->
<!--            <button v-on:click="state='input'" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
<!--        </div>-->
      </div>

      <div v-if="state=='error'">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{message}}</strong>
            <button v-on:click="state='input'" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>

		</form>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				errors: null,
				message: null,
        backendMessage: null,
				googleRecaptchaSiteKey: null,
				email: null,
        state: null
			}
		},
    props:['isChannel'],
		methods: {
			async submit() {
				this.errors = []
        let regex = new RegExp('(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\\])')
				if(!(regex.test(this.email))){
					this.errors.push('email is bad')
					return false; //otherwise execution goes on to axios
				}
        this.setState('wait');
        if(this.$props.isChannel){
          let url = '/profile/channels/email';
          axios.post(url,{email: this.email})
            .then((response) =>{

              this.setState('sent')
            })
					.catch((err) => {
						this.setState('error', 'Something goes wrong. Please, try again')
            console.warn(err)

          });
        } else {
          let url = '/auth/email';
          await grecaptcha.execute(this.googleRecaptchaSiteKey, {action: 'submit'}).then((token) => (
					  axios.post(url,{email: this.email, token: token})
              .then((response) =>{
               console.warn(response.data.message)
                this.backendMessage = response.data.message
                this.setState('sent')
              })
					.catch((err) => {
						this.setState('error', 'Something goes wrong. Please, try again')
					})
				));

        }
			},
      async verify(secretKey){
        await axios.get('/auth/email/verify/'+secretKey).then((response) => {
          console.warn(response.data)
          sessionStorage.setItem('sessionKey', response.data.sessionkey)
          if(response.data.status == 'logged'){
            document.location.href = '/profile'
            return true
          }
          this.backendMessage = response.data.message
          this.setState('answer')
        }).catch((err) => {
          if(err.response.status === 404) {
            console.warn(err.response.data.error)
            this.setState('error', err.response.data.error)
          }else if (err.response.status === 403){
            this.setState('error', err.response.data.error)
          }
          else{
            this.setState('error', 'Unable to identify error')
          }
        });
      },
      async verifyChannel(secretKey){
        await axios.post('/profile/channels/email/verify/'+secretKey)
        this.setState('input')
      },
      setState(state, message){
        this.message = message
        this.state = state
      },
      initRecaptcha(){
        const script = document.createElement('script');
        script.src = "https://www.google.com/recaptcha/api.js?render="+this.googleRecaptchaSiteKey
        document.getElementById('vueapp').after(script)
      },
      async getKeys(){
				await axios.get("/keys").then(responce => (
					this.googleRecaptchaSiteKey = responce.data.googleRecaptchaSiteKey
				));
			},
		},

		async mounted(){
      if (window.location.hash) {
        if (this.state != 'answer') {
          this.setState('wait')
        }
        let secretKey = window.location.hash.split("#")[1];
        if (secretKey) {
          if(this.$props.isChannel){
            await this.verifyChannel(secretKey);
          }else{
            await this.verify(secretKey);
          }
        }
      }
      if(!this.$props.isChannel) {
	  		await this.getKeys();
        this.initRecaptcha()
      }
      if(this.state == null) {
        this.setState('input')
      }
      console.log(this.$props.isChannel);
		}
	}
</script>
<style>
	#location_name::first-letter {
		text-transform: uppercase;
		color: red;
	}
	#modalLabel_location{
		margin-right: 5px;
	}

	button {
		width: 100%;
	}
  .lds-facebook {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
  }
  .lds-facebook div {
    display: inline-block;
    position: absolute;
    left: 8px;
    width: 16px;
    background: #EEE;
    animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
  }
  .lds-facebook div:nth-child(1) {
    left: 8px;
    animation-delay: -0.24s;
  }
  .lds-facebook div:nth-child(2) {
    left: 32px;
    animation-delay: -0.12s;
  }
  .lds-facebook div:nth-child(3) {
    left: 56px;
    animation-delay: 0;
  }
  @keyframes lds-facebook {
    0% {
      top: 8px;
      height: 64px;
    }
    50%, 100% {
      top: 24px;
      height: 32px;
    }
  }
.email-img{
  display: block;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 15px;
  width: 50%;
}

</style>
