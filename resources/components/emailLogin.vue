<template>
	<div id="email-container">
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
</template>
<script>
	export default {
        names: 'location',
		data() {
			return {
				errors: [],
				verifyMessage: '',
				session: null,
				token: null,
				googleRecaptchaSiteKey: null,
				email: null,
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
					axios.post("/auth/email",{email: this.email, token: token}).then((response) =>{
              console.warn(response.data)


            }
          )
					.catch((err) => {
						//console.warn(err.response.data)
					})
				));

			},
			async getKeys(){
				await axios.post("/keys").then(responce => (
					this.googleRecaptchaSiteKey = responce.data.googleRecaptchaSiteKey
				));
			},
      async verify(secretKey){
			  this.verifyMessage = ''
        await axios.get("/auth/email/verify/"+secretKey).then((response) => {
          sessionStorage.setItem('email',response.data.useremail)
          sessionStorage.setItem('sessionKey', response.data.sessionkey)

          this.verifyMessage = 'You are verified'
          this.$refs['emailComponents'].setAttribute("class","d-none")
          this.$refs['emailComponentButton'].setAttribute("class","d-none")
          this.$refs['sessionComponents'].classList.remove("d-none")
        }).catch((err) => {
          if(err.response){
            if(err.response.status === 404) {
                this.verifyMessage = 'Verification key is not found, please try again'
                this.$refs['emailComponents'].classList.remove("d-none")
                this.$refs['emailComponentButton'].classList.remove("d-none")
            }else if (err.response.status === 403){
              this.verifyMessage = 'Verification key is expired, please try again'
              this.$refs['emailComponents'].classList.remove("d-none")
              this.$refs['emailComponentButton'].classList.remove("d-none")
            }
            console.log(err.response)
          }
        });

        //await new Promise(resolve => setTimeout(resolve, 2000));
        console.log(this.resp)
      },
      async gravatar(){
        await axios({
          baseURL: 'http://127.0.0.1/', // optional
          method: 'post',
          url: '/api/gravatar',
          data: {email: sessionStorage.getItem('email')},
          headers: {
            Authorization: 'Bearer ' + this.session
          }
        }).then((response) => {
          console.warn(response)
        }).catch((err) =>{
          console.warn(err)
        })
      },
		},
		mounted: async function(){

      this.verifyMessage = ''
			await this.getKeys();
			const script = document.createElement('script');
			script.src = "https://www.google.com/recaptcha/api.js?render="+this.googleRecaptchaSiteKey
			document.body.insertBefore(script,document.getElementById('vuescript'));
			if (sessionStorage.getItem("sessionKey")){
        window.location.href = "/profile";
      }
      if (window.location.hash) {
        this.$refs['wait_span'].classList.remove("d-none")
        this.$refs['emailComponents'].setAttribute("class","d-none")
        this.$refs['emailComponentButton'].setAttribute("class","d-none")
        let secretKey = window.location.hash.split("#")[1];
        if (secretKey) {
          await this.verify(secretKey);
          this.session = sessionStorage.getItem("sessionKey")
          await this.gravatar();
          this.$refs['wait_span'].setAttribute("class", "d-none")
        }
      }
		},
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
    background: #fff;
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

</style>
