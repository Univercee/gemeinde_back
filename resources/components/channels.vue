<template>
  <div id="channels-container">
    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item">
        <h2 class="accordion-header" v-bind:id="'flush-heading-email'">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse-email'" aria-expanded="false" v-bind:aria-controls="'#flush-collapse-email'">
            <p>Email: {{email??'not connected'}}</p>
          </button>
        </h2>
        <div v-bind:id="'flush-collapse-email'" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading-email'" v-bind:data-bs-parent="'#accordionFlushExample'">
          <div class="accordion-body">
            <div v-if="email">
              <button v-on:click="deleteEmailChannel()" role="button" class="btn btn-primary btn-sm">Disconnect</button>
            </div>
            <div v-else>
              <emailChannel :isChannel="true"></emailChannel>
            </div>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" v-bind:id="'flush-heading-tg'">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse-tg'" aria-expanded="false" v-bind:aria-controls="'#flush-collapse-tg'">
            <p>Telegram: {{tg??'not connected'}}</p>
          </button>
        </h2>
        <div v-bind:id="'flush-collapse-tg'" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading-tg'" v-bind:data-bs-parent="'#accordionFlushExample'">
          <div class="accordion-body">
            <div v-if="tg">
              <button v-on:click="deleteTgChannel()" role="button" class="btn btn-primary btn-sm">Disconnect</button>
            </div>
            <div v-else>
              <tgChannel :isChannel="true" v-on:verifyTgChannel="getChannels()"></tgChannel>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import tgChannel from './tgLogin.vue'
import emailChannel from './emailLogin.vue'
export default {
  components: {
    tgChannel,
    emailChannel
  },
  data() {
    return {
      tg:null,
      email:null
    }
  },
  methods: {
    async getChannels(){
      await axios.get("/profile/channels").then(response => (
        this.tg = response.data.telegram_username,
        this.email = response.data.email
      ))
    },

    async deleteEmailChannel(){
      axios({
        method: 'delete',
        url: '/profile/channels/email/delete'
      }).then(async ()=>{
        await this.getChannels()
      })
    },

    async deleteTgChannel(){
      axios({
        method: 'delete',
        url: '/profile/channels/tg/delete'
      }).then(async ()=>{
        await this.getChannels()
      })
    },
  },
  async mounted(){
    await this.getChannels()
  }
}
</script>
<style>

</style>
