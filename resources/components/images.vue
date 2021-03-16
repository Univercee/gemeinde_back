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
          'Authorization': 'Bearer 26f26a10d759475837bfb3cfb9467ec611725b8001f96778904a597c038f07b4'
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
