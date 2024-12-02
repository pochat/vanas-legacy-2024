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

$page_title = ETQ_TITULO_PAGINA." - Login";

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
      <!--<span id="logo" style="margin-top:0;"> <img src="<?php echo PATH_IMAGES; ?>/logo.png" alt="Vanas"> </span>-->
      <span id="logo" style="margin-top:0;"> <img src="<?php echo PATH_IMAGES; ?>/header_vanas_adm.png" alt="Vanas"> </span>
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
            <form action="login_validate.php" method='post' id="login-form" class="smart-form client-form">
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
                  <label class="label"><?php echo ObtenEtiqueta(123); ?></label>
                  <label class="input"> <i class="icon-append fa fa-lock"></i>
                  <input type="password" id="ds_password" name="ds_password" />
                  <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
                  <div class="note">
                    <a href="<?php echo PAGINA_OLVIDO; ?>"><?php echo ObtenEtiqueta(75); ?></a>
                  </div>
                </section>

                <section>								
                <?php
                  # Presenta mensajes de error
                  if(!empty($err)) {
                    echo '
                    <div class="alert alert-danger fade in">
                    <i class="fa fa-times fa-x"></i>
                    <strong>';

                    switch($err) {
                      case 1: echo "Invalid username or password.<br>"; break;
                      case 2: echo "Session expired.<br>"; break;
                      case 3: echo "Session does not exist.<br>"; break;
                      case 4: echo "Inactive user account.<br>"; break;
                    }
                    echo "
                    </strong> Please try again.
                    </div>";
                  }
                ?>       
                </section>
              </fieldset>
              <footer>
                <button type="submit" class="btn btn-primary">
                Sign in
                </button>
              </footer>
            </form>
          </div>
          <!-- Sitio publico -->
          <h5 class="text-center"> - <?php echo ObtenEtiqueta(77); ?> -</h5>
          <ul class="list-inline text-center">
            <li>
              <a href="<?php echo INICIO_W; ?>" class='btn btn-info btn-circle'><i class="fa fa-font"></i></a>
            </li>
          </ul>
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
          ds_password : {
            required : true,
            // minlength : 3,
            maxlength : 20
          },
          quality: {
            required:true
          }
        },

        // Messages for form validation
        messages : {
          ds_login : {
            required : 'Please enter your user address',
            ds_login : 'Please enter a VALID user address'
          },
          ds_password : {
            required : 'Please enter your password'
          },
          quality: {
            required : 'erorrrr'
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