<?php
require_once(dirname(__FILE__)."/../settings.php");

class CGeneratePage
{
    public function Generate()
	{
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
		
		$this->GenerateTitle();
		$this->GenerateHead();
		$this->GenerateBody();
		echo "</html>\n";
		
		$this->GenerateJs();
	}
	// Overrides
	protected function GenerateModule()   { }
	protected function GenerateHeadData() { }
	protected function GenerateJsData()   { }
	protected function GenerateTitle()    { echo "<title>" . get_system_name() . get_version() . "</title>\n"; }
	
	private function GenerateBody()
	{
		$host = get_server_host_name();
		$logo_img = get_logo_img_full_path();
	
		echo "<body>\n";
			echo "<table border=0>\n";
				echo "<col width=300 />";
				echo "<col width=300 />";
				echo "<col width=300 />";
					echo "<tr>\n";
						if ( strlen($host) > 0 )
						{
							echo "<td width=400>\n";
								echo "<font color='#448800'> $host</font>";
							echo "</td>\n";
						}
						if ( strlen($logo_img) > 0 )
						{
							echo "<td width=100> <img src='$logo_img'> </td>\n";
						}
					echo "</tr>\n";
			echo "</table>\n";
			
			$this->GenerateModulesPanel();
			$this->GenerateModule();
		echo "</body>";		
	}

	private function GenerateHead()
	{	
		echo "<head>\n";
			echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
			echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=UTF-8\">\n";
			
			$favicon  = '../res/favicon.ico';		
			echo "  <link rel='shortcut icon' href='$favicon' type='image/x-icon'>\n";
			echo "  <link rel='icon'          href='$favicon' type='image/x-icon'>\n";
			$this->GenerateHeadData();
		echo "</head>\n\n";
	}
	
	private function GenerateJs()
	{
		echo "<script>\n";
			echo "function go(url)";
			echo "{";
				echo "document.header_menu.action=url;";
				echo "document.header_menu.submit();";
			echo "}";
		echo "</script>\n";
		
		$this->GenerateJsData();
	}

	private function GenerateModulesPanel()
	{
		chdir(dirname(__FILE__));
		$modules        = glob("../_*");
		$modules_sorted = Array();

		foreach ($modules as $module)
		{
			$mod_conf = "$module/module.cfg";
			$name_txt = is_file("$mod_conf") ? file("$mod_conf") : NULL;
			$mod_prio = isset($name_txt[1])  ? $name_txt[1]      : 500;
			$modules_sorted[$module] = $mod_prio;
		}

		asort($modules_sorted);

		$curr_page = $_SERVER["SCRIPT_NAME"];

		echo "<form name='header_menu' action=''>\n";
		foreach ($modules_sorted as $module => $prio)
		{
			$mod_conf = "$module/module.cfg";
			$name_txt = is_file("$mod_conf") ? file("$mod_conf") : NULL;
			$mod_name = isset($name_txt[0])  ? $name_txt[0]      : str_ireplace('_', '', basename($module));
			$selected = stripos("$module/index.php", $curr_page);

			echo "&nbsp;\n";
			echo " <a href='javascript:go(\"$module/index.php\")'>";
			echo $selected ? "<font size='+1' color='blue'>" : "<font size='+1' color='black'>";
			echo "<b>$mod_name</b></font></a>";
		}
		echo "<hr/>\n";
		echo "</form>\n";
	}
}

?>