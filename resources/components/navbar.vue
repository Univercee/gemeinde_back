<template>
	<nav class="navbar navbar-dark bg-dark fixed-top px-5">
		<a class="navbar-brand me-md-auto" href="#">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
				<path d="m0 0h32v32h-32z" fill="#da291c"/>
				<path d="m13 6h6v7h7v6h-7v7h-6v-7h-7v-6h7z" fill="#fff"/>
			</svg>
			<span class="h5 ms-1">Gemeinde Online</span>
		</a>
    <div class="lang-wrapper">
      <div class="dropdown dropdown-my" ref="language-dropdown">
        <button class="lang lang-active dropdown-toggle" ref="language-dropdown-btn" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
          {{$i18n.locale.toUpperCase()}}
        </button>
        <ul class="dropdown-menu py-0 lang-list" ref="language-dropdown-list" aria-labelledby="dropdownMenuButton1">
          <a :href="getLangUrl('en')" class="dropdown-item lang-item">
            <div class="lang" :class="$i18n.locale=='en'?'lang-active':''">EN</div>
            <p class="lang-lable">English</p>
          </a>
          <a :href="getLangUrl('de')" class="dropdown-item lang-item">
            <div class="lang" :class="$i18n.locale=='de'?'lang-active':''">DE</div>
            <p class="lang-lable">Deutsch</p>
          </a>
        </ul>
      </div>

    </div>
		<a v-if="!isAuth" class="me-4 sign-in" href="/signup">{{ $root.t('navbar_1') }}</a>
		<a v-if="!isAuth" class="btn btn-primary register" href="/signup">{{ $root.t('navbar_2') }}</a>
    <a v-if="isAuth" class="btn btn-primary" @click.prevent="logout" href="#logout">{{ $root.t('navbar_3') }}</a>
	</nav>
</template>

<i18n>
{
  "en":{
    "navbar_1":"Sign in",
    "navbar_2":"Register",
    "navbar_3":"Log out"
  },
  "de":{
    "navbar_1":"Einloggen",
    "navbar_2":"Anmelden",
    "navbar_3":"Ausloggen"
  }
}
</i18n>

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
      },

      setLang(lang){
        this.$i18n.locale = lang
      },
      getLangUrl(lang){
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('lang',lang)
        return window.location.pathname+'?'+urlParams
      },
      initDropdown(){
        this.$refs['language-dropdown'].addEventListener('mouseover', ()=>{
          this.$refs['language-dropdown-btn']['aria-expanded'] = true
          this.$refs['language-dropdown-btn'].classList = 'lang lang-active dropdown-toggle show'
          this.$refs['language-dropdown-list'].classList = 'dropdown-menu py-0 lang-list show'
          this.$refs['language-dropdown-list']['data-bs-popper'] = 'none'
        })
        this.$refs['language-dropdown'].addEventListener('mouseleave', ()=>{
          this.$refs['language-dropdown-btn']['aria-expanded'] = false
          this.$refs['language-dropdown-btn'].classList = 'lang lang-active dropdown-toggle'
          this.$refs['language-dropdown-list'].classList = 'dropdown-menu py-0 lang-list'
          delete this.$refs['language-dropdown-list']['data-bs-popper']
        })
      }
    },
    created() {
      if(sessionStorage.getItem('sessionKey')) this.isAuth = true;
    },
    mounted() {
      this.initDropdown()
    },
}
</script>

<style>
  .lang{
    width: 50px;
    height: 50px;
    color: #2F80ED;
    background-color: transparent;
    font-family: Roboto;
    font-size: 24px;
    font-style: normal;
    font-weight: 500;
    line-height: 44px;
    letter-spacing: 0.6499999761581421px;
    text-align: center;
    border: solid #2F80ED 3px;
    margin-right: 0.7em;
  }
  .lang::after{
    content: none;
  }
  .lang-wrapper{
    display: flex;
    margin-right: 2em;
  }
  .lang-active{
    color: #fff;
    background-color: #2F80ED;
  }
  .lang-item{
    cursor: pointer;
    display: flex;
    padding: 0.5em;
  }
  .lang-item:hover .lang{
    color: #fff;
    background-color: #2F80ED;
  }
  .lang-lable{
    font-family: Roboto;
    font-size: 18px;
    font-style: normal;
    font-weight: 500;
    line-height: 55px;
    letter-spacing: 0.6499999761581421px;
    text-align: left;
    padding: 0;
    margin: 0;
  }
  .dropdown-my{
    width: 50px;
    height: 50px;
  }

  .sign-in{
    font-family: Roboto;
    font-size: 18px;
    font-style: normal;
    font-weight: 500;
    line-height: 48px;
    letter-spacing: 0.6499999761581421px;
    text-align: center;
  }

  .register{
    font-family: Roboto;
    font-size: 18px;
  }

  @media(max-width: 480px) {
    .lang{
      margin: 0;
    }
    .lang-lable{
      display: none;
    }
    .lang-list{
      min-width: max-content;
    }
  }
</style>