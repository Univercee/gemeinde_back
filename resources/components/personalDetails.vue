<template>
    <div>
        <h3>Personal details</h3>
        <form @submit.prevent="updatePersonalDetails()" action="POST">
          <div class="container m-0 p-0">
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="firstname">First name</label>
              </div>
              <div class="col-8">
                <input type="text" id="firstname" v-model="firstname" ref="firstname">
              </div>
            </div>
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="lastname">Last name</label>
              </div>
              <div class="col-8">
                <input type="text" id="lastname" v-model="lastname" ref="lastname">
              </div>
            </div>
            <div class="form-group row mb-2">
              <div class="col-4">
                <label for="language">Language</label>
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
            }).then(()=>{
                this.fetchPersonalDetails()
            }).catch(()=>{
                this.firstname = old_firstname
                this.lastname = old_lastname
                this.language = old_language
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
        }
    },
    async mounted() {
        this.fetchPersonalDetails()
    },
}
</script>
