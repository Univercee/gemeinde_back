<template>
    <div class="container">
        <div id="email-container">
            <form @submit="submit" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail address</label>
                    <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" v-model="cred.email" required>
                    <div class="invalid-feedback">
                        <p v-if="errors.length">
                        <ul>
                            <li v-for="error in errors">{{ error }}</li>
                        </ul>
                        </p>
                    </div>
                </div>
                <button type="submit"  class="btn btn-primary">Submit</button>
            </form>
        </div>
		<div ref='recaptcha'></div>
    </div>
</template>
<script>
	export default {
        names: 'location', data() {
			return {
				errors: [],
				token: null,
				googleRecaptchaSiteKey: null,
				cred: {
					email: null,
				},
			}
		},
		methods: {
			submit(e) {
				var regex = new RegExp('(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\\])');
				if((regex.test(this.cred.email)) == false){
					this.errors.push('email is bad')
					document.getElementById("email").classList.add("is-invalid")
				}else{
					document.getElementById("email").classList.remove("is-invalid")
				}
				e.preventDefault();
				const email = this.cred.email
				grecaptcha.execute(this.googleRecaptchaSiteKey, {action: 'submit'}).then(function(token) {
				axios.post("/auth/email",{email: email, token: token})
					.catch((err) => {
						console.warn(err.response.data)
					});
				});
			},
			async getKeys(){
				await axios.post("/keys").then(responce => (
					this.googleRecaptchaSiteKey = responce.data.googleRecaptchaSiteKey
				));
			}
		},
		mounted: async function(){
			await this.getKeys();
			const script = document.createElement('script');
			script.src = "https://www.google.com/recaptcha/api.js?render="+this.googleRecaptchaSiteKey
			document.body.insertBefore(script,document.getElementById('vuescript'))
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
</style>