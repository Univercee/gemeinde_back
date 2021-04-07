<template>
  <div id="test">
    <h1>TEST</h1>
    {{ $t("message.hello") }}
  </div>
</template>

<script>
export default {
name: "test",
  data() {
    return {
      lang: null,
      test: null,
    }
  }, methods: {
    langDetect(){
      let href = window.location.href.split("/").pop()
      if(href == 'en' || href == 'de') {
        this.lang = href
      }else {
        this.lang = navigator.language.split('-')[0]
      }
      return this.lang
    }
  }, mounted(){
    this.test = this.langDetect()
    alert(this.test)
  }
}
</script>

<style scoped>

</style>
