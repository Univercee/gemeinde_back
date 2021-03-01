<template id="megaicons-component">
  <div class="row text-center">
    <div class="col-md-4">
      <h2>Trusted</h2>
      <span class="megaicon megaicon-trusted"></span>
      <p class="mt-2">Get notified about official information and important event notifications sent by local authorities or government agencies.</p>
    </div>

    <div class="col-md-4">
      <h2>Relevant</h2>
      <span class="megaicon megaicon-relevant"></span>
      <p class="mt-2">Receive only approved content from your neighbors, alerts from your service providers and offers from local businesses you opted for.</p>
    </div>

    <div class="col-md-4">
      <h2>Personal</h2>
      <span class="megaicon megaicon-personal"></span>
      <p class="mt-2">Choose between instant delivery or scheduled digest to your favorite messenger, e-mail, smart device, or SMS.</p>
    </div>
  </div>
</template>

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
