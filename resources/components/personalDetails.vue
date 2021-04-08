<template>
    <div>
        <h3>{{$root.t('personalDetails_1')}}</h3>
        <form @submit.prevent="updatePersonalDetails()" action="POST">
          <div class="container m-0 p-0">
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="firstname">{{$root.t('personalDetails_2')}}</label>
              </div>
              <div class="col-8">
                <input type="text" id="firstname" v-model="firstname" ref="firstname">
              </div>
            </div>
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="lastname">{{$root.t('personalDetails_3')}}</label>
              </div>
              <div class="col-8">
                <input type="text" id="lastname" v-model="lastname" ref="lastname">
              </div>
            </div>
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="language">{{$root.t('personalDetails_4')}}</label>
              </div>
              <div class="col-8">
                <select v-model="language" ref="language">
                    <option value="en">English</option>
                    <option value="de">Deutsch</option>
                </select>
              </div>
            </div>
            <div class="row">
              <input type="submit" class="btn btn-block btn-outline-primary">
            </div>
          </div>
        </form>
    </div>
</template>

<i18n>
{
  "en":{
    "personalDetails_1":"Personal details",
    "personalDetails_2":"First name",
    "personalDetails_3":"Last name",
    "personalDetails_4":"Language"
  },
  "de":{
    "personalDetails_1":"Personal details",
    "personalDetails_2":"First name",
    "personalDetails_3":"Last name",
    "personalDetails_4":"Language"
  }
}
</i18n>

<script>
export default {
    data() {
        return {
            firstname:null,
            lastname:null,
            language:null
        }
    },
    methods: {
        async updatePersonalDetails(){
            let old_firstname = this.firstname
            let old_lastname = this.lastname
            let old_language = this.language
            await axios({
                method: 'post',
                url: '/profile/personalDetails',
                data:{
                    firstname:this.firstname,
                    lastname:this.lastname,
                    language:this.language
                }
            }).then(response=>{
                this.fetchPersonalDetails()
                this.$i18n.locale = this.language
                this.notify(response.data.message, false)
            }).catch(err=>{
                this.firstname = old_firstname
                this.lastname = old_lastname
                this.language = old_language
                this.notify(err.response.data.message, true)
            })
        },
        fetchPersonalDetails(){
            axios({
                method: 'get',
                url: '/profile/personalDetails'
            }).then((response)=>{
                this.firstname = response.data.firstname,
                this.lastname = response.data.lastname,
                this.language = response.data.language
            })
        },
        notify(message, isError){
            this.$emit('notify', message, isError)
        }
    },
    async mounted() {
        this.fetchPersonalDetails()
    },
}
</script>
