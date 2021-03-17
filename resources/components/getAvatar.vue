<template>
  <div id="get-avatar-container">
    <form @submit.prevent="submit" method="post">
      Submit to get image
      <input type="submit" value="View image" name="submit">
    </form>
    <img id="image" src="">
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
      await axios({
        baseURL: 'http://127.0.0.1/', // optional
        method: 'post',
        url: '/api/getavatar',
        headers: {
          'Authorization': 'Bearer 26f26a10d759475837bfb3cfb9467ec611725b8001f96778904a597c038f07b4'
        }
      }).then((response) => {
        document.getElementById("image").setAttribute("src", response.data.fromdb)
        console.warn("HERE: ",response.data.fromdb)
      }).catch((err) =>{
        console.log(err)
      });

    },
  },

}
</script>
<style>

</style>
