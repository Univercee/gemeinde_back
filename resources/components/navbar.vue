<template>
	<nav class="navbar navbar-dark fixed-top bg-dark px-5">
		<a class="navbar-brand me-md-auto" href="#">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
				<path d="m0 0h32v32h-32z" fill="#da291c"/>
				<path d="m13 6h6v7h7v6h-7v7h-6v-7h-7v-6h7z" fill="#fff"/>
			</svg>
			<span class="h5 ms-1">Gemeinde Online</span>
		</a>
		<a v-if="!isAuth" class="me-4" href="/signup">Einloggen</a>
		<a v-if="!isAuth" class="btn btn-primary" href="/signup">Anmelden</a>
    <a v-if="isAuth" class="btn btn-primary" @click.prevent="logout" href="#logout">Log out</a>
	</nav>
</template>

<script>
export default {
    data() {
        return {
            isAuth: false
        }
    },
    methods: {
      logout() {
        sessionStorage.removeItem('sessionKey');
        window.location.href = '/';
      }
    },
    created() {
        if(sessionStorage.getItem('sessionKey')) this.isAuth = true;
    }
}
</script>
