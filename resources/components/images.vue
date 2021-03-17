<template>
  <div id="image-container">
  <form @submit.prevent="submit" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" ref="file" name="file" id="file">
    <input type="submit" value="Upload Image" name="submit">
  </form>
  </div>
</template>
<script>
export default {
  names: 'location',
  data() {
    return {
      file: '',
      session: null,
    }
  },
  methods: {
    async submit() {
      await this.handleFileUpload();
      let formData= new FormData();
      console.warn(this.file)
      formData.append('file', this.$refs.file.files[0]);

      await axios({
        baseURL: 'http://127.0.0.1/', // optional
        method: 'post',
        url: '/api/file',
        data: formData,
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': 'Bearer a0158303104b3ddf43b7a20cd26e3a83f6148d603854d238f9aeb412216635b2'
        }
      }).then((response) => {
        console.warn(formData)
        console.warn("HERE: ",response)
      }).catch((err) =>{
        console.log(err)
      });

    },
    async  handleFileUpload() {
      this.file = this.$refs.file.files[0];
      console.log('>>>> 1st element in files array >>>> ', this.file);
    }
  },
  mounted: async function(){

  },
}
</script>
<style>

</style>
