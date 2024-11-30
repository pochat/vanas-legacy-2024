<link rel="stylesheet" type="text/css" media="screen" href="site/stripe.css" >
<form id="form_stripe">
  <div class="group">
    <label>
      <span>Name</span>
      <input name="cardholder-name" class="field" placeholder="Jane Doe" />
    </label>
    <label>
      <span>Phone</span>
      <input class="field" placeholder="(123) 456-7890" type="tel" />
    </label>
  </div>
  <div class="group">
    <label>
      <span>Card</span>
      <div id="card-element" class="field"></div>
    </label>
  </div>
  <button type="submit">Pay $25</button>
  <div class="outcome">
    <div class="error"></div>
    <div class="success">
      Success! Your Stripe token is <span class="token"></span>
    </div>
  </div>
</form>

