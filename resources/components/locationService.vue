<template>
  <div id="services-container">
    <h1></h1>
    <div class="accordion-item" v-for="(value) in services">
      <div class="form-check form-switch">
        <template v-if="value.channel">
        <input class="form-check-input" type="checkbox" v-bind:id="value.name" checked>
        </template>
        <template v-else>
          <input class="form-check-input" type="checkbox" v-bind:id="value.name">
        </template>
        <label class="form-check-label" v-bind:for="value.name">{{value.name}}</label>
      </div>

      <div class="d-none">{{id = value.frequency}}</div>
      <div class="d-none">{{channel = value.channel}}</div>
      <select class="form-select" aria-label="Default select example">
        <template v-for="(value, name, index) in channels">
          <template v-if="channel == 'E' && name =='email' ">-->
            <option selected>{{value}} selected</option>
          <</template>
          <template v-else>
            <option>{{value}} </option>
          </template>
        </template>
      </select>
      <select class="form-select" aria-label="Default select example">
        <template v-for="(value, name, index) in frequencies">
          <template v-if="id == name">
            <option selected> {{value}} </option>
          </template>
          <template v-else>
            <option> {{value}} </option>
          </template>
        </template>
      </select>
    </div>
  </div>
</template>

<script>

export default {
  names: 'locationService',
  data() {
    return {
      errors: [],
      services: [],
      channels: [],
      channel: null,
      id: null,
      frequencies: {'daily_brief': 'Daily brief', 'daily_digest': 'Daily digest', 'weekly_brief': 'Weekly brief', 'weekly_digest': 'Weekly digest'},
      freq: [],
    }
  },
  methods: {
    async submit() {
      this.errors = [] //added to clean array each time btn is pressed to flush old errs
    },
    async verify(secretKey){

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
          //this.channels.push(telegramName)
        }
        if(response.data[0].email != null){
          email = response.data[0].email
          //this.channels.push(email)
        }
        this.channels = {'email': email, 'telegram': telegramName}

      }).catch((err) =>{
        console.log(err)
      });
    },
    async getServices(){
      await axios({
        baseURL: 'http://127.0.0.1/', // optional
        method: 'post',
        url: '/api/services',
        headers: {
          'Authorization': 'Bearer 3179d8aa1b259f2f8066840c5d154e53abe359346cbdd8c2d4301014c5eeb6b9'
        }
      }).then((response) => {
        console.log(response.data['results'])
        console.log(response.data['frequency'])
        this.freq = response.data['frequency']
        this.services = response.data['results']
      }).catch((err) =>{
        console.log(err)
      });
    },

  },

  mounted: async function(){
    await this.getServices()
    await this.getChannels()
  },
  updated: function (){

  },
  components: {

  }
}
</script>

<style scoped>

</style>
