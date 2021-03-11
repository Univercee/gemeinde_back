<template>
	<div id="email-container">
		<form @submit.prevent="submit" method="post" class="needs-validation" novalidate>
			<div class="mb-3">
				<div id="loader" class="" role="status">
					<span class="visually-hidden">Loading...</span>
					<span id="wait_span">Please wait</span>
				</div>
				<div v-if="session != null">
					<h1>You are verified proceed to profile</h1>
					<div class="alert alert-primary" role="alert" v-for="message in verifyMessage">
						{{message}}
					</div>
				</div>
				<div v-if="session == null">
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
			<div v-if="session != null">
				<a href="/profile" role="button" class="btn form-control btn-success btn-sm">Continue</a>
			</div>
			<div v-else>
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
				verifyMessage: null,
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
				await grecaptcha.execute(this.googleRecaptchaSiteKey, {action: 'submit'}).then(token => (
					axios.post("/auth/email",{email: this.email, token: token})
					.catch((err) => {
						console.warn(err.response.data)
					})
				));
			},
			async getKeys(){
				await axios.post("/keys").then(responce => (
					this.googleRecaptchaSiteKey = responce.data.googleRecaptchaSiteKey
				));
			},
      async verify(secretKey){
        await axios.get("/auth/email/verify/"+secretKey).then((response) => {
          sessionStorage.setItem('sessionKey', response.data.sessionkey)
          this.verifyMessage.push('You are verified')
        }).catch((err) => {
          console.warn(err.data)
        })
        await new Promise(resolve => setTimeout(resolve, 2000));
        console.log(this.resp)
      }
		},
		mounted: async function(){

			await this.getKeys();
			const script = document.createElement('script');
			script.src = "https://www.google.com/recaptcha/api.js?render="+this.googleRecaptchaSiteKey
			document.body.insertBefore(script,document.getElementById('vuescript'));
      if (window.location.hash) {
        document.getElementById("loader").setAttribute("class", "spinner-border")
        let secretKey = window.location.hash.split("#")[1];
        if (secretKey) {
          await this.verify(secretKey);

          this.session = sessionStorage.getItem("sessionKey")
          document.getElementById("loader").setAttribute("class", "dont-spin")
          document.getElementById("wait_span").setAttribute("class", "d-none")
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
</style>
