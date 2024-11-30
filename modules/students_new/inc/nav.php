<?php
	# Left panel : Navigation area

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
	if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
	  MuestraPaginaError(ERR_SIN_PERMISO);
	  exit;
	}

?>

<aside id="left-panel">
	<div class="login-info">
		<span> 
			<a href="#ajax/profile.php">
				<img src="<?php echo ObtenAvatarUsuario($fl_usuario); ?>"> 
				<span><?php echo ObtenNombreUsuario($fl_usuario); ?></span>
			</a> 
		</span>
	</div>
	<nav>
		<ul>
			<?php
				/*
					NAVIGATION : This navigation is also responsive
					To make this navigation dynamic please make sure to link the node
					(the reference to the nav > ul) after page load. Or the navigation
					will not initialize.

					NOTE: Notice the gaps after each icon usage <i></i>..
					Please note that these links work a bit different than
					traditional hre="" links. See documentation for details.
				*/
				foreach ($page_nav as $key => $nav_item) {
					//process parent nav
					$nav_htm = '';
					$url = isset($nav_item["url"]) ? $nav_item["url"] : "#";
					$icon_badge = isset($nav_item["icon_badge"]) ? '<em>'.$nav_item["icon_badge"].'</em>' : '';
					$icon = isset($nav_item["icon"]) ? '<i class="fa fa-lg fa-fw '.$nav_item["icon"].'">'.$icon_badge.'</i>' : "";
					$nav_title = isset($nav_item["title"]) ? $nav_item["title"] : "(No Name)";
					$label_htm = isset($nav_item["label_htm"]) ? $nav_item["label_htm"] : "";
					$is_ajax = isset($nav_item["ajax"]) && !$nav_item["ajax"] ? 'target = "_top"' : '';
					//$nav_htm .= '<a href="'.$url.'" '.$is_ajax.' title="'.$nav_title.'">'.$icon.' <span class="menu-item-parent">'.$nav_title.'</span>'.$label_htm.'</a>';
					$nav_htm .= '<a href="#" onclick="return false;" title="'.$nav_title.'">'.$icon.' <span class="menu-item-parent">'.$nav_title.'</span>'.$label_htm.'</a>';
					if (isset($nav_item["sub"]) && $nav_item["sub"])
						$nav_htm .= process_sub_nav($nav_item["sub"]);

					echo '<li class=\'open\'>'.$nav_htm.'</li>';
				}

				function process_sub_nav($nav_item) {
					$sub_item_htm = "";
					if (isset($nav_item["sub"]) && $nav_item["sub"]) {
						$sub_nav_item = $nav_item["sub"];
						$sub_item_htm = process_sub_nav($sub_nav_item);
					} else {
						$sub_item_htm .= '<ul style="display: block;">';
						foreach ($nav_item as $key => $sub_item) {
							$url = isset($sub_item["url"]) ? $sub_item["url"] : "#";
							//$icon = isset($sub_item["icon"]) ? '<i class="fa fa-lg fa-fw '.$sub_item["icon"].'"></i>' : "";
							$nav_icon = isset($sub_item["nav_icon"]) ? $sub_item["nav_icon"] : "";
							$nav_title = isset($sub_item["title"]) ? $sub_item["title"] : "(No Name)";
							$label_htm = isset($sub_item["label_htm"]) ? $sub_item["label_htm"] : "";
							$is_ajax = isset($sub_item["ajax"]) && !$sub_item["ajax"] ? 'target = "_top"' : '';
							$sub_item_htm .= 
								'<li>
									<a href="'.$url.'" '.$is_ajax.'>'.$nav_icon.' '.$nav_title.$label_htm.'</a>
									'.(isset($sub_item["sub"]) ? process_sub_nav($sub_item["sub"]) : '').'
								</li>';
						}
						$sub_item_htm .= '</ul>';
					}
					return $sub_item_htm;
				}
			?>
		</ul>

	</nav>
	<!-- <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span> -->
</aside>