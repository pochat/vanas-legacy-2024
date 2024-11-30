<?php
  # Libreria de funciones
	require("../../lib/cam_general.inc.php");
  
?>  
  
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    if (response.status === 'connected') {
        // Logged into your app and Facebook.        
        $('#conected_facebook').css('display','none');
        $('#desconected_facebook').css('display','inline');
        API();        
    } else if (response.status === 'not_authorized') {
        // The person is logged into Facebook, but not your app.
        $('#conected_facebook').css('display','inline');
        FB.api('/me', function(response) {
          /*document.getElementById('status_face').innerHTML =
          'estas logeado pero no con esta app';*/
        });
    } else {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
        $('#conected_facebook').css('display','inline');
        $('#desconected_facebook').css('display','none');
        FB.api('/me', function(response) {
          document.getElementById('status_face').innerHTML =
          "<strong><?php echo ObtenEtiqueta(787);?></strong>";
        });       
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function loginn() {
      FB.login(function(){
        FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
          if(response.status!='connected')
          window.location.reload();
        },true);
      },{scope: 'email'});
  }
  
  function logout(){
    FB.logout(function(response) {
      statusChangeCallback(response);
    });    
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '<?php echo ObtenConfiguracion(76); ?>',
    cookie     : true,  // enable cookies to allow the server to access the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.2' // use version 2.2
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    },true);
  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  // compartimos lo que suben los estudiantes
  function Shareface(message2,link2,picture2,name2,caption2,description2,entregable){
     FB.api('/me/feed','POST',
     {message:message2,link:link2,picture:picture2,name:name2,caption:caption2,description:description2},
     function(response){
      user_share_face_save(response.id,type='S',entregable);
     });
  }

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function API() {
    FB.api('/me', function(response) {
        document.getElementById('status_face').innerHTML =
        "<strong><?php echo ObtenEtiqueta(786); ?>:</strong><br />"+response.name;
        user_share_face_save(response.id,type='L');
    });
  }
  
  function statusface(){
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    },true);    
  }
  
  function user_share_face_save(id,type,entregable=''){
    $.ajax({
      type: "POST",
      url: "ajax/savefaceuser.php",
      data: "id_face_user="+id+"&type="+type+"&entregable="+entregable
    });    
  }
  
 