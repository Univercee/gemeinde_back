<template id="megaicons-component">
  <div class="row text-center">
    <div class="col-md-4">
      <h2>{{$root.t('megaicons_1.title')}}</h2>
      <span class="megaicon megaicon-trusted"></span>
      <p class="mt-2">{{$root.t('megaicons_1.content')}}</p>
    </div>

    <div class="col-md-4">
      <h2>{{$root.t('megaicons_2.title')}}</h2>
      <span class="megaicon megaicon-relevant"></span>
      <p class="mt-2">{{$root.t('megaicons_2.content')}}</p>
    </div>

    <div class="col-md-4">
      <h2>{{$root.t('megaicons_3.title')}}</h2>
      <span class="megaicon megaicon-personal"></span>
      <p class="mt-2">{{$root.t('megaicons_3.content')}}</p>
    </div>
  </div>
</template>

<i18n>
{
  "en":{
    "megaicons_1":{
      "title":"Trusted",
      "content":"Get notified about official information and important event notifications sent by local authorities or government agencies."
    },
    "megaicons_2":{
      "title":"Relevant",
      "content":"Receive only approved content from your neighbors, alerts from your service providers and offers from local businesses you opted for."
    },
    "megaicons_3":{
      "title":"Personal",
      "content":"Choose between instant delivery or scheduled digest to your favorite messenger, e-mail, smart device, or SMS."
    }
  },
  "de":{
    "megaicons_1":{
      "title":"Trusted",
      "content":"Get notified about official information and important event notifications sent by local authorities or government agencies."
    },
    "megaicons_2":{
      "title":"Relevant",
      "content":"Receive only approved content from your neighbors, alerts from your service providers and offers from local businesses you opted for."
    },
    "megaicons_3":{
      "title":"Personal",
      "content":"Choose between instant delivery or scheduled digest to your favorite messenger, e-mail, smart device, or SMS."
    }
  }
}
</i18n>

<style>
  .megaicon {
    display: inline-block;
    width: 100px;
    height: 100px;
    background-size: cover;
  }

  .megaicon-trusted {
    background-image: url(../resources/assets/images/trusted.svg);
  }

  .megaicon-relevant {
    background-image: url(../resources/assets/images/relevant.svg);
  }

  .megaicon-personal {
    background-image: url(../resources/assets/images/personal.svg);
  }
</style>
