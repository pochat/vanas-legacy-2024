<?php

//initilize the page
require 'lib/general.inc.php';
//require_once("bootstrap/inc/init.php");

//require UI configuration (nav, ribbon, etc.)
//require_once("bootstrap/inc/config.ui.php");

# Recibe parametros
$err = RecibeParametroNumerico('err', True);

# Limpia el cookie
TerminaSesion( );

$page_title = ETQ_TITULO_PAGINA." - Password recovery";

//include header
//you can add your custom css in $page_css array.
//Note: all css files are inside css/ folder
$page_css[] = "your_style.css";
$no_main_header = true;
$page_html_prop = array("id"=>"extr-page", "class"=>"animated fadeInDown");
# Incluimos el header
include(SP_HOME."/AD3M2SRC4/bootstrap/inc/header.php");

?>
  <!-- ==========================CONTENT STARTS HERE ========================== -->
  <!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width-->
  <header id="header">
    <div id="logo-group ">
      <span id="logo" style="margin-top:0;"> <img src="<?php echo PATH_IMAGES; ?>/logo.png" alt="Vanas"> </span>
    </div>
  </header>

  <!-- MAIN -->
  <div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content" class="container">

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-3 hidden-xs hidden-sm"></div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
          <div class="well no-padding">
            <form action="forgot_validate.php" method='post' id="login-form" class="smart-form client-form">
              <header>
                Sign In
              </header>

              <fieldset>

                <section>
                  <label class="label"><?php echo ETQ_USUARIO; ?></label>
                  <label class="input"> <i class="icon-append fa fa-user"></i>
                    <input type="text" id="ds_login" name="ds_login" />
                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter username</b></label>
                </section>

                <section>
                  <label class="label">Email address</label>
                  <label class="input"> <i class="icon-append fa fa-lock"></i>
                    <input type="email" id="ds_email" name="ds_email" />
                    <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
                </section>

                <section>								
                <?php
                # Presenta mensajes de error
                if(!empty($err)) {
                  if($err == '5'){
                    $class = "alert alert-success fade in";                  
                    $fa = " fa-thumbs-o-up ";
                  }
                  else{
                    $class = "alert alert-danger fade in";
                    $fa = "fa-times fa-x";
                  }

                  echo '
                  <div class="'.$class.'">
                    <i class="fa '.$fa.'"></i>
                    <strong>';

                  if(!empty($err)) {
                    switch($err) {
                      case 1: echo "Invalid username or email address.<br>"; break;
                      case 3: echo "The password was not created because there is no email service available.<br>"; break;
                      case 4: echo "Inactive user account.<br>"; break;
                      case 5: echo "A new password has been generated and sent to your email.<br>"; break;
                    }
                  }
                  echo "
                    </strong>
                  </div>";
                }
                ?>       
                </section>
              </fieldset>
              <footer>
                <button type="submit" class="btn btn-primary">
                  Create new password
                </button>
              </footer>
            </form>

          </div>
          <!-- Sitio publico -->
          <h5 class="text-center"><a href='<?php echo PATH_HOME; ?>'>Back</a></h5>        
        </div>
      </div>
    </div>

  </div>
  <!-- END MAIN PANEL -->
  <!-- ==========================CONTENT ENDS HERE ========================== -->

<?php 
	//include required scripts
	include("bootstrap/inc/scripts.php"); 
?>

  <!-- PAGE RELATED PLUGIN(S) 
  <script src="..."></script>-->

  <script type="text/javascript">
    runAllForms();

    $(function() {
      // Validation
      $("#login-form").validate({
        // Rules for form validation
        rules : {
          ds_login : {
            required : true,
            // minlength : 3,
            maxlength : 30
          },
          ds_email : {
            required : true,
            minlength : 3,
            // maxlength : 20
          }
        },

        // Messages for form validation
        messages : {
          ds_login : {
            required : 'Please enter your user address',
            ds_login : 'Please enter a VALID user address'
          },
          ds_email : {
            required : 'Please enter your email'
          }
        },

        // Do not change code below
        errorPlacement : function(error, element) {
          error.insertAfter(element.parent());
        }
      });
    });
  </script>

<?php 
	//include footer
  include("bootstrap/inc/footer.php"); 
?>