server {
	listen 80;
	server_name website.com www.website.com;
	root /var/www/website.com/public;
	index index.php index.html;

	include templates/non_slash;
	include templates/semantic_url;
	include templates/xss_xsrf;
	include templates/favicon;
	include templates/cache_static;
	include templates/no_www;
	include templates/security;
	include templates/protect_system_files;	
	include templates/php;
}