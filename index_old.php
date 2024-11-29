<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  
  # Variables del template de pagina de inicio
  $vid_archivo_01 = ObtenNombreImagen(200);
  $vid_width_01   = ObtenConfiguracion(13);
  $vid_height_01  = ObtenConfiguracion(14);
  
  # Inicia cuerpo del home
  PresentaInicioPagina(True);
  echo "
<table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center' width='1024'>
	<tr>
		<td width='31' class='outline_left'></td>
		<td>
			<table border='".D_BORDES."' cellpadding='0' cellspacing='0' >
				<tr>
					<td rowspan='2' width='241' valign='top' class='menu'>
						&nbsp;\n";
  PresentaMenu(SEC_HOME);
  echo "
					</td>
					<td width='360' height='38' class='announcement_top'>&nbsp;</td>
					<td width='360' class='title'>".ETQ_TIT_PAG."</td>
				</tr>
				<tr>
					<td colspan='2' height='".ObtenConfiguracion(14)."'>";
  PresentaVideo($vid_archivo_01, $vid_width_01, $vid_height_01);
  echo "</td>
				</tr>
			</table>
			<table border='".D_BORDES."' cellpadding='0' cellspacing='0' >
				<tr>
					<td width='720' height='290' valign='top' class='news'>";
  PresentaNoticias( );
  echo "</td>
					<td width='241' class='newsletter_back' valign='top'>";
  PresentaBoletin( );
  echo "</td>
				</tr>
			</table>
		</td>
		<td width='32' class='outline_right'></td>
	</tr>
</table>
<table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='1024' align='center'>
	<tr>
		<td width='31' class='outline_left'></td>
		<td class='footer_bar'>\n";
  PresentaMenuInferior( );
	echo "
		</td>
		<td width='32' class='outline_right'></td>
	</tr>
	<tr>
		<td colspan='9' height='30' class='outline_bottom'>\n";
  PresentaLigasFooter( );
	echo "
		</td>
	</tr>
</table>
</body>
</html>";
  
?>