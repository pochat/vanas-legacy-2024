		<!-- Menu active -->
		<script type="text/javascript">
      // Obtenemos mdulo y funcion actual
      var act_mod = $("#act_mod").val();
      var act_fun = $("#act_fun").val();       
      // Open modulo
      $("#mod_"+act_mod).addClass("active open");
      // Active funcion
      $("#fun_"+act_fun).addClass("active");
      // Buscamos el icono del modulo
      var clase = $("#icono_"+act_mod).attr("class");
      // Agregramos el icono
      $("#icono_modulo").addClass(clase); 
		</script>
		<!-- End Menu Active -->

		<!-- Your GOOGLE ANALYTICS CODE Below -->
<!--                
		<script type="text/javascript">
			var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
				_gaq.push(['_trackPageview']);
			
			(function() {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();

		</script>
                -->

	</body>

</html>