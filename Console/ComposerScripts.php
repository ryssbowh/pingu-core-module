<?php

namespace Pingu\Core\Console;

use Composer\Script\Event;
use Pingu\Core\Exceptions\MysqlException;

class ComposerScripts
{

	public static function install()
	{
		if(file_exists(".env")) return false;

		echo "Let's install Pingu !\n";

		echo "Enter the site url:\n";
		$url = rtrim(fgets(STDIN));
		if(substr($url, 0, 7) != 'http://'){
			$url = 'http://'.$url;
		}

		echo "Enter your database details\n";

		$dbdriver = 'mysql';

		$dbhost = '';
		while($dbhost == ''){
			echo "Host: ";
			$dbhost = rtrim(fgets(STDIN));
		}

		$dbname = '';
		while($dbname == ''){
			echo "Database name: ";
			$dbname = rtrim(fgets(STDIN));
		}

		$dbuser = '';
		while($dbuser == ''){
			echo "Username: ";
			$dbuser = rtrim(fgets(STDIN));
		}

		$dbpassword = '';
		while($dbpassword == ''){
			echo "Password: ";
			$dbpassword = rtrim(fgets(STDIN));
		}

		echo "Testing connection...\n";

		try{
			$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
		}
		catch(\ErrorException $e){
			throw new MysqlException("Failed to connect to MySQL: {$e->getMessage()}");
		}
		echo "Connection working, creating .env file...\n";
		$env = file_get_contents('.env.example');
		$env = explode("\n", $env);
		$env = array_map(function($item) use ($url, $dbdriver, $dbhost, $dbname, $dbuser, $dbpassword){
			if(substr($item, 0, 7) == "APP_URL"){
				return "APP_URL=".$url;
			}
			elseif(substr($item, 0, 13) == "DB_CONNECTION"){
				return "DB_CONNECTION=".$dbdriver;
			}
			elseif(substr($item, 0, 7) == "DB_HOST"){
				return "DB_HOST=".$dbhost;
			}
			elseif(substr($item, 0, 11) == "DB_DATABASE"){
				return "DB_DATABASE=".$dbname;
			}
			elseif(substr($item, 0, 11) == "DB_USERNAME"){
				return "DB_USERNAME=".$dbuser;
			}
			elseif(substr($item, 0, 11) == "DB_PASSWORD"){
				return "DB_PASSWORD=".$dbuser;
			}
			return $item;
		}, $env);
		
		file_put_contents('.env', implode("\n", $env));

		try{
			echo "migrating modules...\n";
			exec('./artisan module:migrate', $output);

			echo "Seeding modules...\n";
			exec('./artisan module:seed', $output);

			echo "Merging package.json...\n";
			exec('./artisan core:merge-packages higher', $output);

			echo "Installing node modules...\n";
			exec('npm install', $output);

			echo "Compiling assets...\n";
			exec('npm run dev', $output);

			echo "Creating key...\n";
			exec('./artisan key:generate', $output);

			echo "Publishing configuration...\n";
			exec('./artisan module:publish-config', $output);
		}
		catch(\Exception $e){
			echo "ERROR WHILE INSTALLING : {$e->getMessage()}\n";
			echo "OUTPUT :\n";
			echo implode("\n", $output);
			unlink(".env");
		}

		echo "Installation complete !\n";
	}
}